@extends('layouts.app')
@php
use App\Helpers\DateHelper;
@endphp

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">@lang('expenses.expenses')</h5>
                        <div>
                            <span class="badge bg-info me-2">
                                @lang('expenses.try_rate'): <span id="tryRate">1</span>
                            </span>
                            <a href="{{ route('expense_groups.index') }}" class="btn btn-secondary btn-sm me-2">
                                <i class="fas fa-layer-group"></i> @lang('expenses.expense_groups')
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createExpenseModal">
                                <i class="fas fa-plus"></i> @lang('expenses.new_expense')
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('expenses.date')</th>
                                    <th>@lang('expenses.amount')</th>
                                    <th>@lang('expenses.description')</th>
                                    <th>@lang('expenses.group')</th>
                                    <th>@lang('expenses.party')</th>
                                    <th>@lang('general.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ DateHelper::format($expense->date) }}</td>
                                    <td>{{ number_format($expense->amount, 2) }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ $expense->group->name ?? '-' }}</td>
                                    <td>{{ $expense->party->name ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{ $expense->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-confirm="@lang('expenses.confirm_delete')" onclick="return confirm(this.dataset.confirm)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">@lang('expenses.no_expenses')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Expense Modal -->
<div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" id="createExpenseForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createExpenseModalLabel">@lang('expenses.new_expense')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Placeholder for displaying validation errors --}}
                    <div id="createExpenseErrors" class="alert alert-danger" style="display: none;">
                        <ul class="mb-0"></ul>
                    </div>

                    @if($errors->any()) {{-- Keep this for non-AJAX fallback if needed, but hide it initially --}}
                        <div class="alert alert-danger general-errors" style="display: none;">
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
                            <div class="mb-3">
                                <label for="date" class="form-label">@lang('expenses.date')</label>
                                <input type="text" class="form-control persian-date @error('date') is-invalid @enderror" id="date" name="date" autocomplete="off" required>
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
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
                            <div class="mb-3">
                                <label for="petty_cash_box_id" class="form-label">@lang('expenses.petty_cash_box')</label>
                                <select class="form-select @error('petty_cash_box_id') is-invalid @enderror" id="petty_cash_box_id" name="petty_cash_box_id" required>
                                    <option value="">@lang('expenses.select_petty_cash_box')</option>
                                    @foreach($boxes as $box)
                                        <option value="{{ $box->id }}">{{ $box->name }}</option>
                                    @endforeach
                                </select>
                                @error('petty_cash_box_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="party_id" class="form-label">@lang('expenses.party')</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createPartyModal">
                                        <i class="fas fa-plus"></i> @lang('expenses.add_party')
                                    </button>
                                </div>
                                <select class="form-select @error('party_id') is-invalid @enderror" id="party_id" name="party_id">
                                    <option value="">@lang('expenses.select_party')</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}">{{ $party->name }}</option>
                                    @endforeach
                                </select>
                                @error('party_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">@lang('expenses.description')</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="receipt_image" class="form-label">@lang('expenses.receipt_image')</label>
                                <input type="file" class="form-control @error('receipt_image') is-invalid @enderror" id="receipt_image" name="receipt_image">
                                @error('receipt_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="group_id" class="form-label">@lang('expenses.group')</label>
                                <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id">
                                    <option value="">@lang('expenses.select_group')</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                @error('group_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="currency" class="form-label">@lang('expenses.currency')</label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                    <option value="IRR">IRR</option>
                                    <option value="TRY">TRY</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="rate" class="form-label">@lang('expenses.rate')</label>
                                <input type="text" class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" required>
                                @error('rate')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">@lang('expenses.amount')</label>
                                <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" required>
                                @error('amount')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="irr_amount" class="form-label">@lang('expenses.irr_amount')</label>
                                <input type="text" class="form-control @error('irr_amount') is-invalid @enderror" id="irr_amount" name="irr_amount" readonly required>
                                @error('irr_amount')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-primary" id="createExpenseSubmitBtn">@lang('common.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Party Modal -->
<div class="modal fade" id="createPartyModal" tabindex="-1" aria-labelledby="createPartyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('parties.store') }}" method="POST" id="createPartyForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPartyModalLabel">@lang('expenses.add_party')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="party_name" class="form-label">@lang('expenses.party_name')</label>
                        <input type="text" class="form-control" id="party_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('general.cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('general.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Expense Modals -->
@foreach($expenses as $expense)
<div class="modal fade" id="editExpenseModal{{ $expense->id }}" tabindex="-1" aria-labelledby="editExpenseModalLabel{{ $expense->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('expenses.update', $expense) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseModalLabel{{ $expense->id }}">@lang('expenses.edit_expense')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date{{ $expense->id }}" class="form-label">@lang('expenses.date')</label>
                        <input type="text" class="form-control persian-date" id="date{{ $expense->id }}" name="date" value="{{ DateHelper::format($expense->date, 'Y-m-d') }}" autocomplete="off" required>
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
                    <div class="mb-3">
                        <label for="amount{{ $expense->id }}" class="form-label">@lang('expenses.amount')</label>
                        <input type="number" step="0.01" class="form-control" id="amount{{ $expense->id }}" name="amount" value="{{ $expense->amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description{{ $expense->id }}" class="form-label">@lang('expenses.description')</label>
                        <textarea class="form-control" id="description{{ $expense->id }}" name="description" rows="3">{{ $expense->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="group_id{{ $expense->id }}" class="form-label">@lang('expenses.group')</label>
                        <select class="form-select" id="group_id{{ $expense->id }}" name="group_id">
                            <option value="">@lang('expenses.select_group')</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $expense->group_id == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="party_id{{ $expense->id }}" class="form-label">@lang('expenses.party')</label>
                        <select class="form-select" id="party_id{{ $expense->id }}" name="party_id">
                            <option value="">@lang('expenses.select_party')</option>
                            @foreach($parties as $party)
                            <option value="{{ $party->id }}" {{ $expense->party_id == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('general.cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('general.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize datepicker is now handled in app.blade.php
        
        const currentLocale = "{{ app()->getLocale() }}";
        const dateInputs = document.querySelectorAll('.persian-date');
        
        // Set today's date as default if the field is empty
        dateInputs.forEach(input => {
            if (!input.value) {
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
                        input.value = `${day}.${month}.${year}`;
                    } else {
                        // Default format: YYYY-MM-DD
                        input.value = today.toISOString().split('T')[0];
                    }
                }
            }
        });

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
        const amountInput = document.getElementById('amount');
        const rateInput = document.getElementById('rate');
        const irrAmountInput = document.getElementById('irr_amount');
        const currencySelect = document.getElementById('currency');

        // Fetch TRY rate from settings
        let tryRate = 1;
        fetch('/settings/try-rate')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                tryRate = data.rate;
                if (currencySelect.value === 'TRY') {
                    rateInput.value = formatNumber(tryRate, 0);
                    calculateIRRAmount();
                }
            })
            .catch(error => {
                console.error('Error fetching TRY rate:', error);
                const tryRateElements = document.querySelectorAll('#tryRate');
                tryRateElements.forEach(element => {
                    element.textContent = '1';
                });
            });

        function calculateIRRAmount() {
            const amount = parseFloat(amountInput.value.replace(/,/g, '')) || 0;
            const rate = parseFloat(rateInput.value.replace(/,/g, '')) || 1;
            const irrAmount = amount * rate;
            irrAmountInput.value = formatNumber(irrAmount, 0);
        }

        amountInput.addEventListener('input', function() {
            const value = this.value.replace(/,/g, '');
            if (!isNaN(value)) {
                this.value = formatNumber(value);
            }
            calculateIRRAmount();
        });

        rateInput.addEventListener('input', function() {
            const value = this.value.replace(/,/g, '');
            if (!isNaN(value)) {
                this.value = formatNumber(value, 0);
            }
            calculateIRRAmount();
        });

        currencySelect.addEventListener('change', function() {
            if (this.value === 'IRR') {
                rateInput.value = '1';
                rateInput.readOnly = true;
            } else if (this.value === 'TRY') {
                rateInput.value = formatNumber(tryRate, 0);
                rateInput.readOnly = false;
            }
            calculateIRRAmount();
        });

        // Initialize rate input based on currency
        if (currencySelect.value === 'IRR') {
            rateInput.readOnly = true;
        }

        // Get TRY rate for display
        fetch('/settings/try-rate')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const tryRateElements = document.querySelectorAll('#tryRate');
                tryRateElements.forEach(element => {
                    element.textContent = formatNumber(data.rate, 0);
                });
            })
            .catch(error => {
                console.error('Error fetching TRY rate:', error);
                const tryRateElements = document.querySelectorAll('#tryRate');
                tryRateElements.forEach(element => {
                    element.textContent = '1';
                });
            });

        // Handle party creation
        const createPartyForm = document.getElementById('createPartyForm');
        if (createPartyForm) {
            createPartyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add new party to select
                        const partySelect = document.getElementById('party_id');
                        const option = new Option(data.party.name, data.party.id);
                        partySelect.add(option);
                        partySelect.value = data.party.id;
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createPartyModal'));
                        modal.hide();
                    }
                });
            });
        }

        const createExpenseForm = document.getElementById('createExpenseForm');
        const createExpenseSubmitBtn = document.getElementById('createExpenseSubmitBtn');
        const createExpenseErrorsDiv = document.getElementById('createExpenseErrors');
        const createExpenseErrorsList = createExpenseErrorsDiv.querySelector('ul');
        const createExpenseModalElement = document.getElementById('createExpenseModal');
        const createExpenseModal = bootstrap.Modal.getOrCreateInstance(createExpenseModalElement);

        if (createExpenseForm) {
            createExpenseForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                // Show loading state on button
                createExpenseSubmitBtn.disabled = true;
                createExpenseSubmitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> @lang('common.saving')...`;

                // Clear previous errors
                createExpenseErrorsList.innerHTML = '';
                createExpenseErrorsDiv.style.display = 'none';
                // Clear previous validation styles
                createExpenseForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                createExpenseForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');


                const formData = new FormData(createExpenseForm);
                const url = createExpenseForm.action;

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json' // Important: Tell Laravel we want JSON back
                    }
                })
                .then(response => {
                     if (!response.ok) {
                        // Handle non-2xx responses (like 422 validation errors)
                        if (response.status === 422) {
                            return response.json().then(data => {
                               throw { validationErrors: data.errors }; // Throw error with validation messages
                            });
                        }
                        // Handle other errors (e.g., 500)
                        return response.text().then(text => { // Get response text for debugging
                            throw new Error(`HTTP error! status: ${response.status}, message: ${text}`);
                        });
                     }
                     return response.json(); // Parse JSON for successful responses
                })
                .then(data => {
                    // Handle successful response
                    createExpenseModal.hide();
                    createExpenseForm.reset(); // Clear the form
                    // Display success message (using Toastr if available)
                    if (typeof toastr !== 'undefined') {
                         toastr.success(data.message || '@lang("expenses.expense_created")');
                    } else {
                        alert(data.message || '@lang("expenses.expense_created")');
                    }
                     // Optional: Reload the page or update the table dynamically
                    window.location.reload();
                })
                .catch(error => {
                    // Handle errors (including validation errors thrown above)
                    if (error.validationErrors) {
                        createExpenseErrorsDiv.style.display = 'block';
                        createExpenseErrorsList.innerHTML = ''; // Clear previous errors
                        for (const field in error.validationErrors) {
                            error.validationErrors[field].forEach(message => {
                                const li = document.createElement('li');
                                li.textContent = message;
                                createExpenseErrorsList.appendChild(li);
                            });
                             // Add is-invalid class to the corresponding input field
                             const inputElement = createExpenseForm.querySelector(`[name="${field}"]`);
                             if (inputElement) {
                                 inputElement.classList.add('is-invalid');
                                 // Optionally add error message below the field if structure allows
                                 let feedbackElement = inputElement.parentElement.querySelector('.invalid-feedback');
                                 if (feedbackElement) {
                                     feedbackElement.textContent = error.validationErrors[field][0]; // Show first error
                                 }
                             }
                        }
                    } else {
                        // Handle network errors or other exceptions
                        console.error('Error submitting form:', error);
                        createExpenseErrorsDiv.style.display = 'block';
                        createExpenseErrorsList.innerHTML = '<li>@lang("common.error_occurred")</li>';
                         if (typeof toastr !== 'undefined') {
                             toastr.error('@lang("common.error_occurred")');
                         }
                    }
                })
                .finally(() => {
                    // Reset button state
                    createExpenseSubmitBtn.disabled = false;
                    createExpenseSubmitBtn.innerHTML = `@lang('common.save')`;
                });
            });
        }

         // Clear errors when modal is closed
         createExpenseModalElement.addEventListener('hidden.bs.modal', function (event) {
             createExpenseErrorsList.innerHTML = '';
             createExpenseErrorsDiv.style.display = 'none';
             createExpenseForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
             createExpenseForm.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
             // Optionally reset the form completely
             // createExpenseForm.reset();
         });
    });
</script>
@endsection