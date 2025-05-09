<?php
session_start();

// Gerekli veritabanı bağlantısı ve diğer gerekli dosyaları dahil edin
require_once 'settings.php';

// settings.php'den mysqli bağlantınız muhtemelen $conn, $db veya $mysqli olarak geliyor
// Aşağıda $conn olarak varsayıyorum, siz kendi değişken adınızla değiştirin
// global $conn; 

$response = [
    'status' => 'error',
    'favorites_count' => 0,
    'message' => 'Başlangıç durumu'
];

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

try {
    // POST verileri alınıyor
    if (isset($_POST['product_id']) && isset($_POST['action'])) {
        $product_id = $_POST['product_id'];
        $user_id = $_SESSION['user_id'];
        $action = $_POST['action'];
        
        if ($action === 'add') {
            // Önce bu ürün daha önce eklendi mi kontrol edelim
            $checkQuery = "SELECT COUNT(*) AS count FROM favorites WHERE customer_id = ? AND product_id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("ii", $user_id, $product_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $row = $checkResult->fetch_assoc();
            $exists = $row['count'];
            $checkStmt->close();
            
            if ($exists) {
                // Eğer zaten varsa güncelleme yapma, başarılı yanıt dön
                $response['status'] = 'success';
                $response['message'] = 'Ürün zaten favorilerinizde.';
            } else {
                // Favorilere ekle
                $query = "INSERT INTO favorites (customer_id, product_id, created_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $user_id, $product_id);
                $result = $stmt->execute();
                $stmt->close();
                
                if ($result) {
                    $response['status'] = 'success';
                    $response['message'] = 'Ürün favorilere eklendi.';
                } else {
                    $response['message'] = 'Veritabanına eklenirken hata oluştu: ' . $conn->error;
                }
            }
            
            // Favori sayısını al
            $countQuery = "SELECT COUNT(*) AS count FROM favorites WHERE customer_id = ?";
            $countStmt = $conn->prepare($countQuery);
            $countStmt->bind_param("i", $user_id);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $row = $countResult->fetch_assoc();
            $favorites_count = $row['count'];
            $countStmt->close();
            
            $response['favorites_count'] = $favorites_count;
        }
    } else {
        $response['message'] = 'Gerekli parametreler eksik.';
    }
} catch (Exception $e) {
    $response['message'] = 'Bir hata oluştu: ' . $e->getMessage();
}

// JSON olarak cevap döndür
header('Content-Type: application/json');
echo json_encode($response);
?>
