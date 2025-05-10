<?php
session_start();
require_once 'settings.php';

$response = [
    'status' => 'error',
    'message' => 'İşlem başarısız oldu.'
];

if (!isset($_SESSION['user_id']) || !isset($_POST['address_id'])) {
    $response['message'] = 'Geçersiz istek.';
    echo json_encode($response);
    exit;
}

$customer_id = $_SESSION['user_id'];
$address_id = (int)$_POST['address_id'];

// Adresin bu kullanıcıya ait olduğunu kontrol et
$query = "SELECT * FROM customer_addresses 
          WHERE address_id = $address_id AND customer_id = $customer_id";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $address = mysqli_fetch_assoc($result);
    $response = [
        'status' => 'success',
        'address' => $address
    ];
} else {
    $response['message'] = 'Adres bulunamadı veya size ait değil.';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
