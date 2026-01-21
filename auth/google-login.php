<<<<<<< HEAD
<?php
require_once '../includes/config.php';
require_once '../includes/google-auth.php';

$google_auth = new GoogleAuth();
header('Location: ' . $google_auth->getAuthUrl());
=======
<?php
require_once '../includes/config.php';
require_once '../includes/google-auth.php';

$google_auth = new GoogleAuth();
header('Location: ' . $google_auth->getAuthUrl());
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
exit();