<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'message' => 'İşlem başarısız oldu.',
    'cart_count' => 0
];

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

// POST verileri alınıyor
if (isset($_POST['updates'])) {
    $user_id = $_SESSION['user_id'];
    $updates = json_decode($_POST['updates'], true);
    $success = true;
    
    // Her ürün için güncelleme işlemi
    foreach($updates as $update) {
        $product_id = $update['product_id'];
        $quantity = (int)$update['quantity'];
        
        // Stok kontrolü
        $stockQuery = "SELECT stock FROM products WHERE product_id = ?";
        $stockStmt = $conn->prepare($stockQuery);
        $stockStmt->bind_param("i", $product_id);
        $stockStmt->execute();
        $stockResult = $stockStmt->get_result();
        $stockRow = $stockResult->fetch_assoc();
        $stockStmt->close();
        
        // Stok yetersizse atla
        if (!$stockRow || $stockRow['stock'] < $quantity) {
            $success = false;
            continue;
        }
        
        // Miktar 0 veya negatifse ürünü sepetten kaldır
        if ($quantity <= 0) {
            $deleteQuery = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bind_param("ii", $user_id, $product_id);
            $result = $deleteStmt->execute();
            $deleteStmt->close();
        } else {
            // Miktarı güncelle
            $updateQuery = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE customer_id = ? AND product_id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("iii", $quantity, $user_id, $product_id);
            $result = $updateStmt->execute();
            $updateStmt->close();
        }
        
        if (!$result) {
            $success = false;
        }
    }
    
    if ($success) {
        // Güncel sepet sayısını al
        $countQuery = "SELECT COUNT(*) AS count FROM cart WHERE customer_id = ?";
        $countStmt = $conn->prepare($countQuery);
        $countStmt->bind_param("i", $user_id);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $row = $countResult->fetch_assoc();
        $cart_count = $row['count'];
        $countStmt->close();
        
        $response = [
            'status' => 'success',
            'message' => 'Sepet güncellendi.',
            'cart_count' => $cart_count
        ];
    } else {
        $response['message'] = 'Bazı ürünler güncellenemedi. Lütfen tekrar deneyin.';
    }
}

// JSON olarak cevap döndür
header('Content-Type: application/json');
echo json_encode($response);
?>
