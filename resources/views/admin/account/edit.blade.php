@extends('layouts.app') {{-- or your admin layout path --}}

@section('page-title', 'My Account')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow rounded p-6">
        <h3 class="text-lg font-semibold mb-4">Account Settings</h3>

        {{-- @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif --}}

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.account.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input name="name" value="{{ old('name', $admin->name) }}" type="text" required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input name="email" value="{{ old('email', $admin->email) }}" type="email" required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Profile Image (optional)</label>

            <div class="flex items-center gap-4 mt-1">
                {{-- File input --}}
                <input type="file" name="profile_image" accept="image/*">

                {{-- Current preview or placeholder --}}
                <div class="text-sm text-gray-500">
                    @if($admin->profile_image)
                        <img src="{{ asset('storage/' . $admin->profile_image) }}" alt="avatar" class="w-12 h-12 rounded-full object-cover">
                    @else
                        {{-- fallback local preview or initials --}}
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-semibold">
                            {{ \Illuminate\Support\Str::upper(substr($admin->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Delete checkbox --}}
            @if($admin->profile_image)
                <div class="mt-2">
                    <label class="inline-flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="delete_image" value="1" class="mr-2">
                        Remove current image
                    </label>
                    <p class="text-xs text-gray-400 mt-1">Check to delete the existing profile image without uploading a new one.</p>
                </div>
            @endif
        </div>


             <hr class="my-4">

            <h4 class="font-medium mb-2">Change Password</h4>
            <p class="text-xs text-gray-500 mb-3">Provide current password to change to a new password. Leave blank to keep the current password.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Current Password</label>
                <input name="current_password" type="password" autocomplete="current-password"
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input name="password" type="password" autocomplete="new-password" minlength="8"
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <input name="password_confirmation" type="password" autocomplete="new-password"
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
