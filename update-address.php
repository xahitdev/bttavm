<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'message' => 'İşlem başarısız oldu.'
];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Lütfen önce giriş yapınız.';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['address_id'])) {
    $customer_id = $_SESSION['user_id'];
    $address_id = (int)$_POST['address_id'];
    
    // Adresin bu kullanıcıya ait olduğunu kontrol et
    $check_query = "SELECT address_id FROM customer_addresses 
                    WHERE address_id = $address_id AND customer_id = $customer_id";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) == 0) {
        $response['message'] = 'Bu adres size ait değil.';
        echo json_encode($response);
        exit;
    }
    
    // Form verilerini al
    $address_title = mysqli_real_escape_string($conn, $_POST['address_title']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $semt = mysqli_real_escape_string($conn, $_POST['semt']);
    $address_detail = mysqli_real_escape_string($conn, $_POST['address_detail']);
    
    // Adresi güncelle
    $update_query = "UPDATE customer_addresses SET 
                     address_title = '$address_title',
                     full_name = '$full_name',
                     phone = '$phone',
                     city = '$city',
                     district = '$district',
                     semt = '$semt',
                     address_detail = '$address_detail'
                     WHERE address_id = $address_id";
    
    if (mysqli_query($conn, $update_query)) {
        $response = [
            'status' => 'success',
            'message' => 'Adres başarıyla güncellendi.'
        ];
    } else {
        $response['message'] = 'Adres güncellenirken bir hata oluştu: ' . mysqli_error($conn);
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
