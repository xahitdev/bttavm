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
    
    // Form verilerini al ve temizle
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $customer_mail = mysqli_real_escape_string($conn, $_POST['customer_mail']);
    $customer_birth = mysqli_real_escape_string($conn, $_POST['customer_birth']);
    $customer_gender = (int)$_POST['customer_gender'];
    
    // Email kontrolü (başka kullanıcıda var mı?)
    $email_check = "SELECT customer_id FROM customers 
                    WHERE customer_mail = '$customer_mail' 
                    AND customer_id != $customer_id";
    $check_result = mysqli_query($conn, $email_check);
    
    if (mysqli_num_rows($check_result) > 0) {
        $response['message'] = 'Bu email adresi başka bir kullanıcı tarafından kullanılıyor.';
        echo json_encode($response);
        exit;
    }
    
    // Güncelleme sorgusu
    $update_query = "UPDATE customers SET 
                     customer_name = '$customer_name',
                     customer_phone = '$customer_phone',
                     customer_mail = '$customer_mail',
                     customer_birth = '$customer_birth',
                     customer_gender = $customer_gender
                     WHERE customer_id = $customer_id";
    
    if (mysqli_query($conn, $update_query)) {
        $response = [
            'status' => 'success',
            'message' => 'Bilgileriniz başarıyla güncellendi.'
        ];
    } else {
        $response['message'] = 'Güncelleme sırasında bir hata oluştu.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
