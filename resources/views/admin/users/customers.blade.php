@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers Management')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Customers List</h3>
        <span class="text-sm text-gray-600">Total: {{ $users->total() }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=32" alt="{{ $user->name }}" class="w-8 h-8 rounded-full mr-3">
                                <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->phone }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full @if($user->status === 'active') bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->created_at->format('M d, Y') ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-{{ $user->status === 'active' ? 'red' : 'green' }}-600 hover:text-{{ $user->status === 'active' ? 'red' : 'green' }}-900">
                                    {{ $user->status === 'active' ? 'Block' : 'Unblock' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No customers found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-200">
        {{ $users->links() }}
    </div>
</div>
@endsection
