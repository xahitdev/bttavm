<?php
include '../settings.php';

if(isset($_POST['parent_id'])) {
  $parent_id = intval($_POST['parent_id']);

  $sql = "SELECT category_id, category_name FROM categories WHERE category_parent_id = $parent_id";
  $result = $conn->query($sql);

  if($result->num_rows > 0) {
    echo '<select class="form-control" name="category[]" style="margin-top:10px;">';
    echo '<option value="">-- Select Subcategory --</option>';
    while($row = $result->fetch_assoc()) {
      echo '<option value="' . htmlspecialchars($row['category_id']) . '">' . htmlspecialchars($row['category_name']) . '</option>';
    }
    echo '</select>';
  }else{
	echo '
	  <div class="col-md-6">
		<input class="form-control" name="addProductCategory" type="text" placeholder="" value ="'. $parent_id .'" hidden>
	  </div>
		';
  }
}
?>

