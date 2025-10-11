<?php

require_once './includes/secureSession.inc.php';


$_SESSION = array();


session_destroy();


header("location: index.php");
exit;
?>