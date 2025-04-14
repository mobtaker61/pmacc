@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">@lang('all.petty_cash.transactions.create')</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('petty-cash.transactions.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="transaction_date">@lang('all.petty_cash.transactions.transaction_date')</label>
                                    <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                    @error('transaction_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="type">@lang('all.petty_cash.transactions.type')</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>@lang('all.petty_cash.transactions.income')</option>
                                        <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>@lang('all.petty_cash.transactions.expense')</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="payer_receiver">@lang('all.petty_cash.transactions.payer_receiver')</label>
                                    <input type="text" class="form-control @error('payer_receiver') is-invalid @enderror" id="payer_receiver" name="payer_receiver" value="{{ old('payer_receiver') }}" required>
                                    @error('payer_receiver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="amount">@lang('all.petty_cash.transactions.amount')</label>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required step="0.01">
                                    @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="currency">@lang('all.petty_cash.transactions.currency')</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        <option value="IRR" {{ old('currency') == 'IRR' ? 'selected' : '' }}>{{ __('Iranian Rial') }}</option>
                                        <option value="TRY" {{ old('currency') == 'TRY' ? 'selected' : '' }}>{{ __('Turkish Lira') }}</option>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>{{ __('US Dollar') }}</option>
                                    </select>
                                    @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="irr_amount">@lang('all.petty_cash.transactions.irr_amount')</label>
                                    <input type="number" class="form-control @error('irr_amount') is-invalid @enderror" id="irr_amount" name="irr_amount" value="{{ old('irr_amount') }}" required step="1">
                                    @error('irr_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="description">@lang('all.petty_cash.transactions.description')</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="receipt_image">@lang('all.petty_cash.transactions.receipt_image')</label>
                                    <div class="input-group">
                                        <input type="file" class="form-control @error('receipt_image') is-invalid @enderror" id="receipt_image" name="receipt_image">
                                        <label class="custom-file-label" for="receipt_image">@lang('all.app.choose_file')</label>
                                        @error('receipt_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="fas fa-save"></i> @lang('all.app.save')
                            </button>
                            <a href="{{ route('petty-cash.transactions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> @lang('all.app.cancel')
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 