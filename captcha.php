<?php
session_start();

// Generate random 4-digit captcha
$code = rand(1000, 9999);
$_SESSION['captcha'] = $code;

// Create image
$width = 100;
$height = 40;
$image = imagecreatetruecolor($width, $height);

// Colors
$bg_color = imagecolorallocate($image, 70, 0, 130);        // Dark blue background
$text_color = imagecolorallocate($image, 255, 255, 255);   // White text
$line_color = imagecolorallocate($image, 255, 255, 0);     // Yellow lines

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Draw some lines
for ($i = 0; $i < 5; $i++) {
    imageline($image, 0, rand(0,$height), $width, rand(0,$height), $line_color);
}

// Add text
imagestring($image, 5, 25, 10, $code, $text_color);

// Output image
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>