@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('all.petty_cash.boxes.title')</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBoxModal">
                        <i class="fas fa-plus"></i> @lang('all.petty_cash.boxes.create')
                    </button>
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
                                    <th>@lang('all.petty_cash.boxes.name')</th>
                                    <th>@lang('all.petty_cash.boxes.currency')</th>
                                    <th>@lang('all.petty_cash.boxes.current_balance')</th>
                                    <th>@lang('all.petty_cash.boxes.is_active')</th>
                                    <th>@lang('all.petty_cash.boxes.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($boxes as $box)
                                    <tr>
                                        <td>{{ $box->name }}</td>
                                        <td>{{ $box->currency }}</td>
                                        <td class="text-end">{{ number_format($box->current_balance, 0) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $box->is_active ? 'success' : 'danger' }}">
                                                @lang('all.app.' . ($box->is_active ? 'yes' : 'no'))
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editBoxModal{{ $box->id }}"
                                                        title="@lang('all.petty_cash.boxes.edit')">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route('petty-cash.transactions.index', ['box' => $box->id]) }}" 
                                                   class="btn btn-primary btn-sm" 
                                                   title="@lang('all.petty_cash.transactions.title')">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">@lang('all.app.no_data')</td>
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

<!-- Create Box Modal -->
<div class="modal fade" id="createBoxModal" tabindex="-1" aria-labelledby="createBoxModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBoxModalLabel">@lang('all.petty_cash.boxes.create')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('petty-cash.boxes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">@lang('all.petty_cash.boxes.name')</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="currency" class="form-label">@lang('all.petty_cash.boxes.currency')</label>
                        <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                            <option value="TRY" {{ old('currency') == 'TRY' ? 'selected' : '' }}>TRY</option>
                            <option value="IRR" {{ old('currency') == 'IRR' ? 'selected' : '' }}>IRR</option>
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">@lang('all.petty_cash.boxes.description')</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                @lang('all.petty_cash.boxes.is_active')
                            </label>
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

<!-- Edit Box Modals -->
@foreach($boxes as $box)
<div class="modal fade" id="editBoxModal{{ $box->id }}" tabindex="-1" aria-labelledby="editBoxModalLabel{{ $box->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBoxModalLabel{{ $box->id }}">@lang('all.petty_cash.boxes.edit')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('petty-cash.boxes.update', $box) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name{{ $box->id }}" class="form-label">@lang('all.petty_cash.boxes.name')</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name{{ $box->id }}" name="name" value="{{ old('name', $box->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="currency{{ $box->id }}" class="form-label">@lang('all.petty_cash.boxes.currency')</label>
                        <select class="form-select @error('currency') is-invalid @enderror" id="currency{{ $box->id }}" name="currency" required>
                            <option value="TRY" {{ old('currency', $box->currency) == 'TRY' ? 'selected' : '' }}>TRY</option>
                            <option value="IRR" {{ old('currency', $box->currency) == 'IRR' ? 'selected' : '' }}>IRR</option>
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description{{ $box->id }}" class="form-label">@lang('all.petty_cash.boxes.description')</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description{{ $box->id }}" name="description" rows="3">{{ old('description', $box->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active{{ $box->id }}" name="is_active" value="1" {{ old('is_active', $box->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active{{ $box->id }}">
                                @lang('all.petty_cash.boxes.is_active')
                            </label>
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
@endforeach
@endsection 