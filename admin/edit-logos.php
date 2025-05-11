<?php
session_start();
require_once '../settings.php';

// Logo güncelleme işlemi
if (isset($_POST['update_logo'])) {
    $logo_id = $_POST['logo_id'];
    
    // Dosya yükleme işlemi
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo_file']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            // Yeni dosya adı oluştur
            $new_filename = 'logo_' . time() . '.' . $file_ext;
            $upload_path = '../img/';
            
            // Dizin yoksa oluştur
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            // Dosya yükleme işlemi
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $upload_path . $new_filename)) {
                // Eski logoyu sil (opsiyonel)
                $old_logo_query = "SELECT navbar_logo FROM logos WHERE logo_id = ?";
                $stmt = $conn->prepare($old_logo_query);
                $stmt->bind_param("i", $logo_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_logo = $result->fetch_assoc();
                
                if ($old_logo && file_exists('../' . $old_logo['navbar_logo'])) {
                    unlink('../' . $old_logo['navbar_logo']);
                }
                
                // Veritabanını güncelle
                $new_path = 'img/' . $new_filename;
                $update_query = "UPDATE logos SET navbar_logo = ? WHERE logo_id = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("si", $new_path, $logo_id);
                
                if ($stmt->execute()) {
                    $success = "Logo başarıyla güncellendi!";
                } else {
                    $error = "Veritabanı güncellenirken hata oluştu!";
                }
            } else {
                $error = "Dosya yüklenirken hata oluştu!";
            }
        } else {
            $error = "Geçersiz dosya formatı! Sadece JPG, PNG ve GIF dosyaları yükleyebilirsiniz.";
        }
    } else {
        $error = "Lütfen bir dosya seçin!";
    }
}

// Yeni logo ekleme
if (isset($_POST['add_logo'])) {
    if (isset($_FILES['new_logo_file']) && $_FILES['new_logo_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['new_logo_file']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            $new_filename = 'logo_' . time() . '.' . $file_ext;
            $upload_path = '../img/';
            
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['new_logo_file']['tmp_name'], $upload_path . $new_filename)) {
                $new_path = 'img/' . $new_filename;
                $insert_query = "INSERT INTO logos (navbar_logo) VALUES (?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("s", $new_path);
                
                if ($stmt->execute()) {
                    $success = "Yeni logo başarıyla eklendi!";
                } else {
                    $error = "Logo eklenirken hata oluştu!";
                }
            }
        } else {
            $error = "Geçersiz dosya formatı!";
        }
    }
}

// Logo silme
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    
    // Önce dosyayı sil
    $get_logo = "SELECT navbar_logo FROM logos WHERE logo_id = ?";
    $stmt = $conn->prepare($get_logo);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $logo = $result->fetch_assoc();
    
    if ($logo && file_exists('../' . $logo['navbar_logo'])) {
        unlink('../' . $logo['navbar_logo']);
    }
    
    // Veritabanından sil
    $delete_query = "DELETE FROM logos WHERE logo_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $success = "Logo başarıyla silindi!";
    } else {
        $error = "Logo silinirken hata oluştu!";
    }
}

// Mevcut logoları getir
$logos_query = "SELECT * FROM logos";
$logos_result = $conn->query($logos_query);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logo Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            object-fit: contain;
            border: 1px solid #ddd;
            padding: 10px;
            background: #f8f9fa;
        }
        .current-logo {
            border: 3px solid #0d6efd;
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
                        <h2 class="mb-4">Logo Yönetimi</h2>
                        
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
                        
                        <!-- Yeni Logo Ekle -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Yeni Logo Ekle</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Logo Dosyası</label>
                                                <input type="file" class="form-control" name="new_logo_file" accept="image/*" required>
                                                <small class="form-text text-muted">JPG, PNG veya GIF formatında olmalıdır.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" name="add_logo" class="btn btn-primary d-block w-100">
                                                    <i class="fas fa-plus"></i> Logo Ekle
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Mevcut Logolar -->
                        <div class="card">
                            <div class="card-header">
                                <h5>Mevcut Logolar</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Logo Önizleme</th>
                                                <th>Dosya Yolu</th>
                                                <th>Durum</th>
                                                <th>İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($logo = $logos_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $logo['logo_id']; ?></td>
                                                    <td>
                                                        <img src="../<?php echo $logo['navbar_logo']; ?>" 
                                                             alt="Logo <?php echo $logo['logo_id']; ?>" 
                                                             class="logo-preview <?php echo $logo['logo_id'] == 1 ? 'current-logo' : ''; ?>">
                                                    </td>
                                                    <td><?php echo $logo['navbar_logo']; ?></td>
                                                    <td>
                                                        <?php if($logo['logo_id'] == 1): ?>
                                                            <span class="badge bg-success">Aktif Logo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Pasif</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $logo['logo_id']; ?>">
                                                            <i class="fas fa-edit"></i> Değiştir
                                                        </button>
                                                        <?php if($logo['logo_id'] != 1): ?>
                                                            <a href="?delete=<?php echo $logo['logo_id']; ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Bu logoyu silmek istediğinize emin misiniz?')">
                                                                <i class="fas fa-trash"></i> Sil
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Güncelleme Modal -->
                                                <div class="modal fade" id="updateModal<?php echo $logo['logo_id']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Logo Güncelle</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST" enctype="multipart/form-data">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="logo_id" value="<?php echo $logo['logo_id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Mevcut Logo</label>
                                                                        <div class="mb-2">
                                                                            <img src="../<?php echo $logo['navbar_logo']; ?>" 
                                                                                 alt="Current Logo" 
                                                                                 class="logo-preview">
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Yeni Logo Dosyası</label>
                                                                        <input type="file" class="form-control" name="logo_file" accept="image/*" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                                    <button type="submit" name="update_logo" class="btn btn-primary">Güncelle</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> Logo ID'si 1 olan logo, sitenizde aktif olarak kullanılan logodur. Bu logoyu değiştirebilir ancak silemezsiniz.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
