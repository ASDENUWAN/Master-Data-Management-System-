<x-app-layout>
    <x-slot name="header">Edit Item</x-slot>

    <div class="container mt-4">
        <form method="POST" action="{{ route('items.update', $item->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Item Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $item->code) }}" required>
                @error('code') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Brand</label>
                <select name="brand_id" class="form-select" required>
                    @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ $item->brand_id == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-select" required>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Change Attachment (optional)</label>
                <input type="file" name="attachment" class="form-control">
                @if ($item->attachment)
                <p class="mt-1">Current: <a href="{{ asset('storage/' . $item->attachment) }}" target="_blank">View</a></p>
                @endif
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Active" {{ old('status', $item->status) == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $item->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>