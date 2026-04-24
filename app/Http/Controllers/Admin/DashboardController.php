<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveChat;
use App\Models\SitePageView;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userCount = User::query()->count();
        $transactionCount = Transaction::query()->count();
        $pendingLike = Transaction::query()
            ->where(function ($q) {
                $q->where('status', '!=', 'Completed')
                    ->orWhereNull('status');
            })
            ->count();
        $openChats = LiveChat::query()->where('status', 'open')->count();
        $volumeMonth = (float) Transaction::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('transaction_amount');

        $recentTransactions = Transaction::query()
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $chartEscrowsDaily = $this->dailyCountSeries(Transaction::query(), 14);
        $chartUsersDaily = $this->dailyCountSeries(User::query(), 14);
        $chartTrafficDaily = $this->dailyPageViewSeries(14);
        $chartStatusBreakdown = $this->transactionStatusBreakdown();

        $trafficViews7d = (int) SitePageView::query()
            ->where('created_at', '>=', now()->subDays(7)->startOfDay())
            ->count();
        $trafficViews30d = (int) SitePageView::query()
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->count();

        $topPaths14d = SitePageView::query()
            ->where('created_at', '>=', now()->subDays(14)->startOfDay())
            ->selectRaw('path, COUNT(*) as views')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'userCount',
            'transactionCount',
            'pendingLike',
            'openChats',
            'volumeMonth',
            'recentTransactions',
            'chartEscrowsDaily',
            'chartUsersDaily',
            'chartTrafficDaily',
            'chartStatusBreakdown',
            'trafficViews7d',
            'trafficViews30d',
            'topPaths14d',
        ));
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    protected function dailyCountSeries(Builder $query, int $days): array
    {
        $model = $query->getModel();
        $labels = [];
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('M j');
            $values[] = (int) $model->newQuery()->whereDate('created_at', $date->toDateString())->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    protected function dailyPageViewSeries(int $days): array
    {
        $labels = [];
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('M j');
            $values[] = (int) SitePageView::query()->whereDate('created_at', $date->toDateString())->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * @return array{labels: list<string>, values: list<int>}
     */
    protected function transactionStatusBreakdown(): array
    {
        $rows = Transaction::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->orderByDesc('aggregate')
            ->get();

        return [
            'labels' => $rows->map(function ($row) {
                $s = $row->status;

                return ($s !== null && trim((string) $s) !== '') ? (string) $s : 'Unknown';
            })->all(),
            'values' => $rows->pluck('aggregate')->map(fn ($v) => (int) $v)->all(),
        ];
    }
}
