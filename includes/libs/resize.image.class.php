<?php
/*
------------------------------------------------------------------------------------
Credits: Bit Repository

Source URL: http://www.bitrepository.com/web-programming/php/resizing-an-image.html
------------------------------------------------------------------------------------
*/
class Resize_Image {

var $image_to_resize;
var $new_width;
var $new_height;
var $ratio;
var $new_image_name;
var $save_folder;

function resize()
{
if(!file_exists($this->image_to_resize))
{
  exit("File ".$this->image_to_resize." does not exist.");
}

$info = GetImageSize($this->image_to_resize);

if(empty($info))
{
  exit("The file ".$this->image_to_resize." doesn't seem to be an image.");
}

$width = $info[0];
$height = $info[1];
$mime = $info['mime'];

/* Keep Aspect Ratio? */

if($this->ratio)
{

$maxWidth = $this->new_width;
$maxHeight = $this->new_height;

$srcRatio = $width/$height; // width/height ratio

$destRatio = $maxWidth/$maxHeight;

if ($destRatio > $srcRatio) {
	$destSize[1] = $maxHeight;
	$destSize[0] = $maxHeight*$srcRatio;
}
else {
	$destSize[0] = $maxWidth;
	$destSize[1] = $maxWidth/$srcRatio;
}

//if set image dimensions are required:
if(isset($resizeFlag)) {
  if ($resizeFlag == 1) {
	  $destSize[0] = $maxWidth;
	  $destSize[1] = $maxHeight;
  }
}

$this->new_width = $destSize[0];
$this->new_height = $destSize[1];

}

// What sort of image?

$type = substr(strrchr($mime, '/'), 1);

switch ($type)
{
case 'jpeg':
    $image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
    break;

case 'png':
    $image_create_func = 'ImageCreateFromPNG';
    $image_save_func = 'ImagePNG';
	$new_image_ext = 'png';
    break;

case 'bmp':
    $image_create_func = 'ImageCreateFromBMP';
    $image_save_func = 'ImageBMP';
	$new_image_ext = 'bmp';
    break;

case 'gif':
    $image_create_func = 'ImageCreateFromGIF';
    $image_save_func = 'ImageGIF';
	$new_image_ext = 'gif';
    break;

case 'vnd.wap.wbmp':
    $image_create_func = 'ImageCreateFromWBMP';
    $image_save_func = 'ImageWBMP';
	$new_image_ext = 'bmp';
    break;

case 'xbm':
    $image_create_func = 'ImageCreateFromXBM';
    $image_save_func = 'ImageXBM';
	$new_image_ext = 'xbm';
    break;

default:
	$image_create_func = 'ImageCreateFromJPEG';
    $image_save_func = 'ImageJPEG';
	$new_image_ext = 'jpg';
}


		// check if image exists within thumb directory
		$cleaned_image_name = str_replace('customers/','',$this->image_to_resize);
		$new_file_location = TEMP_DIR.'thumbs/';
		$new_file_name = $this->new_thumb_name(basename($this->image_to_resize)).'_resized_w_'.(int)$_GET['new_width'].'_h_'.(int)$_GET['new_height'].'.'.$new_image_ext;
		
		if(file_exists($new_file_location.$new_file_name)) {
			
			// get file age
			$cache_file_time = date("F d Y H:i:s.",filemtime($new_file_location.$new_file_name));	
			
			// if file age less than current date create new thumb
			if ((strtotime($cache_file_time." +".SITE_CACHING_MINUTES."minutes") <= strtotime(date("F d Y H:i:s.")))) {
				$this->save_folder = $new_file_location;
				
				// New Image
				$image_c = ImageCreateTrueColor($this->new_width, $this->new_height);
			
				$new_image = $image_create_func($this->image_to_resize);
			
				ImageCopyResampled($image_c, $new_image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);
			
				if($this->save_folder)
				{
//				   if($this->new_image_name)
//				   {
//				   $new_name = $this->new_image_name.'.'.$new_image_ext;
//				   }
//				   else
//				   {
				   $new_name = $new_file_name;
//				   }
				   
				$save_path = $this->save_folder.$new_name;
		
				}
		
				$process = $image_save_func($image_c, $save_path);
				   
				header("Content-Type: ".$mime);
				$file = $new_file_location.$new_file_name;
				$fh = fopen($file, 'r+');
				$output = fread($fh, filesize($file));
				fclose($fh);
				echo $output;
			} else {
				header("Content-Type: ".$mime);
				$file = $new_file_location.$new_file_name;
				$fh = fopen($file, 'r+');
				$output = fread($fh, filesize($file));
				fclose($fh);
				echo $output;
			}
			
		} else {
			$this->save_folder = $new_file_location;
			
			// New Image
			$image_c = ImageCreateTrueColor($this->new_width, $this->new_height);
		
			$new_image = $image_create_func($this->image_to_resize);
		
			ImageCopyResampled($image_c, $new_image, 0, 0, 0, 0, $this->new_width, $this->new_height, $width, $height);
		
			if($this->save_folder)
			{
//			   if($this->new_image_name)
//			   {
//			   $new_name = $this->new_image_name.'.'.$new_image_ext;
//			   }
//			   else
//			   {
			   $new_name = $new_file_name;
//			   }
			   
			$save_path = $this->save_folder.$new_name;
	
			}
	
			$process = $image_save_func($image_c, $save_path);
			   
			header("Content-Type: ".$mime);
			$file = $new_file_location.$new_file_name;
			$fh = fopen($file, 'r+');
			$output = fread($fh, filesize($file));
			fclose($fh);
			echo $output;
			
		}

	}

	function new_thumb_name($filename)
	{
	$string = trim($filename);
	$string = strtolower($string);
	$string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));
	$string = ereg_replace("[ tnr]+", "_", $string);
	$string = str_replace(" ", '_', $string);
	$string = ereg_replace("[ _]+", "_", $string);

	return $string;
	}
}
?>