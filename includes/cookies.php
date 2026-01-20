<<<<<<< HEAD
<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';

if ($action === 'accept') {
    setcookie('cookie_consent', 'accepted', time() + (86400 * 365), '/');
    $_SESSION['cookie_consent'] = 'accepted';
    unset($_SESSION['show_cookie_banner']);
    echo json_encode(['success' => true]);
} elseif ($action === 'reject') {
    setcookie('cookie_consent', 'rejected', time() + (86400 * 365), '/');
    $_SESSION['cookie_consent'] = 'rejected';
    unset($_SESSION['show_cookie_banner']);
    echo json_encode(['success' => true]);
=======
<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';

if ($action === 'accept') {
    setcookie('cookie_consent', 'accepted', time() + (86400 * 365), '/');
    $_SESSION['cookie_consent'] = 'accepted';
    unset($_SESSION['show_cookie_banner']);
    echo json_encode(['success' => true]);
} elseif ($action === 'reject') {
    setcookie('cookie_consent', 'rejected', time() + (86400 * 365), '/');
    $_SESSION['cookie_consent'] = 'rejected';
    unset($_SESSION['show_cookie_banner']);
    echo json_encode(['success' => true]);
>>>>>>> 9eda46afd468fe512e1c54b728d4cf4768644f34
}