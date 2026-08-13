<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\View;
use App\Models\Order;

class DashboardController extends BaseAdminController
{
    public function index(): void
    {
        $pdo = Database::connection();
        $orderCounts = Order::counts();

        $stats = [
            'products' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'onOffer' => (int) $pdo->query('SELECT COUNT(*) FROM products WHERE is_sale = 1')->fetchColumn(),
            'categories' => (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
            'orders' => $orderCounts['total'],
            'pendingOrders' => $orderCounts['pending'],
            'customers' => (int) $pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
        ];

        View::render('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'activeNav' => 'dashboard',
            'stats' => $stats,
            'recentOrders' => Order::recent(8),
        ]);
    }
}
