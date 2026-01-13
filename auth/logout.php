<?php
require_once '../includes/config.php';
//Código básico para desloguearse
session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit();