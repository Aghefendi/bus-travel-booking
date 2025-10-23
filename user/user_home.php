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
        <h2>Profil Bilgileriniz</h2>

        <table class="table custom-table">
            <tr>
                <th>Kullanıcı Adı</th>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>

        </table>

        <div class="button button_color_2">
            <div class="button_bcg"></div>
            <a href="edit_profile.php" class="button-link">Profili Düzenle</a>
        </div>
    </div>

    <?php include '../includes/footer.inc.php'; ?>
</body>

</html>