<?PHP

// encrypt user password
function encrypt_password($password) {
	
	// changing this value will break any user password within the system
	$salt = md5(51374);
	
	$password = sha1($password.$salt);
	
return $password;
}

?>