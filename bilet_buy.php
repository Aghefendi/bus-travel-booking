<?php
require_once './includes/secureSession.inc.php';



if (!isset($_SESSION['user_id'])) {

    header("Location: login.php?error=login_required");
    exit();
}


if (!isset($_POST['id'])) {
    die("Geçersiz ID");
}

require_once './includes/db.inc.php';


$tripId = (int) $_POST['id'];
$stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id");
$stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
$result = $stmt->execute();
$trip = $result->fetchArray(SQLITE3_ASSOC);

if (!$trip) {
    die("Seyahat bulunamadı.");
}


echo "Bilet satın alma işlemi başarılı! (Burada DB kaydı yapılacak)";