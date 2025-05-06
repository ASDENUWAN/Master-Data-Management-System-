<x-app-layout>
    <x-slot name="header">My Profile</x-slot>

    <div class="container mt-4">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Name</h5>
                <p class="card-text">{{ $user->name }}</p>

                <h5 class="card-title">Email</h5>
                <p class="card-text">{{ $user->email }}</p>

                <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(a => {
            a.classList.remove('show');
            a.classList.add('fade');
            setTimeout(() => a.remove(), 300);
        });
    }, 5000);
</script>