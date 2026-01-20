<?php
require_once '../includes/config.php';
//Página básica para un correcto logout
session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/index.php');
exit();
?>