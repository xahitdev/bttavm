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
    
    // Form verilerini al ve temizle
    $seller_name = mysqli_real_escape_string($conn, $_POST['seller_name']);
    $seller_mail = mysqli_real_escape_string($conn, $_POST['seller_mail']);
    $seller_logo = !empty($_POST['seller_logo']) ? mysqli_real_escape_string($conn, $_POST['seller_logo']) : NULL;
    $address_id = !empty($_POST['address_id']) ? (int)$_POST['address_id'] : NULL;
    
    // Email kontrolü (başka satıcıda var mı?)
    $email_check = "SELECT seller_id FROM sellers 
                    WHERE seller_mail = '$seller_mail' 
                    AND seller_id != $seller_id";
    $check_result = mysqli_query($conn, $email_check);
    
    if (mysqli_num_rows($check_result) > 0) {
        $response['message'] = 'Bu email adresi başka bir satıcı tarafından kullanılıyor.';
        echo json_encode($response);
        exit;
    }
    
    // Güncelleme sorgusu
    $update_query = "UPDATE sellers SET 
                     seller_name = '$seller_name',
                     seller_mail = '$seller_mail'";
    
    // Logo varsa ekle
    if ($seller_logo !== NULL) {
        $update_query .= ", seller_logo = '$seller_logo'";
    }
    
    // Address ID varsa ekle
    if ($address_id !== NULL) {
        $update_query .= ", address_id = $address_id";
    }
    
    $update_query .= " WHERE seller_id = $seller_id";
    
    if (mysqli_query($conn, $update_query)) {
        $response = [
            'status' => 'success',
            'message' => 'Bilgileriniz başarıyla güncellendi.'
        ];
    } else {
        $response['message'] = 'Güncelleme sırasında bir hata oluştu: ' . mysqli_error($conn);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
