@extends('layouts.admin')

@section('title', 'New User')

@section('content')
<div class="p-6 md:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">
            New User
        </h1>
        <p class="text-muted-foreground">
            Add a new user to the system
        </p>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Name *</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="Full name"
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">Email *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            placeholder="user@example.com"
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium mb-2">Password *</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            minlength="8"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm Password *</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            minlength="8"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="role" class="block text-sm font-medium mb-2">Role *</label>
                        <select
                            id="role"
                            name="role"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="seller" {{ old('role') === 'seller' ? 'selected' : '' }}>Seller</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email_verified_at" class="block text-sm font-medium mb-2">Email Verified At</label>
                        <input
                            type="datetime-local"
                            id="email_verified_at"
                            name="email_verified_at"
                            value="{{ old('email_verified_at') }}"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                    >
                        Create User
                    </button>
                    <a href="{{ route('admin.users.index') }}">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                        >
                            Cancel
                        </button>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
