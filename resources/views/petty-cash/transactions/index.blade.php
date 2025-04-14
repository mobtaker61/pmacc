@extends('layouts.app')
@php
use App\Helpers\DateHelper;
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('all.petty_cash.transactions.title') - {{ $box->name }}</h5>
                    <div>
                        <a href="{{ route('petty-cash.boxes.index') }}" class="btn btn-info {{ app()->getLocale() === 'fa' ? 'ms-2' : 'me-2' }}">
                            <i class="fas fa-wallet"></i> @lang('all.petty_cash.boxes.title')
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                            <i class="fas fa-plus"></i> @lang('all.petty_cash.transactions.create')
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('all.petty_cash.transactions.date')</th>
                                    <th>@lang('all.petty_cash.transactions.type')</th>
                                    <th>@lang('all.petty_cash.transactions.payer_receiver')</th>
                                    <th>@lang('all.petty_cash.transactions.amount')</th>
                                    <th>@lang('all.petty_cash.transactions.irr_amount')</th>
                                    <th>@lang('all.petty_cash.transactions.description')</th>
                                    <th>@lang('all.petty_cash.transactions.receipt_image')</th>
                                    <th>@lang('all.petty_cash.transactions.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ DateHelper::format($transaction->transaction_date) }}</td>
                                    <td>
                                        @if($transaction->type === 'income')
                                        <span class="badge bg-success">@lang('all.petty_cash.transactions.income')</span>
                                        @else
                                        <span class="badge bg-danger">@lang('all.petty_cash.transactions.expense')</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->payer_receiver }}</td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ number_format($transaction->irr_amount) }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        @if($transaction->receipt_image)
                                        <a href="{{ asset('storage/' . $transaction->receipt_image) }}" data-lightbox="receipts" data-title="@lang('all.petty_cash.transactions.receipt_image')">
                                            <i class="fas fa-image text-primary" style="font-size: 1.2rem;"></i>
                                        </a>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">@lang('all.petty_cash.transactions.no_transactions')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Transaction Modal -->
