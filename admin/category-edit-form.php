<?php
session_start();
require_once '../settings.php';

// Kategori ID'si kontrolü
if (!isset($_GET['id'])) {
    header("Location: category-edit.php");
    exit;
}

$category_id = $_GET['id'];

// Kategori güncelleme işlemi
if (isset($_POST['update_category'])) {
    $category_name = $_POST['category_name'];
    $category_parent_id = $_POST['category_parent_id'];
    $category_icon = $_POST['category_icon'];
    $category_icon_color = $_POST['category_icon_color'];
    $category_order = $_POST['category_order'];
    
    $sql = "UPDATE categories SET category_name = ?, category_parent_id = ?, category_icon = ?, 
            category_icon_color = ?, category_order = ? WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissii", $category_name, $category_parent_id, $category_icon, $category_icon_color, $category_order, $category_id);
    
    if ($stmt->execute()) {
        $success = "Kategori başarıyla güncellendi!";
    } else {
        $error = "Kategori güncellenirken hata oluştu!";
    }
}

// Mevcut kategori bilgilerini getir
$sql = "SELECT * FROM categories WHERE category_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    header("Location: category-edit.php");
    exit;
}

// Ana kategorileri getir (dropdown için)
$main_categories_sql = "SELECT * FROM categories WHERE category_parent_id = 0 AND category_id != ? ORDER BY category_order, category_name";
$main_stmt = $conn->prepare($main_categories_sql);
$main_stmt->bind_param("i", $category_id);
$main_stmt->execute();
$main_categories_result = $main_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Düzenle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Kategori Düzenle</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Ana Sayfa</a></li>
                                <li class="breadcrumb-item"><a href="category-edit.php">Kategori Yönetimi</a></li>
                                <li class="breadcrumb-item">Kategori Düzenle</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Başarı ve Hata Mesajları -->
            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Kategori Düzenleme Formu -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Kategori Bilgilerini Düzenle</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Kategori Adı</label>
                                            <input type="text" class="form-control" name="category_name" 
                                                   value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Ana Kategori</label>
                                            <select class="form-control" name="category_parent_id">
                                                <option value="0" <?php echo $category['category_parent_id'] == 0 ? 'selected' : ''; ?>>
                                                    Ana Kategori
                                                </option>
                                                <?php while($main_cat = $main_categories_result->fetch_assoc()): ?>
                                                    <option value="<?php echo $main_cat['category_id']; ?>"
                                                            <?php echo $category['category_parent_id'] == $main_cat['category_id'] ? 'selected' : ''; ?>>
                                                        <?php echo $main_cat['category_name']; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Sıra</label>
                                            <input type="number" class="form-control" name="category_order" 
                                                   value="<?php echo $category['category_order']; ?>" min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">İkon (Font Awesome)</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas <?php echo $category['category_icon']; ?>" 
                                                       style="color: <?php echo $category['category_icon_color']; ?>;" id="icon-preview"></i>
                                                </span>
                                                <input type="text" class="form-control" name="category_icon" 
                                                       value="<?php echo htmlspecialchars($category['category_icon']); ?>" 
                                                       placeholder="fa-laptop" id="icon-input">
                                            </div>
                                            <small class="form-text text-muted">
                                                Font Awesome ikon kodu (örn: fa-laptop, fa-store, fa-tshirt)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">İkon Rengi</label>
                                            <input type="color" class="form-control" name="category_icon_color" 
                                                   value="<?php echo $category['category_icon_color'] ?: '#007bff'; ?>" id="color-input">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mevcut Alt Kategoriler -->
                                <?php
                                $sub_categories_sql = "SELECT * FROM categories WHERE category_parent_id = ? ORDER BY category_order, category_name";
                                $sub_stmt = $conn->prepare($sub_categories_sql);
                                $sub_stmt->bind_param("i", $category_id);
                                $sub_stmt->execute();
                                $sub_categories_result = $sub_stmt->get_result();
                                
                                if ($sub_categories_result->num_rows > 0):
                                ?>
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <h6>Alt Kategoriler:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Kategori Adı</th>
                                                        <th>Sıra</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while($sub_cat = $sub_categories_result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo $sub_cat['category_id']; ?></td>
                                                            <td><?php echo $sub_cat['category_name']; ?></td>
                                                            <td><?php echo $sub_cat['category_order']; ?></td>
                                                            <td>
                                                                <a href="category-edit-form.php?id=<?php echo $sub_cat['category_id']; ?>" 
                                                                   class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button type="submit" name="update_category" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Güncelle
                                        </button>
                                        <a href="category-edit.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Geri Dön
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../assets/js/plugins/jquery.min.js"></script>
    <script src="../assets/js/plugins/bootstrap.min.js"></script>
    <script>
        // İkon önizleme
        document.getElementById('icon-input').addEventListener('input', function() {
            const iconPreview = document.getElementById('icon-preview');
            iconPreview.className = 'fas ' + this.value;
        });
        
        // Renk önizleme
        document.getElementById('color-input').addEventListener('input', function() {
            const iconPreview = document.getElementById('icon-preview');
            iconPreview.style.color = this.value;
        });
    </script>
</body>
</html>
