<?php
include "settings.php";

if (isset($_POST['district_id'])) {
    $district_id = $_POST['district_id'];
    $query = "SELECT * FROM semtler WHERE ilce_id = $district_id";
    $result = mysqli_query($conn, $query);

    echo '<option value="">Semt Seçiniz</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['id'] . "'>" . $row['semt_adi'] . "</option>";
    }
}
?>