<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTransactionModalLabel">@lang('all.petty_cash.transactions.create')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('petty-cash.transactions.store') }}" method="POST" enctype="multipart/form-data" id="transactionForm">
                    @csrf
                    <input type="hidden" name="petty_cash_box_id" value="{{ $box->id }}">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="transaction_date" class="form-label">@lang('all.petty_cash.transactions.date')</label>
                                        <input type="text" class="form-control persian-date" id="transaction_date" name="transaction_date" autocomplete="off" required>
                                        <small class="form-text text-muted">
                                            @if(app()->getLocale() === 'fa')
                                            @lang('Format'): 1402/01/01
                                            @elseif(app()->getLocale() === 'tr')
                                            @lang('Format'): 01.01.2023
                                            @else
                                            @lang('Format'): 2023-01-01
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">@lang('all.petty_cash.transactions.type')</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="income">@lang('all.petty_cash.transactions.income')</option>
                                            <option value="expense">@lang('all.petty_cash.transactions.expense')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">@lang('all.petty_cash.transactions.description')</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payer_receiver" class="form-label">@lang('all.petty_cash.transactions.payer_receiver')</label>
                                <input type="text" class="form-control" id="payer_receiver" name="payer_receiver" required>
                            </div>

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">@lang('all.petty_cash.transactions.amount')</label>
                                        <input type="text" class="form-control" id="amount" name="amount" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">@lang('all.petty_cash.transactions.currency')</label>
                                        <select class="form-select" id="currency" name="currency" required>
                                            <option value="IRR">IRR</option>
                                            <option value="TRY">TRY</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <label for="irr_amount" class="form-label">@lang('all.petty_cash.transactions.irr_amount')</label>
                                        <input type="text" class="form-control" id="irr_amount" name="irr_amount" readonly>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="rate" class="form-label">@lang('all.petty_cash.transactions.rate')</label>
                                        <input type="text" class="form-control" id="rate" name="rate" required value="{{ $box->currency === 'TRY' ? $box->try_rate : 1 }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="receipt_image" class="form-label">@lang('all.petty_cash.transactions.receipt_image')</label>
                                <input type="file" class="form-control" id="receipt_image" name="receipt_image" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('all.app.cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('all.app.save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datepicker is now handled in app.blade.php

        const currentLocale = "{{ app()->getLocale() }}";
        const transactionDateInput = document.getElementById('transaction_date');
        const transactionForm = document.getElementById('transactionForm');
        const currencySelect = document.getElementById('currency');
        const amountInput = document.getElementById('amount');
        const rateInput = document.getElementById('rate');
        const irrAmountInput = document.getElementById('irr_amount');

        // Set today's date as default if the field is empty
        if (transactionDateInput && !transactionDateInput.value) {
            // The datepicker will handle this automatically
            if (currentLocale === 'fa') {
                // The persian-datepicker in app.blade.php will handle this
            } else {
                // For non-Persian locales, we can set a default
                const today = new Date();
                if (currentLocale === 'tr') {
                    // Turkish format: DD.MM.YYYY
                    const day = String(today.getDate()).padStart(2, '0');
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const year = today.getFullYear();
                    transactionDateInput.value = `${day}.${month}.${year}`;
                } else {
                    // Default format: YYYY-MM-DD
                    transactionDateInput.value = today.toISOString().split('T')[0];
                }
            }
        }

        // Format number inputs
        function formatNumber(number, decimals = 0) {
            if (isNaN(number) || number === '') return '';
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 10,
                useGrouping: true
            }).format(number);
        }

        // Calculate IRR amount when amount or rate changes
        function calculateIRRAmount() {
            const amount = parseFloat(amountInput.value.replace(/,/g, '')) || 0;
            const rate = parseFloat(rateInput.value.replace(/,/g, '')) || 1;
            const irrAmount = amount * rate;
            irrAmountInput.value = formatNumber(irrAmount, 0);
        }

        // Handle amount input changes
        amountInput.addEventListener('input', function() {
            let value = this.value.replace(/,/g, '');
            if (!isNaN(value)) {
                // Format the number with 2 decimal places
                this.value = formatNumber(value, 2);
            }
            calculateIRRAmount();
        });

        // Handle rate input changes
        rateInput.addEventListener('input', function() {
            let value = this.value.replace(/,/g, '');
            if (!isNaN(value)) {
                // Format the number with 0 decimal places
                this.value = formatNumber(value, 0);
            }
            calculateIRRAmount();
        });

        // Handle currency changes
        currencySelect.addEventListener('change', function() {
            if (this.value === 'IRR') {
                rateInput.value = '1';
                rateInput.readOnly = true;
                calculateIRRAmount();
            } else if (this.value === 'TRY') {
                // Fetch TRY rate from settings
                fetch('/settings/try-rate')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        rateInput.value = formatNumber(data.rate, 0);
                        rateInput.readOnly = false;
                        calculateIRRAmount();
                    })
                    .catch(error => {
                        console.error('Error fetching TRY rate:', error);
                        rateInput.value = '1';
                        rateInput.readOnly = false;
                        calculateIRRAmount();
                    });
            }
        });

        // Initialize rate input based on currency
        if (currencySelect.value === 'IRR') {
            rateInput.readOnly = true;
        }

        // Initial calculation
        calculateIRRAmount();

        // Handle form submission
        if (transactionForm) {
            transactionForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> @lang('common.saving')...`;

                // Clear previous errors
                const errorDiv = this.querySelector('.alert-danger');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                    errorDiv.querySelector('ul').innerHTML = '';
                }

                // Clear previous validation styles
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

                const formData = new FormData(this);
                
                // Convert Persian date to Gregorian if needed
                const dateInput = this.querySelector('#transaction_date');
                if (dateInput && currentLocale === 'fa') {
                    const persianDate = dateInput.value;
                    // Convert Persian numbers to English
                    const englishDate = persianDate.replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
                    formData.set('transaction_date', englishDate);
                }
                
                // Log form data for debugging
                console.log('Form Data:', Object.fromEntries(formData));
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 422) {
                            return response.json().then(data => {
                                console.log('Validation Errors:', data.errors);
                                throw { validationErrors: data.errors };
                            });
                        }
                        return response.text().then(text => {
                            throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Handle successful response
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createTransactionModal'));
                    modal.hide();
                    transactionForm.reset();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || '@lang("all.petty_cash.transactions.created")');
                    } else {
                        alert(data.message || '@lang("all.petty_cash.transactions.created")');
                    }
                    
                    // Reload the page to show new data
                    window.location.reload();
                })
                .catch(error => {
                    // Handle errors
                    if (error.validationErrors) {
                        const errorDiv = transactionForm.querySelector('.alert-danger');
                        if (errorDiv) {
                            errorDiv.style.display = 'block';
                            const errorList = errorDiv.querySelector('ul');
                            errorList.innerHTML = '';
                            
                            for (const field in error.validationErrors) {
                                error.validationErrors[field].forEach(message => {
                                    const li = document.createElement('li');
                                    li.textContent = `${field}: ${message}`;
                                    errorList.appendChild(li);

                                    // Add is-invalid class to the corresponding input field
                                    const inputElement = transactionForm.querySelector(`[name="${field}"]`);
                                    if (inputElement) {
                                        inputElement.classList.add('is-invalid');
                                        // Add error message below the field
                                        let feedbackElement = inputElement.parentElement.querySelector('.invalid-feedback');
                                        if (!feedbackElement) {
                                            feedbackElement = document.createElement('div');
                                            feedbackElement.className = 'invalid-feedback';
                                            inputElement.parentElement.appendChild(feedbackElement);
                                        }
                                        feedbackElement.textContent = message;
                                    }
                                });
                            }
                        }
                    } else {
                        console.error('Error submitting form:', error);
                        if (typeof toastr !== 'undefined') {
                            toastr.error('@lang("common.error_occurred")');
                        } else {
                            alert('@lang("common.error_occurred")');
                        }
                    }
                })
                .finally(() => {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
            });
        }
    });
</script>
@endsection