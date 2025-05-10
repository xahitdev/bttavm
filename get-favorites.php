<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'items' => []
];

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Favori ürünleri getir
    $query = "SELECT f.favorite_id, f.product_id, p.product_name, p.price, p.stock 
              FROM favorites f
              JOIN products p ON f.product_id = p.product_id
              WHERE f.customer_id = $user_id
              ORDER BY f.created_at DESC 
              LIMIT 5"; // Son 5 favori

    $result = $conn->query($query);
    
    $items = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Her ürün için product_images tablosundan resim al
            $productId = $row['product_id'];
            $imageQuery = "SELECT product_images_url FROM product_images WHERE product_id = $productId LIMIT 1";
            $imageResult = $conn->query($imageQuery);
            
            $productImage = "img/no-image.jpg"; // Varsayılan resim
            
            if ($imageResult && $imageResult->num_rows > 0) {
                $imageRow = $imageResult->fetch_assoc();
                if (!empty($imageRow['product_images_url'])) {
                    // # karakterinden bölerek ilk resmi al
                    $imageArray = explode('#', $imageRow['product_images_url']);
                    if (!empty($imageArray[0])) {
                        $productImage = $imageArray[0]; // İlk resmi kullan
                    }
                }
            }
            
            $items[] = [
                'id' => $row['product_id'],
                'name' => $row['product_name'],
                'price' => $row['price'],
                'image' => $productImage,
                'stock' => $row['stock']
            ];
        }
    }
    
    $response = [
        'status' => 'success',
        'items' => $items
    ];
} catch (Exception $e) {
    $response['message'] = 'Bir hata oluştu: ' . $e->getMessage();
}

// JSON olarak cevap döndür
header('Content-Type: application/json');
echo json_encode($response);
?>
