<?php
// load application header
require('../../includes/application_top.php');
// load application front header
require(INCLUDES_DIR.'front_top.php');

$adv_info_tbl->get_db_vars($_SESSION['advertiser_id']);

/*
Copyright (c) 2009 Ronnie Garcia, Travis Nickels

This file is part of Uploadify v1.6.2

Permission is hereby granted, free of charge, to any person obtaining a copy
of Uploadify and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

UPLOADIFY IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . md5($adv_info_tbl->username) . "-" . $_FILES['Filedata']['name'];
	
	// Uncomment the following line if you want to make the directory if it doesn't exist
	// mkdir(str_replace('//','/',$targetPath), 0755, true);
	
	move_uploaded_file($tempFile,$targetFile);
	
	// update advertiser image in database
	$adv_info_tbl->image = md5($adv_info_tbl->username) . "-" . $_FILES['Filedata']['name'];
	$adv_info_tbl->id = $_SESSION['advertiser_id'];
	$adv_info_tbl->update_image();
}
echo "1";
?>