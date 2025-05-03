<?php
function mAlert($message){
	echo "<script>alert('$message');</script>";
}

function limitText($text, $limit = 25) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . "...";
    }
    return $text;
}

?>
