<?php
session_start();
require_once '../settings.php';

// AJAX isteği kontrolü
if (isset($_POST['category_id']) && isset($_POST['new_order'])) {
    $category_id = $_POST['category_id'];
    $new_order = $_POST['new_order'];
    
    // Güvenlik kontrolü - sayı olup olmadığını kontrol et
    if (!is_numeric($category_id) || !is_numeric($new_order)) {
        echo json_encode(['status' => 'error', 'message' => 'Geçersiz değerler']);
        exit;
    }
    
    // Veritabanı güncelleme
    $sql = "UPDATE categories SET category_order = ? WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $new_order, $category_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Sıra güncellendi']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Güncelleme hatası']);
    }
    
    $stmt->close();
    exit;
}

// Eğer AJAX değilse ana sayfaya yönlendir
header("Location: category-edit.php");
exit;
?>
