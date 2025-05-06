<?php
// Veritabanı bağlantısı
include '../settings.php';

if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']);
    $path = [];
    
    // Kategoriden ana kategoriye kadar yolu bul
    while ($categoryId > 0) {
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            array_unshift($path, $categoryId); // Yolu başa ekle
            $categoryId = $row['category_parent_id'];
        } else {
            break;
        }
    }
    
    echo json_encode(['success' => true, 'path' => $path]);
} else {
    echo json_encode(['success' => false]);
}
?>
