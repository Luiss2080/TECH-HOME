<?php
$password = '14.Leo2015';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Contraseña: " . $password . "\n";
echo "Hash: " . $hash . "\n";
?>