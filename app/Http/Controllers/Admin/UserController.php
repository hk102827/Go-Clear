<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function customers()
    {
        $users = User::where('role', 'customer')->paginate(15);
        return view('admin.users.customers', compact('users'));
    }

    public function drivers()
    {
        $users = User::where('role', 'driver')->paginate(15);
        return view('admin.users.drivers', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['driverProfile', 'driverServices', 'jobsAsCustomer', 'jobsAsDriver', 'transactionsAsCustomer', 'transactionsAsDriver', 'ratingsAsCustomer', 'ratingsAsDriver']);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user)
    {
        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.customers')->with('success', 'User deleted successfully.');
    }
}
