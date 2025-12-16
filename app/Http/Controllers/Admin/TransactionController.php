<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'driver', 'job'])->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['customer', 'driver', 'job']);
        return view('admin.transactions.show', compact('transaction'));
    }
}
