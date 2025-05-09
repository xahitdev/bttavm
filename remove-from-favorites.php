<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'favorites_count' => 0,
    'message' => 'İşlem başarısız oldu.'
];

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

// POST verileri alınıyor
if (isset($_POST['product_id']) && isset($_POST['action'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    $action = $_POST['action'];
    
    if ($action === 'remove') {
        // Favorilerden kaldır
        $query = "DELETE FROM favorites WHERE customer_id = ? AND product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $product_id);
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            // Favori sayısını al
            $countQuery = "SELECT COUNT(*) AS count FROM favorites WHERE customer_id = ?";
            $countStmt = $conn->prepare($countQuery);
            $countStmt->bind_param("i", $user_id);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $row = $countResult->fetch_assoc();
            $favorites_count = $row['count'];
            $countStmt->close();
            
            $response = [
                'status' => 'success',
                'favorites_count' => $favorites_count,
                'message' => 'Ürün favorilerden kaldırıldı.'
            ];
        } else {
            $response['message'] = 'Veritabanı işlemi sırasında bir hata oluştu: ' . $conn->error;
        }
    }
}

// JSON olarak cevap döndür
header('Content-Type: application/json');
echo json_encode($response);
?>
