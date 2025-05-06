<x-app-layout>
    <x-slot name="header">
        <h2 class="h4">Welcome, {{ Auth::user()->name }}</h2>
    </x-slot>

    <div class="container mt-4">

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row g-4 mb-4">
            <!-- Stats -->
            <div class="col-md-4">
                <div class="card text-white bg-primary shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Brands</h5>
                        <p class="card-text fs-3">{{ $brandCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Categories</h5>
                        <p class="card-text fs-3">{{ $categoryCount }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning shadow">
                    <div class="card-body">
                        <h5 class="card-title">Total Items</h5>
                        <p class="card-text fs-3">{{ $itemCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Quick Links -->
            <div class="col-md-4">
                <div class="card border-primary shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Brands</h5>
                        <p class="card-text">Manage master brand data.</p>
                        <a href="{{ route('brands.index') }}" class="btn btn-primary">Go to Brands</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <p class="card-text">Manage master categories.</p>
                        <a href="{{ route('categories.index') }}" class="btn btn-success">Go to Categories</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Items</h5>
                        <p class="card-text">View and manage items.</p>
                        <a href="{{ route('items.index') }}" class="btn btn-warning">Go to Items</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>