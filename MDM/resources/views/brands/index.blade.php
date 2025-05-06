<x-app-layout>
    <x-slot name="header">
        Brands
    </x-slot>

    <div class="container mt-4">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
        @endif


        <a href="{{ route('brands.create') }}" class="btn btn-primary mb-3">Add Brand</a>
        <form method="GET" action="{{ route('brands.index') }}" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search code or name"
                    value="{{ request('search') }}">
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">-- Status --</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            @if (Auth::user()->is_admin)
            <div class="col-md-2">
                <input type="text" name="user_id" class="form-control" placeholder="User ID"
                    value="{{ request('user_id') }}">
            </div>
            @endif

            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-lg"></i> Clear
                </a>
            </div>
        </form>
        <div class="d-flex mb-3">
            <a href="{{ route('brands.export.csv', request()->query()) }}"
                class="btn btn-outline-secondary btn-sm me-1">
                <i class="bi bi-filetype-csv"></i> CSV
            </a>
            <a href="{{ route('brands.export.xlsx', request()->query()) }}"
                class="btn btn-outline-success btn-sm me-1">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('brands.export.pdf', request()->query()) }}"
                class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Status</th>
                    @if (Auth::user()->is_admin)
                    <th>User&nbsp;ID</th>
                    @endif
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                <tr>
                    <td>{{ $brand->code }}</td>
                    <td>{{ $brand->name }}</td>
                    <td>{{ $brand->status }}</td>
                    @if (Auth::user()->is_admin)
                    <td>{{ $brand->user_id }}</td> {{-- NEW --}}
                    @endif
                    <td>
                        <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                        <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this brand?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $brands->links() }}
    </div>
</x-app-layout>

<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 300); // Remove from DOM after fade
        });
    }, 2000);
</script>