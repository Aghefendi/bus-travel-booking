<?php
include './includes/secureSession.inc.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
switch ($_SESSION["role"]) {
    case 'admin':
        header("Location: dashboard.php");
        break;

    case 'company':
        header("Location: dashboard.php");
        break;

    case 'user':
        header("Location: dashboard.php");
        break;

    default:

        session_destroy();
        header("Location: dashboard.php");
        break;
}
exit();


?>