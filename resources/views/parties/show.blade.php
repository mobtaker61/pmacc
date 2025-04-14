<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Party Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $party->name }}</h3>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Phone') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $party->phone ?? '-' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $party->email ?? '-' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $party->address ?? '-' }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $party->description ?? '-' }}</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('parties.index') }}" class="btn btn-secondary mr-2">{{ __('Back') }}</a>
                        <a href="{{ route('parties.edit', $party) }}" class="btn btn-primary mr-2">{{ __('Edit') }}</a>
                        <form action="{{ route('parties.destroy', $party) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" data-confirm="{{ __('Are you sure you want to delete this party?') }}" onclick="return confirm(this.dataset.confirm)">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 