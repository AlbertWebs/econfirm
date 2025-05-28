<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Transaction;
use App\Services\MpesaService;
class DashboardController extends Controller
{
    /**
     * Display the dashboard with transactions.
     *
     * @return \Illuminate\View\View
     */
    public function viewTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        return view('dashboard.view', compact('transaction'));
    }

    public function index(): \Illuminate\View\View
    {
        $transactions = Transaction::all();
        return view('dashboard.index', compact('transactions'));
    }

    public function approveTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);

        return view('dashboard.approve', compact('transaction'));
    }
}