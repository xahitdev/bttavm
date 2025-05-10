<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'cart_count' => 0,
    'message' => 'İşlem başarısız oldu.'
];

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

// POST verileri alınıyor
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];
    
    // Ürün stok kontrolü
    $stockQuery = "SELECT stock FROM products WHERE product_id = ?";
    $stockStmt = $conn->prepare($stockQuery);
    $stockStmt->bind_param("i", $product_id);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();
    $stockRow = $stockResult->fetch_assoc();
    $stockStmt->close();
    
    // Stok kontrolü
    if (!$stockRow || $stockRow['stock'] < $quantity) {
        $response['message'] = 'Üzgünüz, bu ürün stokta yok veya istediğiniz miktarda bulunmuyor.';
        echo json_encode($response);
        exit;
    }
    
    // Önce sepette bu ürün var mı kontrol et
    $checkQuery = "SELECT cart_id, quantity FROM cart WHERE customer_id = ? AND product_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $user_id, $product_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkStmt->close();
    
    if ($checkResult->num_rows > 0) {
        // Ürün zaten sepette, miktarı güncelle
        $row = $checkResult->fetch_assoc();
        $newQuantity = $row['quantity'] + $quantity;
        
        // Stok kontrolü
        if ($newQuantity > $stockRow['stock']) {
            $newQuantity = $stockRow['stock']; // Maksimum stok kadar ekle
        }
        
        $updateQuery = "UPDATE cart SET quantity = ?, updated_at = NOW() WHERE cart_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $newQuantity, $row['cart_id']);
        $result = $updateStmt->execute();
        $updateStmt->close();
        
        if ($result) {
            $response['message'] = 'Ürün miktarı güncellendi.';
        }
    } else {
        // Ürün sepette yok, yeni ekle
        $insertQuery = "INSERT INTO cart (customer_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("iii", $user_id, $product_id, $quantity);
        $result = $insertStmt->execute();
        $insertStmt->close();
        
        if ($result) {
            $response['message'] = 'Ürün sepete eklendi.';
        }
    }
    
    if (isset($result) && $result) {
        // Sepet sayısını hesapla (toplam ürün adedi değil, benzersiz ürün sayısı)
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
            'cart_count' => $cart_count,
            'message' => 'Ürün sepete eklendi.'
        ];
    }
}

// JSON olarak cevap döndür
header('Content-Type: application/json');
echo json_encode($response);
?>
