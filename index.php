<?php
include("includes/secureSession.inc.php");
session_start();


if ($_SESSION["user_id"]) {
    header("Location: welcome.php");
    exit();
} else {
    header("Location: dashboard.php");
    exit();
}


?>