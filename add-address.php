<?php
session_start();
include "settings.php";

$response = ["status" => "error", "message" => "İşlem başarısız."];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = "Lütfen giriş yapınız.";
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_SESSION['user_id'];
    $address_title = mysqli_real_escape_string($conn, $_POST['address_title']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $semt = mysqli_real_escape_string($conn, $_POST['semt']);
    $address_detail = mysqli_real_escape_string($conn, $_POST['address_detail']);
    
    $query = "INSERT INTO customer_addresses (customer_id, address_title, full_name, phone, city, district, semt, address_detail, created_at) 
              VALUES ($customer_id, '$address_title', '$full_name', '$phone', '$city', '$district', '$semt', '$address_detail', NOW())";
    
    if (mysqli_query($conn, $query)) {
        $response = ["status" => "success", "message" => "Adres başarıyla eklendi."];
    } else {
        $response['message'] = "Veritabanı hatası: " . mysqli_error($conn);
    }
}

echo json_encode($response);
?>
