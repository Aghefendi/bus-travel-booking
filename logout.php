<?php
// Oturumu başlat
session_start();

// Tüm session değişkenlerini temizle
$_SESSION = array();

// Session'ı sonlandır
session_destroy();

// Kullanıcıyı login sayfasına yönlendir
header("location: index.php");
exit;
?>