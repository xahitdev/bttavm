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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    // Mevcut şifreyi kontrol et
    $query = "SELECT password_hashed FROM customers WHERE customer_id = $customer_id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    // Şifre doğrulama (hash algoritmanıza göre değiştirin)
    if (!password_verify($current_password, $user['password_hashed'])) {
        $response['message'] = 'Mevcut şifreniz yanlış.';
        echo json_encode($response);
        exit;
    }
    
    // Yeni şifreyi hashle
    $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Şifreyi güncelle
    $update_query = "UPDATE customers 
                     SET password_hashed = '$new_password_hashed' 
                     WHERE customer_id = $customer_id";
    
    if (mysqli_query($conn, $update_query)) {
        $response = [
            'status' => 'success',
            'message' => 'Şifreniz başarıyla güncellendi.'
        ];
    } else {
        $response['message'] = 'Şifre güncellenirken bir hata oluştu.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
