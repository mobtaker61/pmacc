@extends('layouts.app')

@php
    use App\Helpers\NumberHelper; // Assuming you might have a NumberHelper for formatting
    $locale = app()->getLocale();
@endphp

@section('content')
<div class="container">

    <!-- KPIs Row -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-wallet me-2"></i>مانده صندوق</h5>
                    <p class="card-text fs-4 fw-bold">{{ NumberHelper::format($kpiData['pettyCashBalance']) }} IRR</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-arrow-trend-down me-2"></i>@lang('dashboard.expenses_last_30_days')</h5>
                    <p class="card-text fs-4 fw-bold">{{ NumberHelper::format($kpiData['expensesLast30Days']) }} IRR</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-arrow-trend-up me-2"></i>مجموع ورودی تنخواه</h5>
                    <p class="card-text fs-4 fw-bold">{{ NumberHelper::format($kpiData['totalIncomeToBox']) }} IRR</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    روند هزینه‌ها (@lang('dashboard.last_30_days'))
                </div>
                <div class="card-body">
                    <canvas id="expenseTrendChart"></canvas>
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
                                        <th>@lang('all.common.current_balance')</th>
                                        {{-- Optional: Add balance in original currency --}}
                                        {{-- <th>@lang('all.common.current_balance')</th> --}}
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
                        <a href="{{ route('parties.index') }}" class="btn btn-outline-primary btn-sm">@lang('all.common.view_all')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@php
    $expenseTrendJson = json_encode($chartData['expenseTrendData']);
    $expensesByGroupJson = json_encode($chartData['expensesByGroup']);
@endphp

{{-- Hidden divs to store JSON data --}}
<div id="expenseTrendData" style="display: none;">{!! $expenseTrendJson !!}</div>
<div id="expensesByGroupData" style="display: none;">{!! $expensesByGroupJson !!}</div>

{{-- Inject translations into JS --}}
<script>
    window.dashboardLang = {
        expense: "هزینه",
        total_expenses: "مجموع هزینه‌ها"
    };
</script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const locale = '{{ $locale }}';
    const currency = 'IRR';
    // Expense Trend Chart
    let expenseTrendData = JSON.parse(document.getElementById('expenseTrendData').textContent);
    if (!expenseTrendData || !expenseTrendData.labels || expenseTrendData.labels.length === 0) {
        expenseTrendData = { labels: ['بدون داده'], expense: [0] };
    }
    // Ensure all values are numbers
    expenseTrendData.expense = expenseTrendData.expense.map(x => Number(x));
    const ctxExpense = document.getElementById('expenseTrendChart');
    if (ctxExpense) {
        new Chart(ctxExpense.getContext('2d'), {
            type: 'line',
            data: {
                labels: expenseTrendData.labels,
                datasets: [
                    {
                        label: window.dashboardLang.expense,
                        data: expenseTrendData.expense,
                        borderColor: 'rgba(220,53,69,1)',
                        backgroundColor: 'rgba(220,53,69,0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return Number(value).toLocaleString(locale) + ' ' + currency;
                            }
                        }
                    }
                }
            }
        });
    }

    // Expenses By Group Chart
    let expensesByGroupData = JSON.parse(document.getElementById('expensesByGroupData').textContent);
    if (!expensesByGroupData || Object.keys(expensesByGroupData).length === 0) {
        expensesByGroupData = { 'بدون داده': 0 };
    }
    // Ensure all values are numbers
    expensesByGroupData = Object.fromEntries(
        Object.entries(expensesByGroupData).map(([k, v]) => [k, Number(v)])
    );
    const ctxGroup = document.getElementById('expensesByGroupChart');
    if (ctxGroup) {
        new Chart(ctxGroup.getContext('2d'), {
            type: 'bar',
            data: {
                labels: Object.keys(expensesByGroupData),
                datasets: [
                    {
                        label: window.dashboardLang.total_expenses,
                        data: Object.values(expensesByGroupData),
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return Number(value).toLocaleString(locale) + ' ' + currency;
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