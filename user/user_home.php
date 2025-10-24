<?php
require_once '../includes/secureSession.inc.php';
require_once '../includes/db.inc.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT full_name, email  FROM User WHERE id = :id");
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if (!$user) {
    die("Kullanıcı bulunamadı.");
}
?>

<!DOCTYPE html>
<html lang="tr">
<?php $title = "Profile Page"; ?>
<?php include '../includes/head.inc.php'; ?>

<body>
    <?php include '../includes/navbar.inc.php'; ?>

    <div class="container">

        <div class="company-detail-card" style="text-align: left;">

            <h2 style="text-align: center;">Profil Bilgileriniz</h2>

            <table class="table custom-table">
                <tr class="label1">
                    <th class="label1">Kullanıcı Adı</th>
                    <td class="label1"><?= htmlspecialchars($user['full_name']) ?></td>
                </tr>
                <tr>
                    <th class="label1">Email</th>
                    <td class="label1"><?= htmlspecialchars($user['email']) ?></td>
                </tr>
            </table>
        </div>
    </div>
    <?php include '../includes/footer.inc.php'; ?>
</body>

</html>