<?php
include "settings.php";

if (isset($_POST['city_id'])) {
    $city_id = $_POST['city_id'];
    $query = "SELECT * FROM ilceler WHERE il_id = $city_id";
    $result = mysqli_query($conn, $query);

    echo '<option value="">İlçe Seçiniz</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['id'] . "'>" . $row['ilce_adi'] . "</option>";
    }
}
?>