<?php
// Hata ayıklama
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../settings.php';

// Tablonun doğru adı
$table_name = 'index_images';
$primary_key = 'index_image_id';

// Güncelleme işlemi
if (isset($_POST['update_image'])) {
    $image_id = $_POST['image_id'];
    $image_type = $_POST['image_type'];
    
    // Dosya yükleme işlemi
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image_file']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed)) {
            // Yeni dosya adı oluştur
            $new_filename = 'banner_' . time() . '.' . $file_ext;
            
            // Ana sayfa resimleri için standart bir isim kullan
            if ($image_type == 'index_slider_image') {
                $new_filename = 'banner_1.webp';
            } else if ($image_type == 'index_slider_image_2') {
                $new_filename = 'banner_2.webp';
            } else if ($image_type == 'index_slider_image_3') {
                $new_filename = 'banner_3.webp';
            } else if ($image_type == 'card_image') {
                $new_filename = 'iphone.webp';
            } else if ($image_type == 'card_image2') {
                $new_filename = 'matkap.webp';
            }
            
            $upload_path = '../img/';
            
            // Dizin yoksa oluştur
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            // Dosya yükleme işlemi
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path . $new_filename)) {
                // Veritabanını güncelle
                $new_path = 'img/' . $new_filename;
                
                try {
                    $update_query = "UPDATE $table_name SET `$image_type` = ? WHERE $primary_key = ?";
                    $stmt = $conn->prepare($update_query);
                    
                    if (!$stmt) {
                        die("SQL hazırlama hatası: " . $conn->error);
                    }
                    
                    $stmt->bind_param("si", $new_path, $image_id);
                    
                    if ($stmt->execute()) {
                        $success = "Resim başarıyla güncellendi!";
                    } else {
                        $error = "Veritabanı güncellenirken hata oluştu: " . $stmt->error;
                    }
                } catch (Exception $e) {
                    $error = "SQL hatası: " . $e->getMessage();
                }
            } else {
                $error = "Dosya yüklenirken hata oluştu: " . $_FILES['image_file']['error'];
            }
        } else {
            $error = "Geçersiz dosya formatı! Sadece JPG, PNG, GIF ve WEBP dosyaları yükleyebilirsiniz.";
        }
    } else {
        $error = "Lütfen bir dosya seçin!";
    }
}

// Mevcut resimleri getir
try {
    $images_query = "SELECT * FROM $table_name WHERE $primary_key = 1";
    $images_result = $conn->query($images_query);
    
    if (!$images_result) {
        die("Sorgu hatası: " . $conn->error);
    }
    
    $images = $images_result->fetch_assoc();
    
    if (!$images) {
        die("Veritabanında kayıt bulunamadı. İlk kaydı oluşturun.");
    }
} catch (Exception $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Tüm resim alanlarını ayarla
$image_fields = [
    'index_slider_image' => [
        'title' => 'Slider Resmi 1',
        'description' => 'Ana sayfadaki ilk slider resmi'
    ],
    'index_slider_image_2' => [
        'title' => 'Slider Resmi 2',
        'description' => 'Ana sayfadaki ikinci slider resmi'
    ],
    'index_slider_image_3' => [
        'title' => 'Slider Resmi 3',
        'description' => 'Ana sayfadaki üçüncü slider resmi'
    ],
    'card_image' => [
        'title' => 'Kart Resmi 1',
        'description' => 'Ana sayfadaki ilk kart resmi'
    ],
    'card_image2' => [
        'title' => 'Kart Resmi 2',
        'description' => 'Ana sayfadaki ikinci kart resmi'
    ]
];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa Resimleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .image-preview {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            background-color: #f8f9fa;
            margin-bottom: 15px;
        }
        .card {
            margin-bottom: 20px;
        }
        .image-card {
            transition: all 0.3s;
        }
        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
                        <h2 class="mb-4">Ana Sayfa Resimleri</h2>
                        
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
                        
                        <div class="row">
                            <?php foreach ($image_fields as $field => $info): ?>
                                <div class="col-md-4">
                                    <div class="card image-card">
                                        <div class="card-header">
                                            <h5><?php echo $info['title']; ?></h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="text-muted"><?php echo $info['description']; ?></p>
                                            <img src="../<?php echo $images[$field]; ?>" alt="<?php echo $info['title']; ?>" class="image-preview">
                                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo str_replace('_', '', $field); ?>">
                                                <i class="fas fa-edit"></i> Resimi Değiştir
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Modal for image update -->
                                <div class="modal fade" id="imageModal<?php echo str_replace('_', '', $field); ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?php echo $info['title']; ?> Güncelle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="image_id" value="1">
                                                    <input type="hidden" name="image_type" value="<?php echo $field; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Mevcut Resim</label>
                                                        <div>
                                                            <img src="../<?php echo $images[$field]; ?>" alt="<?php echo $info['title']; ?>" class="image-preview">
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Yeni Resim</label>
                                                        <input type="file" class="form-control" name="image_file" accept="image/*" required>
                                                        <small class="form-text text-muted">
                                                            Önerilen boyut: 
                                                            <?php 
                                                            if ($field == 'index_slider_image' || $field == 'index_slider_image_2' || $field == 'index_slider_image_3') {
                                                                echo '1920x600 piksel';
                                                            } else {
                                                                echo '600x400 piksel';
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                                    <button type="submit" name="update_image" class="btn btn-primary">Güncelle</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> Önemli: Ana sayfa resimlerini değiştirdiğinizde, eski resimler üzerine yazılır. Yedek almak istiyorsanız, önce mevcut resimleri bilgisayarınıza kaydedin.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
