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

class PettyCashTransactionController extends Controller
{
    public function index(PettyCashBox $box)
    {
        $transactions = $box->transactions()
            ->with('party')
            ->latest()
            ->paginate(10);

        $parties = Party::all();

        return view('petty-cash.transactions.index', compact('box', 'transactions', 'parties'));
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
            $parsedDate = DateHelper::parse($request->transaction_date);
            
            if (!$parsedDate) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['transaction_date' => __('Invalid date format.')]);
            }
            
            // Set the parsed date in Y-m-d format
            $request->merge(['transaction_date' => $parsedDate->format('Y-m-d')]);

            $validated = $request->validate([
                'petty_cash_box_id' => 'required|exists:petty_cash_boxes,id',
                'transaction_date' => 'required|date',
                'type' => 'required|in:income,expense',
                'payer_receiver' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|in:TRY,IRR',
                'rate' => 'required|numeric|min:0',
                'irr_amount' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'receipt_image' => 'nullable|image|max:2048'
            ]);

            // Convert formatted numbers to actual numbers
            $validated['amount'] = str_replace(',', '', $validated['amount']);
            $validated['rate'] = str_replace(',', '', $validated['rate']);
            $validated['irr_amount'] = str_replace(',', '', $validated['irr_amount']);

            if ($request->hasFile('receipt_image')) {
                $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
            }

            $transaction = PettyCashTransaction::create($validated);

            return redirect()->route('petty-cash.transactions.index')
                ->with('success', __('Transaction created successfully.'));

        } catch (\Exception $e) {
            Log::error('Transaction creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('Failed to create transaction. Please try again.')]);
        }
    }
} 