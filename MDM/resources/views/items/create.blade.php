<x-app-layout>
    <x-slot name="header">Add Item</x-slot>

    <div class="container mt-4">
        <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label>Item Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                @error('code') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Item Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Brand</label>
                <select name="brand_id" class="form-select" required>
                    <option value="">Select Brand</option>
                    @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                    @endforeach
                </select>
                @error('brand_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Attachment (optional)</label>
                <input type="file" name="attachment" class="form-control">
                @error('attachment') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>