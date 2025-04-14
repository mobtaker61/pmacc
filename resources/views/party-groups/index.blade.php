@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>@lang('parties.groups')</span>
                    <div>
                        <a href="{{ route('parties.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left"></i> @lang('parties.back_to_parties')
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                            <i class="fas fa-plus"></i> @lang('parties.new_group')
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('parties.name')</th>
                                    <th>@lang('parties.description')</th>
                                    <th>@lang('parties.actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partyGroups as $group)
                                    <tr>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ $group->description ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editGroupModal{{ $group->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('party-groups.destroy', $group) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" data-confirm="@lang('parties.confirm_delete_group')" onclick="return confirm(this.dataset.confirm)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">@lang('parties.no_groups')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $partyGroups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Group Modal -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGroupModalLabel">@lang('parties.create_group')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('party_groups.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">@lang('parties.name')</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">@lang('parties.description')</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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

@foreach($partyGroups as $group)
<!-- Edit Group Modal -->
<div class="modal fade" id="editGroupModal{{ $group->id }}" tabindex="-1" aria-labelledby="editGroupModalLabel{{ $group->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editGroupModalLabel{{ $group->id }}">@lang('parties.edit_group')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('party-groups.update', $group) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name{{ $group->id }}" class="form-label">@lang('parties.name')</label>
                        <input type="text" class="form-control" id="edit_name{{ $group->id }}" name="name" value="{{ $group->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description{{ $group->id }}" class="form-label">@lang('parties.description')</label>
                        <textarea class="form-control" id="edit_description{{ $group->id }}" name="description" rows="3">{{ $group->description }}</textarea>
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