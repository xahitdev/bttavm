<?php
error_reporting(E_ALL & ~E_NOTICE);

session_start();
ob_start();

/* error_reporting(E_NOTICE); */

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>E Store - eCommerce HTML Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="eCommerce HTML Template Free Download" name="keywords">
    <meta content="eCommerce HTML Template Free Download" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400|Source+Code+Pro:700,900&display=swap"
        rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="lib/slick/slick.css" rel="stylesheet">
    <link href="lib/slick/slick-theme.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <?php
    include 'navbar.php';
    ?>
    <!-- Breadcrumb Start -->
    <div class="breadcrumb-wrap">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Ana sayfa</a></li>
                <li class="breadcrumb-item"><a href="#">Hesabım</a></li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- My Account Start -->
    <div class="my-account">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="dashboard-nav" data-toggle="pill" href="#dashboard-tab"
                            role="tab"><i class="fa fa-tachometer-alt"></i>Kullanıcı Profili</a>
                        <a class="nav-link" id="orders-nav" data-toggle="pill" href="#orders-tab" role="tab"><i
                                class="fa fa-shopping-bag"></i>Siparişlerim</a>
                        <a class="nav-link" id="address-nav" data-toggle="pill" href="#address-tab" role="tab"><i
                                class="fa fa-map-marker-alt"></i>Adres</a>
                        <a class="nav-link" id="account-nav" data-toggle="pill" href="#account-tab" role="tab"><i
                                class="fa fa-user"></i>Hesap Detayları</a>
                        <a class="nav-link" href="index.php"><i class="fa fa-sign-out-alt"></i>Çıkış yap.</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="dashboard-tab" role="tabpanel"
                            aria-labelledby="dashboard-nav">
                            <h4>Kullanıcı Profili</h4>
                            <p>
														Kullanıcı paneline hoş geldiniz. Bu kısımda profilinizi düzenleyebilirsiniz.
                            </p>
                        </div>
												<div class="tab-pane fade" id="orders-tab" role="tabpanel" aria-labelledby="orders-nav">
														<div class="table-responsive">
																<table class="table table-bordered">
																		<thead class="thead-dark">
																				<tr>
																						<th>No</th>
																						<th>Sipariş No</th>
																						<th>Tarih</th>
																						<th>Toplam</th>
																						<th>Durum</th>
																						<th>İşlem</th>
																				</tr>
																		</thead>
																		<tbody>
																				<?php
																				$customer_id = $_SESSION['user_id'];
																				
																				$orders_query = "SELECT * FROM orders 
																												WHERE customer_id = $customer_id 
																												ORDER BY created_at DESC";
																				
																				$orders_result = mysqli_query($conn, $orders_query);
																				
																				if (mysqli_num_rows($orders_result) > 0) {
																						$counter = 1;
																						while ($order = mysqli_fetch_assoc($orders_result)) {
																								// Durum çevirileri
																								$status_labels = [
																										'pending' => '<span class="badge badge-warning">Beklemede</span>',
																										'processing' => '<span class="badge badge-info">İşleniyor</span>',
																										'shipped' => '<span class="badge badge-primary">Kargoda</span>',
																										'delivered' => '<span class="badge badge-success">Teslim Edildi</span>',
																										'cancelled' => '<span class="badge badge-danger">İptal Edildi</span>'
																								];
																								
																								$status = $status_labels[$order['order_status']] ?? $order['order_status'];
																								$date = date('d.m.Y', strtotime($order['created_at']));
																								
																								echo "<tr>
																												<td>$counter</td>
																												<td>{$order['order_number']}</td>
																												<td>$date</td>
																												<td>{$order['total_amount']} TL</td>
																												<td>$status</td>
																												<td><button class='btn view-order' data-order-id='{$order['order_id']}'>Görüntüle</button></td>
																											</tr>";
																								$counter++;
																						}
																				} else {
																						echo "<tr><td colspan='6' class='text-center'>Henüz siparişiniz bulunmuyor.</td></tr>";
																				}
																				?>
																		</tbody>
																</table>
														</div>
												</div>
												<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
														<div class="modal-dialog modal-lg" role="document">
																<div class="modal-content">
																		<div class="modal-header">
																				<h5 class="modal-title" id="orderDetailsModalLabel">Sipariş Detayları</h5>
																				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																						<span aria-hidden="true">&times;</span>
																				</button>
																		</div>
																		<div class="modal-body" id="orderDetailsContent">
																				<!-- Sipariş detayları buraya yüklenecek -->
																		</div>
																		<div class="modal-footer">
																				<button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
																		</div>
																</div>
														</div>
												</div>
                        <div class="tab-pane fade" id="payment-tab" role="tabpanel" aria-labelledby="payment-nav">
                            <h4>Payment Method</h4>
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. In condimentum quam ac mi
                                viverra dictum. In efficitur ipsum diam, at dignissim lorem tempor in. Vivamus tempor
                                hendrerit finibus. Nulla tristique viverra nisl, sit amet bibendum ante suscipit non.
                                Praesent in faucibus tellus, sed gravida lacus. Vivamus eu diam eros. Aliquam et sapien
                                eget arcu rhoncus scelerisque.
                            </p>
                        </div>
                        <div class="tab-pane fade" id="address-tab" role="tabpanel" aria-labelledby="address-nav">
												<?php
												// Kullanıcının adreslerini çek
												$customer_id = $_SESSION['user_id'];

												// En son eklenen adresi getir
												$query = "SELECT a.*, i.il_adi, ilc.ilce_adi, s.semt_adi 
																	FROM customer_addresses a
																	LEFT JOIN iller i ON a.city = i.id
																	LEFT JOIN ilceler ilc ON a.district = ilc.id
																	LEFT JOIN semtler s ON a.semt = s.id
																	WHERE a.customer_id = $customer_id
																	ORDER BY a.created_at DESC
																	LIMIT 1";

												$result = mysqli_query($conn, $query);
												$address = mysqli_fetch_assoc($result);
												?>

												<h4>Address</h4>
												<div class="row">
														<div class="col-md-12">
																<?php if ($address): ?>
																		<h5><?php echo htmlspecialchars($address['address_title'] ?: 'Adresim'); ?></h5>
																		<p>
																				<?php echo htmlspecialchars($address['full_name']); ?><br>
																				<?php echo htmlspecialchars($address['address_detail']); ?><br>
																				<?php 
																						$full_address = [];
																						if (!empty($address['semt_adi'])) $full_address[] = $address['semt_adi'];
																						if (!empty($address['ilce_adi'])) $full_address[] = $address['ilce_adi'];
																						if (!empty($address['il_adi'])) $full_address[] = $address['il_adi'];
																						echo implode(', ', $full_address);
																				?>
																		</p>
																		<p>Telefon: <?php echo htmlspecialchars($address['phone']); ?></p>
																<?php else: ?>
																		<p>Henüz adres eklenmemiş.</p>
																<?php endif; ?>
																<button class="btn" data-toggle="pill" href="#address-edit-tab" role="tab">Adresi Düzenle</button>
														</div>
												</div>
                        </div>
												<div class="tab-pane fade" id="address-edit-tab" role="tabpanel" aria-labelledby="address-nav">
														<div class="row">
																<!-- Mevcut adresler listesi -->
																<div class="col-md-12 mb-4">
																		<h4>Kayıtlı Adreslerim</h4>
																		<div id="addresses-list">
																				<!-- AJAX ile yüklenecek -->
																		</div>
																</div>
																
																<!-- Yeni adres ekleme formu -->
																<div class="col-md-12">
																		<h4>Yeni Adres Ekle</h4>
																		<form id="address-form">
																				<input type="hidden" id="address_id" value="">
																				<div class="mb-3">
																						<label class="form-label">Adres Başlığı:</label>
																						<input type="text" id="address_title" class="form-control" placeholder="Örn: Ev, İş">
																				</div>
																				<div class="mb-3">
																						<label class="form-label">Ad Soyad:</label>
																						<input type="text" id="full_name" class="form-control">
																				</div>
																				<div class="mb-3">
																						<label class="form-label">Telefon:</label>
																						<input type="text" id="phone" class="form-control">
																				</div>
																				<div class="mb-3">
																						<label class="form-label">İl:</label>
																						<select id="city" class="form-select">
																								<option value="">İl Seçiniz</option>
																								<?php
																								$result = mysqli_query($conn, "SELECT * FROM iller");
																								while ($row = mysqli_fetch_assoc($result)) {
																										echo "<option value='" . $row['id'] . "'>" . $row['il_adi'] . "</option>";
																								}
																								?>
																						</select>
																				</div>
																				<div class="mb-3">
																						<label class="form-label">İlçe:</label>
																						<select id="district" class="form-select" disabled>
																								<option value="">Önce il seçiniz...</option>
																						</select>
																				</div>
																				<div class="mb-3">
																						<label class="form-label">Semt:</label>
																						<select id="semt" class="form-select" disabled>
																								<option value="">Önce ilçe seçiniz...</option>
																						</select>
																				</div>
																				<div class="mb-3">
																						<label class="form-label">Açık Adres:</label>
																						<textarea id="address_detail" class="form-control" rows="3" placeholder="Mahalle, sokak, bina no vb."></textarea>
																				</div>
																				<button type="submit" class="btn btn-primary">Adresi Kaydet</button>
																		</form>
																</div>
														</div>
												</div>
												<div class="tab-pane fade" id="account-tab" role="tabpanel" aria-labelledby="account-nav">
														<?php
														// Mevcut kullanıcı bilgilerini getir
														$customer_id = $_SESSION['user_id'];
														$query = "SELECT * FROM customers WHERE customer_id = $customer_id";
														$result = mysqli_query($conn, $query);
														$customer = mysqli_fetch_assoc($result);
														?>
														
														<h4>Account Details</h4>
														<form id="update-account-form">
																<div class="row">
																		<div class="col-md-6">
																				<label>İsim</label>
																				<input class="form-control" type="text" id="customer_name" value="<?php echo htmlspecialchars($customer['customer_name']); ?>">
																		</div>
																		<div class="col-md-6">
																				<label>Telefon</label>
																				<input class="form-control" type="text" id="customer_phone" value="<?php echo htmlspecialchars($customer['customer_phone']); ?>">
																		</div>
																		<div class="col-md-6">
																				<label>Email</label>
																				<input class="form-control" type="email" id="customer_mail" value="<?php echo htmlspecialchars($customer['customer_mail']); ?>">
																		</div>
																		<div class="col-md-6">
																				<label>Doğum Tarihi</label>
																				<input class="form-control" type="date" id="customer_birth" value="<?php echo htmlspecialchars($customer['customer_birth']); ?>">
																		</div>
																		<div class="col-md-6">
																				<label>Cinsiyet</label>
																				<select class="form-control" id="customer_gender">
																						<option value="0" <?php echo $customer['customer_gender'] == 0 ? 'selected' : ''; ?>>Kadın</option>
																						<option value="1" <?php echo $customer['customer_gender'] == 1 ? 'selected' : ''; ?>>Erkek</option>
																				</select>
																		</div>
																		<div class="col-md-12 mt-3">
																				<button type="submit" class="btn">Bilgileri Güncelle</button>
																				<br><br>
																		</div>
																</div>
														</form>
														
														<h4>Şifre Değiştir</h4>
														<form id="change-password-form">
																<div class="row">
																		<div class="col-md-12">
																				<label>Mevcut Şifre</label>
																				<input class="form-control" type="password" id="current_password" placeholder="Mevcut şifrenizi girin">
																		</div>
																		<div class="col-md-6">
																				<label>Yeni Şifre</label>
																				<input class="form-control" type="password" id="new_password" placeholder="Yeni şifre">
																		</div>
																		<div class="col-md-6">
																				<label>Yeni Şifre (Tekrar)</label>
																				<input class="form-control" type="password" id="confirm_password" placeholder="Yeni şifre tekrar">
																		</div>
																		<div class="col-md-12 mt-3">
																				<button type="submit" class="btn">Şifreyi Değiştir</button>
																		</div>
																</div>
														</form>
												</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- My Account End -->

		<?php
		include 'footer.php';
		?>

    <!-- Back to Top -->
    <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/slick/slick.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
		<script>
			$(document).ready(function () {
					// İl seçimi değiştiğinde
					$("#city").change(function () {
							var cityId = $(this).val();
							$("#district").html('<option value="">İlçeler Yükleniyor...</option>').prop("disabled", true);
							$("#semt").html('<option value="">Önce ilçe seçiniz...</option>').prop("disabled", true);
							
							if (cityId !== "") {
									$.ajax({
											url: "get_districts.php",
											type: "POST",
											data: { city_id: cityId },
											success: function (data) {
													$("#district").html(data).prop("disabled", false);
											}
									});
							}
					});
					
					// İlçe seçimi değiştiğinde
					$("#district").change(function () {
							var districtId = $(this).val();
							$("#semt").html('<option value="">Semtler Yükleniyor...</option>').prop("disabled", true);
							
							if (districtId !== "") {
									$.ajax({
											url: "get_semt.php",
											type: "POST",
											data: { district_id: districtId },
											success: function (data) {
													$("#semt").html(data).prop("disabled", false);
											}
									});
							}
					});
					
					// Sayfa yüklendiğinde adresleri getir
					loadAddresses();
					
					// Adres formu gönderildiğinde
					$("#address-form").submit(function(e) {
							e.preventDefault();
							
							// Form verilerini al
							var formData = {
									address_id: $("#address_id").val(),
									address_title: $("#address_title").val(),
									full_name: $("#full_name").val(),
									phone: $("#phone").val(),
									city: $("#city").val(),
									district: $("#district").val(),
									semt: $("#semt").val(),
									address_detail: $("#address_detail").val()
							};
							
							// Boş alan kontrolü
							if (!formData.full_name || !formData.phone || !formData.city || !formData.district || !formData.address_detail) {
									alert("Lütfen tüm zorunlu alanları doldurun.");
									return;
							}
							
							// AJAX isteği
							$.ajax({
									url: formData.address_id ? "update-address.php" : "add-address.php",
									type: "POST",
									data: formData,
									dataType: "json",
									success: function(response) {
											if (response.status === "success") {
													alert(response.message);
													// Formu temizle
													$("#address-form")[0].reset();
													$("#address_id").val("");
													$("#district").prop("disabled", true);
													$("#semt").prop("disabled", true);
													// Adresleri yeniden yükle
													loadAddresses();
											} else {
													alert(response.message);
											}
									},
									error: function() {
											alert("Bir hata oluştu. Lütfen tekrar deneyin.");
									}
							});
					});
					
					// Adresleri yükle
					function loadAddresses() {
							$.ajax({
									url: "get-addresses.php",
									type: "GET",
									success: function(data) {
											$("#addresses-list").html(data);
											
											// Düzenle butonuna tıklama - Delegated event binding kullan
											$(document).off('click', '.edit-address').on('click', '.edit-address', function() {
													console.log("Edit button clicked"); // Debug için
													var addressId = $(this).data("id");
													console.log("Address ID:", addressId); // Debug için
													loadAddressForEdit(addressId);
											});
											
											// Sil butonuna tıklama - Delegated event binding kullan
											$(document).off('click', '.delete-address').on('click', '.delete-address', function() {
													console.log("Delete button clicked"); // Debug için
													var addressId = $(this).data("id");
													console.log("Address ID:", addressId); // Debug için
													if (confirm("Bu adresi silmek istediğinize emin misiniz?")) {
															deleteAddress(addressId);
													}
											});
									},
									error: function() {
											console.error("Adresler yüklenirken hata oluştu");
									}
							});
					}
					
					// Adres düzenleme için yükle
					function loadAddressForEdit(addressId) {
							console.log("loadAddressForEdit called with ID:", addressId); // Debug için
							$.ajax({
									url: "get-address-detail.php",
									type: "POST",
									data: { address_id: addressId },
									dataType: "json",
									success: function(response) {
											console.log("Address detail response:", response); // Debug için
											if (response.status === "success") {
													var address = response.address;
													$("#address_id").val(address.address_id);
													$("#address_title").val(address.address_title);
													$("#full_name").val(address.full_name);
													$("#phone").val(address.phone);
													$("#city").val(address.city).trigger("change");
													
													// İlçeleri yükle ve seç
													setTimeout(function() {
															$("#district").val(address.district).trigger("change");
															
															// Semtleri yükle ve seç
															setTimeout(function() {
																	$("#semt").val(address.semt);
															}, 500);
													}, 500);
													
													$("#address_detail").val(address.address_detail);
													
													// Formu göster
													$('html, body').animate({
															scrollTop: $("#address-form").offset().top - 100
													}, 500);
											} else {
													alert(response.message || "Adres detayları yüklenemedi.");
											}
									},
									error: function(xhr, status, error) {
											console.error("AJAX error:", status, error);
											console.log("Response:", xhr.responseText);
											alert("Adres detayları yüklenirken hata oluştu.");
									}
							});
					}
					
					// Adres sil
					function deleteAddress(addressId) {
							console.log("deleteAddress called with ID:", addressId); // Debug için
							$.ajax({
									url: "delete-address.php",
									type: "POST",
									data: { address_id: addressId },
									dataType: "json",
									success: function(response) {
											console.log("Delete response:", response); // Debug için
											if (response.status === "success") {
													alert(response.message);
													loadAddresses();
											} else {
													alert(response.message);
											}
									},
									error: function(xhr, status, error) {
											console.error("AJAX error:", status, error);
											console.log("Response:", xhr.responseText);
											alert("Adres silinirken hata oluştu.");
									}
							});
					}
			});
		</script>
		<script>
		// Hesap bilgilerini güncelleme
			$('#update-account-form').on('submit', function(e) {
					e.preventDefault();
					
					var formData = {
							customer_name: $('#customer_name').val(),
							customer_phone: $('#customer_phone').val(),
							customer_mail: $('#customer_mail').val(),
							customer_birth: $('#customer_birth').val(),
							customer_gender: $('#customer_gender').val()
					};
					
					$.ajax({
							url: 'update-account.php',
							type: 'POST',
							data: formData,
							dataType: 'json',
							success: function(response) {
									if (response.status === 'success') {
											alert(response.message);
									} else {
											alert(response.message);
									}
							},
							error: function() {
									alert('Bir hata oluştu. Lütfen tekrar deneyin.');
							}
					});
			});

			// Şifre değiştirme
			$('#change-password-form').on('submit', function(e) {
					e.preventDefault();
					
					var currentPassword = $('#current_password').val();
					var newPassword = $('#new_password').val();
					var confirmPassword = $('#confirm_password').val();
					
					// Validasyon
					if (!currentPassword || !newPassword || !confirmPassword) {
							alert('Lütfen tüm alanları doldurun.');
							return;
					}
					
					if (newPassword !== confirmPassword) {
							alert('Yeni şifreler eşleşmiyor.');
							return;
					}
					
					if (newPassword.length < 6) {
							alert('Yeni şifre en az 6 karakter olmalıdır.');
							return;
					}
					
					$.ajax({
							url: 'change-password.php',
							type: 'POST',
							data: {
									current_password: currentPassword,
									new_password: newPassword
							},
							dataType: 'json',
							success: function(response) {
									if (response.status === 'success') {
											alert(response.message);
											$('#change-password-form')[0].reset();
									} else {
											alert(response.message);
									}
							},
							error: function() {
									alert('Bir hata oluştu. Lütfen tekrar deneyin.');
							}
					});
			});
		</script>
		<script>
		// Sipariş detayını görüntüle
		$(document).on('click', '.view-order', function() {
				var orderId = $(this).data('order-id');
				var orderNumber = $(this).closest('tr').find('td:eq(1)').text(); // Sipariş numarasını al
				
				// Modal başlığını güncelle
				$('#orderDetailsModalLabel').text('Sipariş Detayları - ' + orderNumber);
				
				// Loading göster
				$('#orderDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Yükleniyor...</p></div>');
				
				// Modalı aç
				$('#orderDetailsModal').modal('show');
				
				// AJAX isteği
				$.ajax({
						url: 'get-order-details.php',
						type: 'POST',
						data: { order_id: orderId },
						success: function(response) {
								$('#orderDetailsContent').html(response);
						},
						error: function() {
								$('#orderDetailsContent').html('<div class="alert alert-danger">Sipariş detayları yüklenemedi.</div>');
						}
				});
		});
		</script>
</body>

</html>
