<?php
require_once '../includes/config.php';
require_once '../includes/google-auth.php';

$google_auth = new GoogleAuth();
header('Location: ' . $google_auth->getAuthUrl());
exit();