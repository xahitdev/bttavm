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
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Sepetten kaldır
    $deleteQuery = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("ii", $user_id, $product_id);
    $result = $deleteStmt->execute();
    $deleteStmt->close();
    
    if ($result) {
        // Sepet sayısını hesapla
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
            'message' => 'Ürün sepetten kaldırıldı.'
        ];
    }
}

// JSON olarak cevap döndür
header('Content-Type: application/json');
echo json_encode($response);
?>
