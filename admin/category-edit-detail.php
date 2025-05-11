<?php
session_start();
require_once '../settings.php';

// ID kontrolü
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: category-edit.php");
    exit;
}

$category_id = intval($_GET['id']);

// Kategori güncelleme işlemi
if (isset($_POST['update_category'])) {
    $name = $_POST['category_name'];
    $parent_id = $_POST['parent_id'];
    $icon = $_POST['icon'];
    $icon_color = $_POST['icon_color'];
    $order = $_POST['order'];
    
    $stmt = $conn->prepare("UPDATE categories SET category_name = ?, category_parent_id = ?, category_icon = ?, category_icon_color = ?, category_order = ? WHERE category_id = ?");
    $stmt->bind_param("sissii", $name, $parent_id, $icon, $icon_color, $order, $category_id);
    
    if ($stmt->execute()) {
        $success = "Kategori başarıyla güncellendi!";
    } else {
        $error = "Güncelleme sırasında hata oluştu!";
    }
}

// Mevcut kategori bilgilerini getir
$stmt = $conn->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: category-edit.php");
    exit;
}

$category = $result->fetch_assoc();

// Ana kategorileri getir (dropdown için)
$parent_categories = $conn->query("SELECT * FROM categories WHERE category_parent_id = 0 AND category_id != $category_id ORDER BY category_order");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Düzenle - <?php echo htmlspecialchars($category['category_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .icon-preview {
            font-size: 2rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <?php include 'sidebar-components.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Kategori Düzenle: <?php echo htmlspecialchars($category['category_name']); ?></h2>
                            <a href="category-edit.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri Dön
                            </a>
                        </div>
                        
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
                        
                        <div class="card">
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kategori Adı</label>
                                                <input type="text" class="form-control" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Ana Kategori</label>
                                                <select class="form-control" name="parent_id">
                                                    <option value="0" <?php echo $category['category_parent_id'] == 0 ? 'selected' : ''; ?>>Ana Kategori</option>
                                                    <?php while($parent = $parent_categories->fetch_assoc()): ?>
                                                        <option value="<?php echo $parent['category_id']; ?>" <?php echo $category['category_parent_id'] == $parent['category_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($parent['category_name']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">İkon (Font Awesome Class)</label>
                                                <input type="text" class="form-control" id="icon-input" name="icon" value="<?php echo htmlspecialchars($category['category_icon']); ?>" placeholder="Örn: fa-laptop">
                                                <small class="form-text text-muted">Font Awesome ikon sınıfını girin (fa-laptop, fa-store, vb.)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">İkon Rengi</label>
                                                <input type="color" class="form-control" id="color-input" name="icon_color" value="<?php echo $category['category_icon_color'] ?: '#000000'; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">Sıra</label>
                                                <input type="number" class="form-control" name="order" value="<?php echo $category['category_order']; ?>" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">İkon Önizleme</label>
                                                <div class="icon-preview">
                                                    <i class="fas <?php echo $category['category_icon']; ?>" id="icon-preview" style="color: <?php echo $category['category_icon_color'] ?: '#000000'; ?>;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button type="submit" name="update_category" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Güncelle
                                            </button>
                                            <a href="category-edit.php" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> İptal
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // İkon önizleme
        document.getElementById('icon-input').addEventListener('input', function() {
            var icon = document.getElementById('icon-preview');
            icon.className = 'fas ' + this.value;
        });
        
        // Renk önizleme
        document.getElementById('color-input').addEventListener('input', function() {
            document.getElementById('icon-preview').style.color = this.value;
        });
    </script>
</body>
</html>
