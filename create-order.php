<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'message' => 'İşlem başarısız oldu.'
];

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

$customer_id = $_SESSION['user_id'];

// Transaction başlat
mysqli_begin_transaction($conn);

try {
    // Sepetteki ürünleri getir
    $cart_query = "SELECT c.*, p.product_name, p.price 
                   FROM cart c
                   JOIN products p ON c.product_id = p.product_id
                   WHERE c.customer_id = $customer_id";
    
    $cart_result = mysqli_query($conn, $cart_query);
    
    if (mysqli_num_rows($cart_result) == 0) {
        throw new Exception('Sepetiniz boş.');
    }
    
    // Toplam tutarı hesapla
    $subtotal = 0;
    $cart_items = [];
    
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $item_total = $item['quantity'] * $item['price'];
        $subtotal += $item_total;
        $cart_items[] = $item;
    }
    
    // Kargo ücreti (örnek)
    $shipping_amount = 10.00;
    $total_amount = $subtotal + $shipping_amount;
    
    // Sipariş numarası oluştur
    $order_number = 'ORD-' . date('Ymd') . '-' . uniqid();
    
    // Varsayılan adresi al
    $address_query = "SELECT address_id FROM customer_addresses 
                      WHERE customer_id = $customer_id 
                      ORDER BY created_at DESC LIMIT 1";
    $address_result = mysqli_query($conn, $address_query);
    $address = mysqli_fetch_assoc($address_result);
    $address_id = $address['address_id'] ?? null;
    
    // Siparişi oluştur
    $order_query = "INSERT INTO orders (customer_id, order_number, total_amount, shipping_amount, address_id, order_status, payment_method) 
                    VALUES ($customer_id, '$order_number', $total_amount, $shipping_amount, " . ($address_id ? $address_id : 'NULL') . ", 'pending', 'credit_card')";
    
    if (!mysqli_query($conn, $order_query)) {
        throw new Exception('Sipariş oluşturulamadı: ' . mysqli_error($conn));
    }
    
    $order_id = mysqli_insert_id($conn);
    
    // Sipariş detaylarını ekle
    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $product_name = mysqli_real_escape_string($conn, $item['product_name']);
        $quantity = $item['quantity'];
        $unit_price = $item['price'];
        $total_price = $quantity * $unit_price;
        
        $detail_query = "INSERT INTO order_details (order_id, product_id, product_name, quantity, unit_price, total_price) 
                         VALUES ($order_id, $product_id, '$product_name', $quantity, $unit_price, $total_price)";
        
        if (!mysqli_query($conn, $detail_query)) {
            throw new Exception('Sipariş detayları eklenemedi: ' . mysqli_error($conn));
        }
        
        // Stoktan düş
        $stock_query = "UPDATE products SET stock = stock - $quantity WHERE product_id = $product_id";
        if (!mysqli_query($conn, $stock_query)) {
            throw new Exception('Stok güncellenemedi: ' . mysqli_error($conn));
        }
    }
    
    // Sepeti temizle
    $clear_cart = "DELETE FROM cart WHERE customer_id = $customer_id";
    if (!mysqli_query($conn, $clear_cart)) {
        throw new Exception('Sepet temizlenemedi: ' . mysqli_error($conn));
    }
    
    // Transaction'ı tamamla
    mysqli_commit($conn);
    
    $response = [
        'status' => 'success',
        'message' => 'Siparişiniz başarıyla oluşturuldu.',
        'order_number' => $order_number,
        'order_id' => $order_id
    ];
    
} catch (Exception $e) {
    // Hata durumunda rollback
    mysqli_rollback($conn);
    $response['message'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
