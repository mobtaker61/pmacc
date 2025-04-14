@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ __('all.settings.title') }}</h3>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('all.settings.key') }}</th>
                                    <th>{{ __('all.settings.value') }}</th>
                                    <th>{{ __('all.settings.description') }}</th>
                                    <th>{{ __('all.settings.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($settings as $setting)
                                    <tr>
                                        <td>{{ $setting->key }}</td>
                                        <td>{{ (string) $setting->value }}</td>
                                        <td>{{ $setting->description }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editSettingModal{{ $setting->id }}">
                                                <i class="fas fa-edit"></i> {{ __('all.settings.edit') }}
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Edit Setting Modal -->
                                    <div class="modal fade" id="editSettingModal{{ $setting->id }}" tabindex="-1" aria-labelledby="editSettingModalLabel{{ $setting->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editSettingModalLabel{{ $setting->id }}">{{ __('all.settings.edit_title') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('settings.update', $setting) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="key{{ $setting->id }}" class="form-label">{{ __('all.settings.key') }}</label>
                                                            <input type="text" class="form-control" id="key{{ $setting->id }}" value="{{ $setting->key }}" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="value{{ $setting->id }}" class="form-label">{{ __('all.settings.value') }}</label>
                                                            <input type="text" class="form-control @error('value') is-invalid @enderror" id="value{{ $setting->id }}" name="value" value="{{ old('value', (string) $setting->value) }}" required>
                                                            @error('value')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="description{{ $setting->id }}" class="form-label">{{ __('all.settings.description') }}</label>
                                                            <input type="text" class="form-control" id="description{{ $setting->id }}" value="{{ $setting->description }}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times"></i> {{ __('all.settings.cancel') }}
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save"></i> {{ __('all.settings.update') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 