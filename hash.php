<?php
	$password = password_hash($_POST["password"],PASSWORD_DEFAULT);
	echo $password;
?>
