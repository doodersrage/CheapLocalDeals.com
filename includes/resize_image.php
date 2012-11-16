<?php

// document resizes images

// start output buffer
ob_start();

// load config file
require('config.php');
require('settings.php');

// clear output buffer
ob_end_clean();

set_time_limit(10000);

//error_reporting(E_ALL ^ E_NOTICE);

require(LIBS_DIR.'resize.image.class.php');

$resize_image = new Resize_Image;

// Folder where the (original) images are located with trailing slash at the end
$images_dir = IMAGES_DIRECTORY;

// Image to resize
$image = $_GET['image'];

/* Some validation */
if(!@file_exists($images_dir.$image) || empty($image))
{
$image = 'no_image.gif';
}

// Get the new with & height
$new_width = (int)$_GET['new_width'];
$new_height = (int)$_GET['new_height'];

$resize_image->new_width = $new_width;
$resize_image->new_height = $new_height;

$resize_image->image_to_resize = $images_dir.$image; // Full Path to the file

$resize_image->ratio = true; // Keep aspect ratio

//$resize_image->save_folder = IMAGES_DIRECTORY.'thumbs/'; // Keep aspect ratio

$process = $resize_image->resize(); // Output image
?>
