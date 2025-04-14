@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">@lang('parties.parties')</h5>
                    <div>
                        <a href="{{ route('party_groups.index') }}" class="btn btn-info me-2">
                            <i class="fas fa-layer-group"></i> @lang('parties.groups')
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPartyModal">
                            <i class="fas fa-plus"></i> @lang('parties.new_party')
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('parties.group')</th>
                                    <th>@lang('parties.name')</th>
                                    <th>@lang('parties.company')</th>
                                    <th>@lang('parties.total_payments')</th>
                                    <th>@lang('parties.total_receipts')</th>
                                    <th>@lang('parties.balance')</th>
                                    <th>@lang('parties.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($parties as $party)
                                <tr>
                                    <td>{{ $party->partyGroup?->name ?? '-' }}</td>
                                    <td>{{ $party->first_name }} {{ $party->last_name }}</td>
                                    <td>{{ $party->company_name ?? '-' }}</td>
                                    <td class="text-danger">{{ number_format($party->total_payments) }}</td>
                                    <td class="text-success">{{ number_format($party->total_receipts) }}</td>
                                    <td class="{{ $party->balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($party->balance) }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#showPartyModal{{ $party->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editPartyModal{{ $party->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('parties.destroy', $party) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" data-confirm="@lang('parties.confirm_delete')" onclick="return confirm(this.dataset.confirm)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">@lang('parties.no_parties')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $parties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Party Modal -->
<div class="modal fade" id="createPartyModal" tabindex="-1" aria-labelledby="createPartyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPartyModalLabel">@lang('parties.create_party')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('parties.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="party_group_id" class="form-label">@lang('parties.group')</label>
                                <select class="form-select" id="party_group_id" name="party_group_id">
                                    <option value="">@lang('parties.select_group')</option>
                                    @foreach($partyGroups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">@lang('parties.first_name')</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">@lang('parties.last_name')</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="company_name" class="form-label">@lang('parties.company_name')</label>
                                <input type="text" class="form-control" id="company_name" name="company_name">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">@lang('parties.description')</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="phone" class="form-label">@lang('parties.phone')</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label">@lang('parties.mobile')</label>
                                <input type="text" class="form-control" id="mobile" name="mobile">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('parties.email')</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">@lang('parties.address')</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('parties.cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('parties.create')</button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>

@foreach($parties as $party)
<!-- Show Party Modal -->
<div class="modal fade" id="showPartyModal{{ $party->id }}" tabindex="-1" aria-labelledby="showPartyModalLabel{{ $party->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showPartyModalLabel{{ $party->id }}">@lang('parties.party_details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.group')</label>
                    <p>{{ $party->partyGroup?->name ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.first_name')</label>
                    <p>{{ $party->first_name }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.last_name')</label>
                    <p>{{ $party->last_name }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.company_name')</label>
                    <p>{{ $party->company_name ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.phone')</label>
                    <p>{{ $party->phone ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.mobile')</label>
                    <p>{{ $party->mobile ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.email')</label>
                    <p>{{ $party->email ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.address')</label>
                    <p>{{ $party->address ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.description')</label>
                    <p>{{ $party->description ?? '-' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.total_payments')</label>
                    <p class="text-danger">{{ number_format($party->total_payments) }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.total_receipts')</label>
                    <p class="text-success">{{ number_format($party->total_receipts) }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">@lang('parties.balance')</label>
                    <p class="{{ $party->balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($party->balance) }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('parties.back')</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Party Modal -->
<div class="modal fade" id="editPartyModal{{ $party->id }}" tabindex="-1" aria-labelledby="editPartyModalLabel{{ $party->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPartyModalLabel{{ $party->id }}">@lang('parties.edit_party')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('parties.update', $party) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label for="edit_party_group_id{{ $party->id }}" class="form-label">@lang('parties.group')</label>
                                <select class="form-select" id="edit_party_group_id{{ $party->id }}" name="party_group_id">
                                    <option value="">@lang('parties.select_group')</option>
                                    @foreach($partyGroups as $group)
                                    <option value="{{ $group->id }}" {{ $party->party_group_id == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_first_name{{ $party->id }}" class="form-label">@lang('parties.first_name')</label>
                                        <input type="text" class="form-control" id="edit_first_name{{ $party->id }}" name="first_name" value="{{ $party->first_name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_last_name{{ $party->id }}" class="form-label">@lang('parties.last_name')</label>
                                        <input type="text" class="form-control" id="edit_last_name{{ $party->id }}" name="last_name" value="{{ $party->last_name }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="edit_company_name{{ $party->id }}" class="form-label">@lang('parties.company_name')</label>
                                <input type="text" class="form-control" id="edit_company_name{{ $party->id }}" name="company_name" value="{{ $party->company_name }}">
                            </div>
                            <div class="mb-3">
                                <label for="edit_description{{ $party->id }}" class="form-label">@lang('parties.description')</label>
                                <textarea class="form-control" id="edit_description{{ $party->id }}" name="description" rows="3">{{ $party->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label for="edit_phone{{ $party->id }}" class="form-label">@lang('parties.phone')</label>
                                <input type="text" class="form-control" id="edit_phone{{ $party->id }}" name="phone" value="{{ $party->phone }}">
                            </div>
                            <div class="mb-3">
                                <label for="edit_mobile{{ $party->id }}" class="form-label">@lang('parties.mobile')</label>
                                <input type="text" class="form-control" id="edit_mobile{{ $party->id }}" name="mobile" value="{{ $party->mobile }}">
                            </div>
                            <div class="mb-3">
                                <label for="edit_email{{ $party->id }}" class="form-label">@lang('parties.email')</label>
                                <input type="email" class="form-control" id="edit_email{{ $party->id }}" name="email" value="{{ $party->email }}">
                            </div>
                            <div class="mb-3">
                                <label for="edit_address{{ $party->id }}" class="form-label">@lang('parties.address')</label>
                                <textarea class="form-control" id="edit_address{{ $party->id }}" name="address" rows="3">{{ $party->address }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('parties.cancel')</button>
                        <button type="submit" class="btn btn-primary">@lang('parties.update')</button>
                    </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection