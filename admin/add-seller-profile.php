<?php
// Hata ayıklama için
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Veritabanı bağlantısını dahil et
include '../settings.php'; // Veritabanı bağlantınızın olduğu dosya

// Veritabanı bağlantısını kontrol et
if (!isset($conn) || $conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . ($conn->connect_error ?? "Bağlantı değişkeni tanımlanmamış"));
}

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form verilerini al
    $seller_name = $_POST['seller_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $seller_mail = $_POST['seller_mail'];
    // Adres ID'yi sabit 1 olarak ayarla
    $address_id = 1;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $role = $_POST['role'];
    
    // Hata mesajları için dizi
    $errors = [];
    
    // Parola kontrolü
    if ($password !== $confirm_password) {
        $errors[] = "Parolalar eşleşmiyor.";
    }
    
    // E-posta format kontrolü
    if (!filter_var($seller_mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Geçerli bir e-posta adresi giriniz.";
    }
    
    // E-posta benzersizlik kontrolü
    $checkEmailSQL = "SELECT seller_id FROM sellers WHERE seller_mail = ?";
    $stmt = $conn->prepare($checkEmailSQL);
    if (!$stmt) {
        $error = "Sorgu hazırlama hatası: " . $conn->error;
    } else {
        $stmt->bind_param("s", $seller_mail);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Bu e-posta adresi zaten kullanılmaktadır.";
        }
        $stmt->close();
        
        // Hata yoksa veritabanına ekle
        if (empty($errors)) {
            // Parolayı MD5 ile hashle
            $password_hashed = md5($password);
            
            // Varsayılan logo adı
            $seller_logo = "default.png";
            
            // Satıcı ekleme sorgusu
            $insertSQL = "INSERT INTO sellers (seller_name, password_hashed, seller_mail, seller_logo, address_id, is_active, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insertSQL);
            if (!$stmt) {
                $error = "Sorgu hazırlama hatası: " . $conn->error;
            } else {
                $stmt->bind_param("ssssiis", $seller_name, $password_hashed, $seller_mail, $seller_logo, $address_id, $is_active, $role);
                
                if ($stmt->execute()) {
                    $success = "Satıcı başarıyla eklendi.";
                } else {
                    $error = "Kayıt işlemi sırasında bir hata oluştu: " . $stmt->error;
                }
                
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satıcı Ekle - Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .logo-preview {
            max-height: 50px;
            max-width: 100%;
        }
        .current-logo {
            border: 2px solid #198754;
        }
        .main-content {
            padding: 20px;
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
                        <h2 class="mb-4">Yeni Satıcı Ekle</h2>
                        
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
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error_msg): ?>
                                        <li><?php echo $error_msg; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Satıcı Ekleme Formu -->
                        <div class="card">
                            <div class="card-header">
                                <h5>Satıcı Bilgileri</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="seller_name" class="form-label">Satıcı Adı*</label>
                                                <input type="text" class="form-control" id="seller_name" name="seller_name" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="seller_mail" class="form-label">E-posta Adresi*</label>
                                                <input type="email" class="form-control" id="seller_mail" name="seller_mail" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Parola*</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="confirm_password" class="form-label">Parola Tekrar*</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Rol*</label>
                                                <select class="form-select" id="role" name="role" required>
                                                    <option value="seller">Satıcı</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3 form-check mt-4">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                                <label class="form-check-label" for="is_active">Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-3">
                                        <a href="seller-list.php" class="btn btn-secondary me-2">İptal</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-1"></i> Satıcı Ekle
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i> Eklenen tüm satıcılar için varsayılan adres ID'si 1 olarak tanımlanacaktır.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
