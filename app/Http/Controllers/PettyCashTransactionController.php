<?php

namespace App\Http\Controllers;

use App\Models\PettyCashBox;
use App\Models\PettyCashTransaction;
use App\Models\Setting;
use App\Helpers\DateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
use App\Models\Party;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class PettyCashTransactionController extends Controller
{
    public function index(PettyCashBox $box)
    {
        $transactions = $box->transactions()
            ->with('party')
            ->latest()
            ->paginate(25);

        $parties = Party::all();
        $boxes = PettyCashBox::all();

        return view('petty-cash.transactions.index', compact('box', 'transactions', 'parties', 'boxes'));
    }

    public function create(Request $request)
    {
        $box = PettyCashBox::findOrFail($request->box);
        return view('petty-cash.transactions.create', compact('box'));
    }

    public function store(Request $request)
    {
        try {

            // Parse the transaction date using DateHelper
            $date = self::convertPersianNumbers($request->transaction_date);
            $parsedDate = DateHelper::parse($date);
            if (!$parsedDate) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['transaction_date' => __('Invalid date format.')]);
            }
            $request->merge(['transaction_date' => $parsedDate->format('Y-m-d')]);

            // Clean numeric fields before validation
            $request->merge([
                'amount' => str_replace(',', '', $request->input('amount')),
                'rate' => str_replace(',', '', $request->input('rate')),
                'irr_amount' => str_replace(',', '', $request->input('irr_amount')),
            ]);

            Log::info('Merged values', [
                'amount' => $request->input('amount'),
                'rate' => $request->input('rate'),
                'irr_amount' => $request->input('irr_amount'),
                'party_id' => $request->input('party_id'),
                'petty_cash_box_id' => $request->input('petty_cash_box_id'),
                'receipt_image' => $request->file('receipt_image'),
            ]);

            $validated = $request->validate([
                'petty_cash_box_id' => 'required|exists:petty_cash_boxes,id',
                'transaction_date' => 'required|date',
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|in:TRY,IRR',
                'rate' => 'required|numeric|min:0',
                'irr_amount' => 'required|numeric|min:0',
                'party_id' => 'required|exists:parties,id',
                'description' => 'nullable|string',
                'receipt_image' => 'nullable|mimes:jpg,jpeg,png,gif,pdf|max:5120',
            ]);

            Log::info('Validated data before create:', $validated);

            if ($request->hasFile('receipt_image')) {
                $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
            }

            $transaction = PettyCashTransaction::create($validated);

            Log::info('Created transaction:', $transaction->toArray());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Transaction created successfully.'),
                    'transaction' => $transaction
                ]);
            }

            return redirect()->route('petty-cash.transactions.index')
                ->with('success', __('Transaction created successfully.'));
        } catch (ValidationException $e) {
            Log::error('Validation errors:', $e->errors());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Transaction creation failed: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to create transaction. Please try again.'),
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('Failed to create transaction. Please try again.')]);
        }
    }

    public function allTransactions()
    {
        $transactions = PettyCashTransaction::with(['party', 'pettyCashBox'])
            ->latest()
            ->paginate(25);

        $parties = Party::all();
        $boxes = PettyCashBox::all();
        return view('petty-cash.transactions.index', compact('transactions', 'parties', 'boxes'));
    }

    public static function convertPersianNumbers($string)
    {
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $latin = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($persian, $latin, $string);
    }
}
