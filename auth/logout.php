<?php
require_once '../includes/config.php';
<<<<<<< HEAD
//Página básica para un correcto logout
=======

>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
session_unset();
session_destroy();

header('Location: ' . BASE_URL . '/index.php');
exit();
?>