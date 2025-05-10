<?php
session_start();
require_once 'settings.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['order_id'])) {
    echo '<div class="alert alert-danger">Geçersiz istek.</div>';
    exit;
}

$customer_id = $_SESSION['user_id'];
$order_id = (int)$_POST['order_id'];

// Siparişin bu kullanıcıya ait olduğunu kontrol et
$check_query = "SELECT o.*, a.full_name, a.phone, a.address_detail, 
                i.il_adi, ilc.ilce_adi, s.semt_adi
                FROM orders o
                LEFT JOIN customer_addresses a ON o.address_id = a.address_id
                LEFT JOIN iller i ON a.city = i.id
                LEFT JOIN ilceler ilc ON a.district = ilc.id
                LEFT JOIN semtler s ON a.semt = s.id
                WHERE o.order_id = $order_id AND o.customer_id = $customer_id";

$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) == 0) {
    echo '<div class="alert alert-danger">Bu siparişe erişim yetkiniz yok.</div>';
    exit;
}

$order = mysqli_fetch_assoc($check_result);

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
    <!-- Sipariş Özeti -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6>Sipariş Bilgileri</h6>
            <table class="table table-sm">
                <tr>
                    <td><strong>Sipariş No:</strong></td>
                    <td><?php echo $order['order_number']; ?></td>
                </tr>
                <tr>
                    <td><strong>Tarih:</strong></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                </tr>
                <tr>
                    <td><strong>Durum:</strong></td>
                    <td><?php echo $status; ?></td>
                </tr>
                <tr>
                    <td><strong>Ödeme Yöntemi:</strong></td>
                    <td><?php echo ucfirst($order['payment_method']); ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Teslimat Adresi</h6>
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
                <p>Adres bilgisi bulunamadı.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Ürün Detayları -->
    <h6>Sipariş Detayları</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Ürün</th>
                    <th>Adet</th>
                    <th>Birim Fiyat</th>
                    <th>Toplam</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Sipariş detaylarını getir
                $details_query = "SELECT od.*, p.product_name as current_name 
                                  FROM order_details od
                                  LEFT JOIN products p ON od.product_id = p.product_id
                                  WHERE od.order_id = $order_id";
                
                $details_result = mysqli_query($conn, $details_query);
                
                while ($detail = mysqli_fetch_assoc($details_result)) {
                    echo "<tr>
                            <td>{$detail['product_name']}</td>
                            <td>{$detail['quantity']}</td>
                            <td>{$detail['unit_price']} TL</td>
                            <td>{$detail['total_price']} TL</td>
                          </tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Ara Toplam:</strong></td>
                    <td><?php echo ($order['total_amount'] - $order['shipping_amount']); ?> TL</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Kargo Ücreti:</strong></td>
                    <td><?php echo $order['shipping_amount']; ?> TL</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Genel Toplam:</strong></td>
                    <td><strong><?php echo $order['total_amount']; ?> TL</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<style>
.order-details {
    padding: 15px;
}
.order-details h6 {
    margin-bottom: 15px;
    border-bottom: 2px solid #eee;
    padding-bottom: 5px;
}
</style>
