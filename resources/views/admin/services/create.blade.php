@extends('layouts.app')

@section('title', 'Create Service')
@section('page-title', 'Create New Service')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Service Name</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name') }}">
                @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-900 mb-2">Description</label>
                <textarea id="description" name="description" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="mb-6">
                <label for="icon" class="block text-sm font-medium text-gray-900 mb-2">Icon (emoji)</label>
                <input type="text" id="icon" name="icon" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('icon') border-red-500 @enderror" value="{{ old('icon') }}" placeholder="e.g., 🚗">
                @error('icon')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Create Service
                </button>
                <a href="{{ route('admin.services.index') }}" class="px-6 py-2 border border-gray-300 text-gray-900 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
