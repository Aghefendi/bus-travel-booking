<?php
// Oturumu başlat ve giriş yapılıp yapılmadığını kontrol et
session_start();

// Eğer kullanıcı giriş yapmamışsa, login sayfasına yönlendir
if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit;
}
?>

<html>

<?php $title = "Welcome"; ?>
<?php include 'includes/head.inc.php'; ?>
<?php include 'includes/secureSession.inc.php'; ?>

<body>
    <?php include 'includes/navbar.inc.php'; ?>
    <div class="container">
        <h1 class="mt-5">Hoş Geldin, <?= htmlspecialchars($_SESSION["name"]); ?>!</h1>
        <p>Başarıyla giriş yaptınız.</p>
        <p>Email adresiniz: <?= htmlspecialchars($_SESSION["email"]); ?></p>
        <p><a href="logout.php" class="btn btn-danger">Çıkış Yap</a></p>
    </div>
</body>

</html>