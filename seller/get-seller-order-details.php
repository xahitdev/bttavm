<?php
session_start();
require_once '../settings.php';

if (!isset($_SESSION['seller_id']) || !isset($_POST['order_id'])) {
    echo '<div class="alert alert-danger">Invalid request.</div>';
    exit;
}

$seller_id = $_SESSION['seller_id'];
$order_id = (int)$_POST['order_id'];

// Sipariş bilgilerini getir
$orderQuery = "SELECT o.*, c.customer_name, c.customer_mail, c.customer_phone,
               a.full_name, a.phone, a.address_detail, 
               i.il_adi, ilc.ilce_adi, s.semt_adi
               FROM orders o
               JOIN customers c ON o.customer_id = c.customer_id
               LEFT JOIN customer_addresses a ON o.address_id = a.address_id
               LEFT JOIN iller i ON a.city = i.id
               LEFT JOIN ilceler ilc ON a.district = ilc.id
               LEFT JOIN semtler s ON a.semt = s.id
               WHERE o.order_id = $order_id";

$orderResult = $conn->query($orderQuery);

if ($orderResult->num_rows == 0) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    exit;
}

$order = $orderResult->fetch_assoc();

// Sadece bu satıcıya ait ürünleri getir
$detailsQuery = "SELECT od.*, p.product_name, p.product_id
                 FROM order_details od
                 JOIN products p ON od.product_id = p.product_id
                 WHERE od.order_id = $order_id AND p.seller_id = $seller_id";

$detailsResult = $conn->query($detailsQuery);

// Durum çevirileri
$status_labels = [
    'pending' => '<span class="badge badge-warning">Beklemede</span>',
    'processing' => '<span class="badge badge-info">İşleniyor</span>',
    'shipped' => '<span class="badge badge-primary">Kargoda</span>',
    'delivered' => '<span class="badge badge-success">Teslim Edildi</span>',
    'cancelled' => '<span class="badge badge-danger">İptal Edildi</span>'
];

$status = $status_labels[$order['order_status']] ?? $order['order_status'];
?>

<div class="order-details">
    <div class="row mb-4">
        <div class="col-md-6">
            <h6>Order Information</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Order No:</strong></td>
                    <td><?php echo $order['order_number']; ?></td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td><?php echo $status; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Customer Information</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Name:</strong></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><?php echo htmlspecialchars($order['customer_mail']); ?></td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <h6>Delivery Address</h6>
            <?php if ($order['address_id']): ?>
                <p>
                    <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                    <?php echo htmlspecialchars($order['phone']); ?><br>
                    <?php echo htmlspecialchars($order['address_detail']); ?><br>
                    <?php 
                        $address_parts = [];
                        if ($order['semt_adi']) $address_parts[] = $order['semt_adi'];
                        if ($order['ilce_adi']) $address_parts[] = $order['ilce_adi'];
                        if ($order['il_adi']) $address_parts[] = $order['il_adi'];
                        echo implode(', ', $address_parts);
                    ?>
                </p>
            <?php else: ?>
                <p>Address information not available.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <h6>Your Products in this Order</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $seller_total = 0;
                while ($detail = $detailsResult->fetch_assoc()) {
                    $seller_total += $detail['total_price'];
                    echo "<tr>
                            <td>{$detail['product_name']}</td>
                            <td>{$detail['quantity']}</td>
                            <td>" . number_format($detail['unit_price'], 2) . " TL</td>
                            <td>" . number_format($detail['total_price'], 2) . " TL</td>
                          </tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong><?php echo number_format($seller_total, 2); ?> TL</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
