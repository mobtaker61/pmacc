@extends('layouts.app')

@php
    use App\Helpers\NumberHelper; // Assuming you might have a NumberHelper for formatting
    $locale = app()->getLocale();
@endphp

@section('content')
<div class="container">

    <!-- KPIs Row -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-wallet me-2"></i>@lang('dashboard.total_balance')</h5>
                    <p class="card-text fs-4 fw-bold">{{ NumberHelper::format($kpiData['totalBalanceIRR']) }} IRR</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-arrow-trend-down me-2"></i>@lang('dashboard.expenses_last_30_days')</h5>
                    <p class="card-text fs-4 fw-bold">{{ NumberHelper::format($kpiData['expensesLast30Days']) }} IRR</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-arrow-trend-up me-2"></i>@lang('dashboard.income_last_30_days')</h5>
                    <p class="card-text fs-4 fw-bold">{{ NumberHelper::format($kpiData['incomeLast30Days']) }} IRR</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-dark bg-warning h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>@lang('dashboard.active_parties')</h5>
                    <p class="card-text fs-4 fw-bold">{{ $kpiData['activePartiesCount'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    @lang('dashboard.income_expense_trend') (@lang('dashboard.last_30_days'))
                </div>
                <div class="card-body">
                    <canvas id="incomeExpenseTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    @lang('dashboard.expenses_by_group') (@lang('dashboard.last_30_days'))
                </div>
                <div class="card-body">
                    <canvas id="expensesByGroupChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Tables Row -->
    <div class="row">
        <!-- Petty Cash Boxes Table -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('all.petty_cash.boxes.title')</h5>
                </div>
                <div class="card-body">
                    @if($boxes->isEmpty())
                        <div class="alert alert-info">
                            @lang('all.app.no_data')
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>@lang('all.petty_cash.boxes.name')</th>
                                        <th>@lang('all.petty_cash.boxes.currency')</th>
                                        <th>@lang('all.petty_cash.boxes.current_balance_irr')</th>
                                        {{-- Optional: Add balance in original currency --}}
                                        {{-- <th>@lang('all.petty_cash.boxes.current_balance')</th> --}}
                                        <th>@lang('all.app.actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boxes as $box)
                                        <tr>
                                            <td>{{ $box->name }}</td>
                                            <td>{{ $box->currency }}</td>
                                            <td>{{ NumberHelper::format($box->current_balance) }} IRR</td>
                                            {{-- Optional: Display balance in original currency --}}
                                            {{-- <td>
                                                @if($box->currency != 'IRR' && $box->getRateForCurrency() > 0)
                                                    {{ NumberHelper::formatCurrency($box->current_balance / $box->getRateForCurrency(), $box->currency, $locale) }}
                                                @else
                                                    -
                                                @endif
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('petty-cash.transactions.index', ['box' => $box->id]) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Parties Table -->
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">@lang('parties.recent_parties')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>@lang('parties.name')</th>
                                    {{-- <th>@lang('parties.total_payments')</th>
                                    <th>@lang('parties.total_receipts')</th> --}}
                                    <th>@lang('parties.balance') (IRR)</th>
                                    <th>@lang('parties.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($parties as $party)
                                    <tr>
                                        <td>{{ $party->name }}</td>
                                        {{-- <td class="text-danger">{{ NumberHelper::formatCurrency($party->total_payments, 'IRR', $locale) }}</td>
                                        <td class="text-success">{{ NumberHelper::formatCurrency($party->total_receipts, 'IRR', $locale) }}</td> --}}
                                        <td class="{{ $party->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ NumberHelper::format($party->balance) }} IRR
                                        </td>
                                        <td>
                                            <a href="{{ route('parties.show', $party) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">@lang('parties.no_parties')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('parties.index') }}" class="btn btn-outline-primary btn-sm">@lang('parties.view_all')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@php
    $incomeExpenseTrendJson = json_encode($chartData['incomeExpenseTrendData']);
    $expensesByGroupJson = json_encode($chartData['expensesByGroup']);
@endphp

{{-- Hidden divs to store JSON data --}}
<div id="incomeExpenseTrendData" style="display: none;">{!! $incomeExpenseTrendJson !!}</div>
<div id="expensesByGroupData" style="display: none;">{!! $expensesByGroupJson !!}</div>

{{-- Inject translations into JS --}}
<script>
    window.dashboardLang = {
        income: "@lang('dashboard.income')",
        expense: "@lang('dashboard.expense')",
        total_expenses: "@lang('dashboard.total_expenses')"
    };
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const locale = '{{ $locale }}';
    const currency = 'IRR';

    // Function to safely parse JSON from hidden divs
    const getJsonData = (elementId) => {
        const element = document.getElementById(elementId);
        if (!element) return null;
        try {
            return JSON.parse(element.textContent);
        } catch (e) {
            console.error('Error parsing JSON data from:', elementId, e);
            return null;
        }
    };

    // Retrieve chart data
    const incomeExpenseData = getJsonData('incomeExpenseTrendData');
    const expensesByGroupData = getJsonData('expensesByGroupData');

    const formatChartCurrency = (value) => {
        try {
            return new Intl.NumberFormat(locale + '-u-nu-latn', {
                 style: 'decimal',
                 minimumFractionDigits: 0,
                 maximumFractionDigits: 0 
            }).format(value);
        } catch (e) {
             console.error("Currency formatting error:", e);
             return value;
        }
    };

    // 1. Income vs Expense Trend Chart
    const incomeExpenseCtx = document.getElementById('incomeExpenseTrendChart');
    if (incomeExpenseCtx && incomeExpenseData) {
        new Chart(incomeExpenseCtx, {
            type: 'line',
            data: {
                labels: incomeExpenseData.labels,
                datasets: [
                    {
                        label: window.dashboardLang.income || 'Income',
                        data: incomeExpenseData.income,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true,
                    },
                    {
                        label: window.dashboardLang.expense || 'Expense',
                        data: incomeExpenseData.expense,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                             callback: function(value, index, values) {
                                 return formatChartCurrency(value);
                             }
                         }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatChartCurrency(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Expenses by Group Chart
    const expensesByGroupCtx = document.getElementById('expensesByGroupChart');
    if (expensesByGroupCtx && expensesByGroupData) {
        const groupLabels = Object.keys(expensesByGroupData);
        const groupTotals = Object.values(expensesByGroupData);
        
        const backgroundColors = groupLabels.map(() => 
            `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`
        );
        const borderColors = backgroundColors.map(color => color.replace('0.7', '1'));

        new Chart(expensesByGroupCtx, {
            type: 'doughnut',
            data: {
                labels: groupLabels,
                datasets: [{
                    label: window.dashboardLang.total_expenses || 'Total Expenses',
                    data: groupTotals,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                     label += formatChartCurrency(context.parsed);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush