<?php
session_start();
include "settings.php";

if (!isset($_SESSION['user_id'])) {
    echo "<p>Lütfen giriş yapınız.</p>";
    exit;
}

$customer_id = $_SESSION['user_id'];

$query = "SELECT a.*, i.il_adi, ilc.ilce_adi, s.semt_adi 
          FROM customer_addresses a
          LEFT JOIN iller i ON a.city = i.id
          LEFT JOIN ilceler ilc ON a.district = ilc.id
          LEFT JOIN semtler s ON a.semt = s.id
          WHERE a.customer_id = $customer_id
          ORDER BY a.created_at DESC";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo $row['address_title']; ?></h5>
                <p class="card-text">
                    <strong><?php echo $row['full_name']; ?></strong><br>
                    <?php echo $row['phone']; ?><br>
                    <?php echo $row['address_detail']; ?><br>
                    <?php echo $row['semt_adi'] . ', ' . $row['ilce_adi'] . ', ' . $row['il_adi']; ?>
                </p>
                <button class="btn btn-sm btn-primary edit-address" data-id="<?php echo $row['address_id']; ?>">Düzenle</button>
                <button class="btn btn-sm btn-danger delete-address" data-id="<?php echo $row['address_id']; ?>">Sil</button>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p>Henüz kayıtlı adresiniz bulunmuyor.</p>";
}
?>
