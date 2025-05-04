<?php
require_once '../settings.php';
$xyz = $_POST['editProductID'];
$productByCodeSQL = "SELECT * FROM product_images WHERE product_id = $xyz";
$productByCodeResult = $conn->query($productByCodeSQL);

$productByCodeArray = [];
if ($productByCodeResult->num_rows > 0) {
	while ($row = $productByCodeResult->fetch_assoc()) {
		$productByCodeArray[] = $row;
	}
}
$images = explode("#", $productByCodeArray[0]['product_images_url']);

// Only create carousel if there are images
if (count($images) > 0 && $images[0] != '') {
	echo '<div id="carouselExampleControls" class="carousel slide shadow-sm rounded" data-ride="carousel" max-width: 25%; margin: 0 auto;>';

	// Add indicators for better navigation
	echo '<ol class="carousel-indicators">';
	$indicator = 0;
	foreach($images as $item) {
		if($item == '') continue;
		$active = ($indicator == 0) ? 'active' : '';
		echo '<li data-target="#carouselExampleControls" data-slide-to="'.$indicator.'" class="'.$active.'"></li>';
		$indicator++;
	}
	echo '</ol>';

	// Carousel items
	echo '<div class="carousel-inner rounded">';
	$spid = 0;
	$a = "active";
	foreach($images as $item) {
		if($item == '') continue;

		if($spid == 0)
			echo '<div class="carousel-item '.$a.'">';
		else
			echo '<div class="carousel-item">';

		// Add a container for the image for better control
		echo '<div class="img-container d-flex justify-content-center align-items-center" style="height: 300px; overflow: hidden;">';
		echo '<img class="img-fluid" style="object-fit: contain; max-height: 100%;" src="../'.$item.'" alt="Product image '.($spid+1).'">';
		echo '</div></div>';
		$spid++;
	}
	echo '</div>';

	// Control arrows with improved styling
	echo '<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon bg-dark rounded-circle p-1" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
		<span class="carousel-control-next-icon bg-dark rounded-circle p-1" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
		</a>
		</div>';
}
?>
