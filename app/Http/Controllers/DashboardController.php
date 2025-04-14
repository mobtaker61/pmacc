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
        
        $parties = Party::withSum(['transactions as total_payments' => function ($query) {
            $query->where('type', 'payment');
        }], 'irr_amount')
        ->withSum(['transactions as total_receipts' => function ($query) {
            $query->where('type', 'receipt');
        }], 'irr_amount')
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($party) {
            $party->total_payments = $party->total_payments ?? 0;
            $party->total_receipts = $party->total_receipts ?? 0;
            $party->balance = $party->total_receipts - $party->total_payments;
            return $party;
        });

        $totalBalanceIRR = $boxes->sum('current_balance');
        $expensesLast30Days = Expense::where('date', '>=', Carbon::now()->subDays(30))->sum('irr_amount');
        $incomeLast30Days = PettyCashTransaction::where('type', 'income')
                            ->where('transaction_date', '>=', Carbon::now()->subDays(30))
                            ->sum('irr_amount');
        $activePartiesCount = Party::count();

        $expensesByGroup = Expense::join('expense_groups', 'expenses.expense_group_id', '=', 'expense_groups.id')
            ->where('expenses.date', '>=', Carbon::now()->subDays(30))
            ->select('expense_groups.name', DB::raw('SUM(expenses.irr_amount) as total'))
            ->groupBy('expense_groups.name')
            ->orderBy('total', 'desc')
            ->pluck('total', 'name')
            ->all();

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

        $dailyIncome = PettyCashTransaction::where('type', 'income')->select(
                DB::raw('DATE(transaction_date) as day'),
                DB::raw('SUM(irr_amount) as total')
            )
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->all();
            
        $trendLabels = [];
        $trendIncomeData = [];
        $trendExpenseData = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dayStr = $date->toDateString();
            $trendLabels[] = $date->format('M d');
            $trendIncomeData[] = $dailyIncome[$dayStr] ?? 0;
            $trendExpenseData[] = $dailyExpenses[$dayStr] ?? 0;
        }
        
        $incomeExpenseTrendData = [
            'labels' => $trendLabels,
            'income' => $trendIncomeData,
            'expense' => $trendExpenseData,
        ];

        $kpiData = compact('totalBalanceIRR', 'expensesLast30Days', 'incomeLast30Days', 'activePartiesCount');
        $chartData = compact('expensesByGroup', 'incomeExpenseTrendData');

        return view('dashboard', compact('boxes', 'parties', 'kpiData', 'chartData'));
    }
} 