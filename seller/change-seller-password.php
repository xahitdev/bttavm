<?php
session_start();
require_once '../settings.php';

$response = [
    'status' => 'error',
    'message' => 'İşlem başarısız oldu.'
];

// Satıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['seller_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seller_id = $_SESSION['seller_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Mevcut şifreyi kontrol et
    $query = "SELECT password_hashed FROM sellers WHERE seller_id = $seller_id";
    $result = mysqli_query($conn, $query);
    $seller = mysqli_fetch_assoc($result);
    
    // Mevcut şifreyi MD5 ile hashle ve kontrol et
    $current_password_hashed = md5($current_password);
    
    if ($current_password_hashed !== $seller['password_hashed']) {
        $response['message'] = 'Mevcut şifreniz yanlış.';
        echo json_encode($response);
        exit;
    }
    
    // Yeni şifreyi MD5 ile hashle
    $new_password_hashed = md5($new_password);
    
    // Güncelleme sorgusu
    $update_query = "UPDATE sellers 
                     SET password_hashed = '$new_password_hashed' 
                     WHERE seller_id = $seller_id";
    
    if (mysqli_query($conn, $update_query)) {
        $response = [
            'status' => 'success',
            'message' => 'Şifreniz başarıyla güncellendi.'
        ];
    } else {
        $response['message'] = 'Şifre güncellenirken bir hata oluştu: ' . mysqli_error($conn);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
