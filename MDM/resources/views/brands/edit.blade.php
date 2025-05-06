<x-app-layout>
    <x-slot name="header">
        Edit Brand
    </x-slot>

    <div class="container mt-4">
        <form method="POST" action="{{ route('brands.update', $brand->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="code" class="form-label">Brand Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $brand->code) }}" required>
                @error('code') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Brand Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Active" {{ old('status', $brand->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $brand->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Brand</button>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>