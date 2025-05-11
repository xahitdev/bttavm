<?php
session_start();
require_once '../settings.php';

// Kategori ekleme
if (isset($_POST['add_category'])) {
    $name = $_POST['category_name'];
    $parent_id = $_POST['parent_id'];
    $icon = $_POST['icon'];
    $icon_color = $_POST['icon_color'];
    $order = $_POST['order'];
    
    $stmt = $conn->prepare("INSERT INTO categories (category_name, category_parent_id, category_icon, category_icon_color, category_order) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sissi", $name, $parent_id, $icon, $icon_color, $order);
    
    if ($stmt->execute()) {
        $success = "Kategori eklendi!";
    }
}

// Sıra güncelleme (AJAX)
if (isset($_POST['ajax']) && $_POST['ajax'] == 'update_order') {
    $id = $_POST['id'];
    $order = $_POST['order'];
    
    $stmt = $conn->prepare("UPDATE categories SET category_order = ? WHERE category_id = ?");
    $stmt->bind_param("ii", $order, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// Kategorileri getir
$categories = $conn->query("SELECT * FROM categories ORDER BY category_parent_id, category_order");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .order-input {
            width: 80px;
            text-align: center;
        }
        .updated {
            background-color: #d1e7dd !important;
            transition: background-color 0.5s;
        }
        .sub-category {
            padding-left: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <?php include 'sidebar-components.php'; ?>
            </div>
            
            <!-- Ana içerik -->
            <div class="col-md-9">
                <div class="p-4">
                    <h2>Kategori Yönetimi</h2>
                    
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <!-- Yeni Kategori Ekle -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Yeni Kategori Ekle</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="category_name" placeholder="Kategori Adı" required>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control" name="parent_id">
                                            <option value="0">Ana Kategori</option>
                                            <?php
                                            $categories->data_seek(0);
                                            while($cat = $categories->fetch_assoc()) {
                                                if($cat['category_parent_id'] == 0) {
                                                    echo "<option value='{$cat['category_id']}'>{$cat['category_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="icon" placeholder="İkon (fa-laptop)">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="color" class="form-control" name="icon_color" value="#000000">
                                    </div>
                                    <div class="col-md-1">
                                        <input type="number" class="form-control" name="order" placeholder="Sıra" value="0">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="add_category" class="btn btn-primary">Ekle</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Kategori Listesi -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Mevcut Kategoriler</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kategori Adı</th>
                                            <th>İkon</th>
                                            <th>Sıra</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $categories->data_seek(0);
                                        $cats_array = [];
                                        while($cat = $categories->fetch_assoc()) {
                                            $cats_array[$cat['category_id']] = $cat;
                                        }
                                        
                                        foreach($cats_array as $cat) {
                                            $is_sub = $cat['category_parent_id'] != 0;
                                            ?>
                                            <tr>
                                                <td><?php echo $cat['category_id']; ?></td>
                                                <td class="<?php echo $is_sub ? 'sub-category' : ''; ?>">
                                                    <?php if($is_sub) echo '↳ '; ?>
                                                    <?php echo $cat['category_name']; ?>
                                                </td>
                                                <td>
                                                    <?php if($cat['category_icon']): ?>
                                                        <i class="fas <?php echo $cat['category_icon']; ?>" style="color: <?php echo $cat['category_icon_color']; ?>"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           class="form-control order-input" 
                                                           value="<?php echo $cat['category_order']; ?>"
                                                           data-id="<?php echo $cat['category_id']; ?>"
                                                           min="0">
                                                </td>
                                                <td>
                                                    <a href="category-edit-detail.php?id=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Sıra değişikliği
        $('.order-input').on('change', function() {
            var id = $(this).data('id');
            var order = $(this).val();
            var input = $(this);
            
            $.ajax({
                url: 'category-edit.php',
                type: 'POST',
                data: {
                    ajax: 'update_order',
                    id: id,
                    order: order
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        input.addClass('updated');
                        setTimeout(function() {
                            input.removeClass('updated');
                        }, 1500);
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
