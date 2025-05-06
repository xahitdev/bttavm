<?php
// Veritabanı bağlantısı
include '../settings.php';

if (isset($_GET['parent_id']) && is_numeric($_GET['parent_id'])) {
    $parentId = intval($_GET['parent_id']);
    $level = isset($_GET['level']) ? intval($_GET['level']) : 0;
    
    // Alt kategorileri getir
    $sql = "SELECT * FROM categories WHERE category_parent_id = ? ORDER BY category_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $parentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo '<select class="form-control mb-2" id="subcategory-level-' . $level . '" name="subcategory_' . $level . '">';
        echo '<option value="">-- Select Subcategory --</option>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['category_id'] . '">' . htmlspecialchars($row['category_name']) . '</option>';
        }
        
        echo '</select>';
    }
}
?>
