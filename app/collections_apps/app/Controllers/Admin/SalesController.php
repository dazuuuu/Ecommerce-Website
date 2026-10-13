<?php

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Request;
use App\Core\View;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use PDOException;

class SalesController extends BaseAdminController
{
    public function index(): void
    {
        $products = array_values(array_filter(
            Product::all(),
            fn($p) => (int) ($p['quantity'] ?? 0) > 0
        ));

        View::render('admin.sales.index', [
            'pageTitle' => 'Record Sale',
            'activeNav' => 'sales',
            'products' => $products,
            'recentSales' => Sale::all(20),
        ]);
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        $payload = Request::json();

        if (!csrfVerify((string) ($payload['csrfToken'] ?? ''))) {
            $this->respond(['success' => false, 'message' => 'CSRF token mismatch. Please reload the page and try again.'], 419);
        }

        $cartItems = $payload['items'] ?? [];
        $customerName = trim((string) ($payload['customerName'] ?? ''));
        $paymentMethod = in_array($payload['paymentMethod'] ?? '', ['cash', 'mpesa', 'mixed'], true)
            ? $payload['paymentMethod']
            : 'cash';
        $mpesaCode = trim((string) ($payload['mpesaCode'] ?? ''));
        $cashAmount = max(0, (float) ($payload['cashAmount'] ?? 0));
        $mpesaAmount = max(0, (float) ($payload['mpesaAmount'] ?? 0));

        if (!$cartItems) {
            $this->respond(['success' => false, 'message' => 'Add at least one product to the sale.'], 422);
        }

        $lineItems = [];
        $total = 0.0;
        foreach ($cartItems as $item) {
            $productId = (int) ($item['productId'] ?? 0);
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $product = Product::findRawById($productId);
            if (!$product) {
                $this->respond(['success' => false, 'message' => 'One of the selected products no longer exists.'], 422);
            }
            $available = (int) ($product['quantity'] ?? 0);
            if ($qty > $available) {
                $this->respond(['success' => false, 'message' => "Not enough stock for {$product['name']}. Only {$available} left."], 422);
            }
            $unitPrice = (float) $product['price'];
            $total += $unitPrice * $qty;
            $lineItems[] = [
                'product_id' => $productId,
                'product_name' => $product['name'],
                'quantity' => $qty,
                'unit_price' => $unitPrice,
            ];
        }
        $total = round($total, 2);

        if ($paymentMethod === 'cash') {
            $cashAmount = $total;
            $mpesaAmount = 0;
        } elseif ($paymentMethod === 'mpesa') {
            $mpesaAmount = $total;
            $cashAmount = 0;
        }

        $pdo = Database::connection();
        try {
            $pdo->beginTransaction();

            $saleId = Sale::create([
                'customer_name' => $customerName !== '' ? $customerName : null,
                'payment_method' => $paymentMethod,
                'mpesa_code' => $mpesaCode !== '' ? $mpesaCode : null,
                'cash_amount' => $cashAmount,
                'mpesa_amount' => $mpesaAmount,
                'total' => $total,
                'admin_id' => $this->admin['id'] ?? null,
            ]);

            foreach ($lineItems as $li) {
                SaleItem::create($saleId, $li);
                Product::recordSale($li['product_id'], $li['quantity']);
            }

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $this->respond(['success' => false, 'message' => 'Could not record the sale. Please try again.'], 500);
        }

        $this->respond([
            'success' => true,
            'saleId' => $saleId,
            'total' => $total,
            'message' => 'Sale recorded successfully.',
        ]);
    }

    private function respond(array $payload, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode($payload);
        exit;
    }
}
