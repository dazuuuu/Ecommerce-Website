<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends BaseAdminController
{
    private const RANGES = ['week' => 7, 'month' => 30];

    public function index(): void
    {
        Product::expireMaturedOffers();
        $range = (string) Request::query('range', 'week');
        if (!isset(self::RANGES[$range])) {
            $range = 'week';
        }
        $days = self::RANGES[$range];

        $kpis = [
            'orders' => $this->metric("SELECT COUNT(*) FROM orders WHERE created_at >= :from AND created_at < :to", $days),
            'customers' => $this->metric("SELECT COUNT(*) FROM customers WHERE created_at >= :from AND created_at < :to", $days),
            'pending' => $this->metric("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND created_at >= :from AND created_at < :to", $days),
            'revenue' => $this->metric("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'cancelled' AND created_at >= :from AND created_at < :to", $days),
        ];

        $pdo = Database::connection();
        $totals = [
            'orders' => Order::counts()['total'],
            'customers' => (int) $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
            'pending' => Order::counts()['pending'],
            'revenue' => (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'cancelled'")->fetchColumn(),
        ];

        View::render('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'activeNav' => 'dashboard',
            'range' => $range,
            'kpis' => $kpis,
            'totals' => $totals,
            'recentOrders' => Order::recent(30),
        ]);
    }

    /**
     * Current-period value, previous-period value, % change and one point per
     * day (for the sparkline) for an aggregate query with :from/:to bounds.
     */
    private function metric(string $sql, int $days): array
    {
        $stmt = Database::connection()->prepare($sql);
        $run = function (string $from, string $to) use ($stmt): float {
            $stmt->execute(['from' => $from, 'to' => $to]);
            return (float) $stmt->fetchColumn();
        };

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $start = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $prevStart = date('Y-m-d', strtotime('-' . (2 * $days - 1) . ' days'));

        $current = $run($start, $tomorrow);
        $previous = $run($prevStart, $start);
        $change = $previous > 0 ? round((($current - $previous) / $previous) * 100) : ($current > 0 ? 100 : 0);

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} days"));
            $series[] = $run($day, date('Y-m-d', strtotime($day . ' +1 day')));
        }

        return ['current' => $current, 'previous' => $previous, 'change' => (int) $change, 'series' => $series];
    }
}
