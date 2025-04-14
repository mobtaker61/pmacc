<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseGroup;
use App\Models\Party;
use App\Models\PettyCashBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    // Helper function to convert Persian/Arabic numbers to Latin
    private function convertNumbersToLatin($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $num = range(0, 9);
        $convertedPersian = str_replace($persian, $num, $string);
        return str_replace($arabic, $num, $convertedPersian);
    }

    public function index()
    {
        $expenses = Expense::with(['pettyCashBox', 'expenseGroup', 'party'])
            ->latest()
            ->paginate(10);

        $groups = ExpenseGroup::all();
        $parties = Party::all();
        $boxes = PettyCashBox::all();

        return view('expenses.index', compact('expenses', 'groups', 'parties', 'boxes'));
    }

    public function create()
    {
        $groups = ExpenseGroup::all();
        $parties = Party::all();
        $boxes = PettyCashBox::all();

        return view('expenses.create', compact('groups', 'parties', 'boxes'));
    }

    public function store(Request $request)
    {
        // Convert date numbers to Latin *before* logging or validation attempt
        $rawDateInput = $request->input('date');
        $latinDateInput = $this->convertNumbersToLatin($rawDateInput);

        // Log the raw and converted date input for debugging
        Log::info('Raw date input:', ['date' => $rawDateInput]);
        Log::info('Latin date input:', ['date' => $latinDateInput]);

        // Merge the *converted* date back into the request for validation
        $request->merge(['date' => $latinDateInput]);

        // Clean numeric inputs before validation
        $request->merge([
            'amount' => str_replace(',', '', $request->input('amount')),
            'rate' => str_replace(',', '', $request->input('rate')),
            'irr_amount' => str_replace(',', '', $request->input('irr_amount')),
        ]);

        try {
            $validated = $request->validate([
                'date' => 'required|string', // Keep as string for initial validation
                'amount' => 'required|numeric',
                'description' => 'nullable|string',
                'group_id' => 'nullable|exists:expense_groups,id',
                'party_id' => 'nullable|exists:parties,id',
                'petty_cash_box_id' => 'required|exists:petty_cash_boxes,id',
                'currency' => 'required|in:IRR,TRY',
                'rate' => 'required|numeric',
                'irr_amount' => 'required|numeric',
                'receipt_image' => 'nullable|image|max:2048'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors as JSON
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Rename group_id key to expense_group_id if it exists
        if (isset($validated['group_id'])) {
            $validated['expense_group_id'] = $validated['group_id'];
            unset($validated['group_id']);
        }

        // Attempt to convert Persian date to Gregorian AFTER initial validation
        if (app()->getLocale() === 'fa') {
            if (!empty($validated['date'])) {
                $originalDate = $validated['date']; // This is now Latin numeral date
                 try {
                    // Try parsing with Carbon first (should work now)
                    $carbonDate = \Carbon\Carbon::parse(str_replace('/', '-', $validated['date']));
                    $validated['date'] = Jalalian::fromCarbon($carbonDate)->format('Y-m-d');
                    Log::info('Date conversion successful (Carbon):', ['original' => $originalDate, 'converted' => $validated['date']]);
                 } catch (\Exception $e) {
                     Log::warning('Date conversion failed (Carbon), trying Jalalian::fromFormat:', ['original' => $originalDate, 'error' => $e->getMessage()]);
                    // Fallback to Jalalian::fromFormat (should also work now)
                    try {
                         $carbonDate = Jalalian::fromFormat('Y/m/d', $originalDate)->toCarbon();
                         $validated['date'] = $carbonDate->format('Y-m-d');
                         Log::info('Date conversion successful (Jalalian::fromFormat):', ['original' => $originalDate, 'converted' => $validated['date']]);
                    } catch (\Exception $e2) {
                         Log::error('Date conversion failed (Both methods):', ['original' => $originalDate, 'error' => $e2->getMessage()]);
                        // If both fail, return a specific validation error for the date field
                        return response()->json(['errors' => ['date' => [__('validation.date_format', ['format' => 'YYYY/MM/DD'])]]], 422);
                    }
                 }
            } else {
                 // This case should ideally be caught by 'required' rule, but handle defensively
                 Log::warning('Date field was empty after validation?');
                 return response()->json(['errors' => ['date' => [__('validation.required')]]], 422);
            }
        } else {
            // For non-Persian locales, ensure standard format if needed (optional)
            try {
                $validated['date'] = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                 Log::error('Date conversion failed (Non-FA locale):', ['date' => $validated['date'], 'error' => $e->getMessage()]);
                 return response()->json(['errors' => ['date' => [__('validation.date_format', ['format' => 'YYYY-MM-DD or similar'])]]], 422);
            }
        }


        if ($request->hasFile('receipt_image')) {
            try {
                 $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
            } catch (\Exception $e) {
                Log::error('Receipt image upload failed:', ['error' => $e->getMessage()]);
                return response()->json(['errors' => ['receipt_image' => [__('common.file_upload_error')]]], 422);
            }
        }

        try {
             $expense = Expense::create($validated);
             Log::info('Expense created successfully:', ['expense_id' => $expense->id]);
        } catch (\Exception $e) {
            Log::error('Expense creation failed:', ['error' => $e->getMessage(), 'data' => $validated]);
            return response()->json(['message' => __('common.error_occurred_saving')], 500);
        }

        // Return success response as JSON
        return response()->json(['message' => __('expenses.expense_created')]);
    }

    public function edit(Expense $expense)
    {
        $groups = ExpenseGroup::all();
        $parties = Party::all();
        $boxes = PettyCashBox::all();

        return view('expenses.edit', compact('expense', 'groups', 'parties', 'boxes'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Similar validation and update logic needed here, consider refactoring
        $validated = $request->validate([
            'date' => 'required|string', // Adjust validation as needed
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'group_id' => 'nullable|exists:expense_groups,id',
            'party_id' => 'nullable|exists:parties,id',
            'petty_cash_box_id' => 'required|exists:petty_cash_boxes,id',
            'currency' => 'required|in:IRR,TRY',
            'rate' => 'required|numeric',
        ]);

        // Date conversion logic for update (similar to store)
        if (app()->getLocale() === 'fa' && !empty($validated['date'])) {
             try {
                $carbonDate = \Carbon\Carbon::parse(str_replace('/', '-', $validated['date']));
                $validated['date'] = Jalalian::fromCarbon($carbonDate)->format('Y-m-d');
             } catch (\Exception $e) {
                try {
                    $carbonDate = Jalalian::fromFormat('Y/m/d', $validated['date'])->toCarbon();
                    $validated['date'] = $carbonDate->format('Y-m-d');
                } catch (\Exception $e2) {
                   // Handle error - perhaps redirect back with error
                   return back()->withErrors(['date' => __('validation.date_format', ['format' => 'YYYY/MM/DD'])])->withInput();
                }
             }
        } elseif (!empty($validated['date'])) {
            try {
                $validated['date'] = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                 return back()->withErrors(['date' => __('validation.date_format', ['format' => 'YYYY-MM-DD or similar'])])->withInput();
            }
        }

        // Clean numeric fields (do this *before* validation ideally, refactor needed)
        $validated['amount'] = str_replace(',', '', $request->input('amount'));
        $validated['rate'] = str_replace(',', '', $request->input('rate'));

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', __('expenses.expense_updated'));
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', __('expenses.expense_deleted'));
    }
} 