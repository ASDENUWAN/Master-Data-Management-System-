<x-app-layout>
    <x-slot name="header">
        Add Brand
    </x-slot>

    <div class="container mt-4">
        <form method="POST" action="{{ route('brands.store') }}">
            @csrf

            <div class="mb-3">
                <label for="code" class="form-label">Brand Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                @error('code') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Brand Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">Create Brand</button>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>