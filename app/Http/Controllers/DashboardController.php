<?php

namespace App\Http\Controllers;

use App\Models\PettyCashBox;
use App\Models\Party;
use App\Models\Expense;
use App\Models\PettyCashTransaction;
use App\Models\ExpenseGroup;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $boxes = PettyCashBox::all();

        // مجموع واریزی به صندوق (تنخواه)
        $totalIncomeToBox = PettyCashTransaction::where('type', 'income')->sum('irr_amount');
        // مجموع هزینه ها
        $totalExpenses = Expense::sum('irr_amount');
        // مانده صندوق = واریزی به صندوق - مجموع هزینه ها
        $pettyCashBalance = $totalIncomeToBox - $totalExpenses;

        // KPI data
        $kpiData = [
            'pettyCashBalance' => $pettyCashBalance,
            'expensesLast30Days' => Expense::where('date', '>=', Carbon::now()->subDays(30))->sum('irr_amount'),
            'totalIncomeToBox' => $totalIncomeToBox,
            'activePartiesCount' => Party::count(),
        ];

        // چارت روند هزینه ها (۳۰ روز اخیر)
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $dailyExpenses = Expense::select(
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(irr_amount) as total')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->all();
        $trendLabels = [];
        $trendExpenseData = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayStr = $date->toDateString();
            $trendLabels[] = $date->format('M d');
            $trendExpenseData[] = $dailyExpenses[$dayStr] ?? 0;
        }
        $expenseTrendData = [
            'labels' => $trendLabels,
            'expense' => $trendExpenseData,
        ];

        // چارت هزینه ها براساس گروه
        $expensesByGroup = ExpenseGroup::with('expenses')
            ->get()
            ->mapWithKeys(function ($group) {
                return [$group->name => $group->expenses->sum('irr_amount')];
            })
            ->toArray();
        $chartData = [
            'expensesByGroup' => $expensesByGroup,
            'expenseTrendData' => $expenseTrendData,
        ];

        // Parties logic unchanged
        $parties = Party::withSum(['expenses as expense_payments'], 'irr_amount')
            ->withSum(['transactions as transaction_payments' => function ($query) {
                $query->where('type', 'payment');
            }], 'irr_amount')
            ->withSum(['transactions as total_receipts' => function ($query) {
                $query->where('type', 'receipt');
            }], 'irr_amount')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($party) {
                $party->expense_payments = $party->expense_payments ?? 0;
                $party->transaction_payments = $party->transaction_payments ?? 0;
                $party->total_payments = $party->expense_payments + $party->transaction_payments;
                $party->total_receipts = $party->total_receipts ?? 0;
                $party->balance = $party->total_receipts - $party->total_payments;
                return $party;
            });

        return view('dashboard', compact('boxes', 'parties', 'kpiData', 'chartData'));
    }
} 