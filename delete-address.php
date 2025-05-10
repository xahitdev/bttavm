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
    
    // Adresi sil
    $delete_query = "DELETE FROM customer_addresses WHERE address_id = $address_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $response = [
            'status' => 'success',
            'message' => 'Adres başarıyla silindi.'
        ];
    } else {
        $response['message'] = 'Adres silinirken bir hata oluştu.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
