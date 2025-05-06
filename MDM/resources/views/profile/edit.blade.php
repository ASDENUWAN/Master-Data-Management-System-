<x-app-layout>
    <x-slot name="header">Edit Profile</x-slot>

    <div class="container mt-4">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf

            {{-- Name & Email as before --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name"
                    value="{{ old('name',$user->name) }}"
                    class="form-control" required>
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email"
                    value="{{ old('email',$user->email) }}"
                    class="form-control" required>
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Toggle to show password fields --}}
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="" id="togglePassword">
                <label class="form-check-label" for="togglePassword">
                    Change Password
                </label>
            </div>

            {{-- Password fields, hidden by default --}}
            <div id="passwordFields" style="display:none;">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="{{ route('profile.show') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</x-app-layout>

{{-- JS to toggle visibility --}}
<script>
    document.getElementById('togglePassword')
        .addEventListener('change', function() {
            document.getElementById('passwordFields').style.display =
                this.checked ? 'block' : 'none';
        });
</script>