<?php
// Veritabanı bağlantısı
require_once '../settings.php';

// Oturum kontrolü
session_start();
if (!isset($_SESSION['seller_id'])) {
  header("Location: login.php");
  exit;
}

$seller_id = $_SESSION['seller_id'];

// ID'yi al ve güvenli hale getir
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $product_id = intval($_GET['id']);

  // Ürünün satıcıya ait olduğunu doğrula (güvenlik için önemli!)
  $checkSQL = "SELECT product_id FROM products WHERE product_id = ? AND seller_id = ?";
  $stmt = $conn->prepare($checkSQL);
  $stmt->bind_param("ii", $product_id, $seller_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Ürünü fiziksel olarak silmek yerine is_deleted alanını 1 yap
    $deleteSQL = "UPDATE products SET is_deleted = 1 WHERE product_id = ?";
    $stmt = $conn->prepare($deleteSQL);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
      $_SESSION['success_message'] = "Ürün başarıyla silindi.";
    } else {
      $_SESSION['error_message'] = "Ürün silinirken bir hata oluştu.";
    }
  } else {
    $_SESSION['error_message'] = "Bu ürünü silme yetkiniz bulunmuyor.";
  }

  // Ürün listesine geri dön
  header("Location: seller-panel.php");
  exit;
} else {
  $_SESSION['error_message'] = "Geçersiz ürün ID'si.";
  header("Location: seller-panel.php");
  exit;
}
?>