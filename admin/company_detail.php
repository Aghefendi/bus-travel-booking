<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

$company = null;
$error_message = '';

if (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];

    $stmt = $db->prepare("SELECT id, name, logo_path FROM Bus_Company WHERE id = :id");
    $stmt->bindValue(':id', $company_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $company = $row;
    } else {
        $error_message = "Belirtilen ID ile firma bulunamadı.";
    }
} else {

    header("Location: ./admin_dashboard.php");
    exit();
}
?>

<html>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>
    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-circle-info"></i>
                <h4>Firma Detayları</h4>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php elseif ($company): ?>
                <div class="company-detail-card">
                    <img src="<?= htmlspecialchars($company['logo_path']) ?>"
                        alt="<?= htmlspecialchars($company['name']) ?> Logo" class="company-logo-large">
                    <h2><?= htmlspecialchars($company['name']) ?></h2>
                  

                    <a href="./admin_dashboard.php" class="button button_color_2">
                        <div class='button_bcg'></div>
                        <button type='submit'>Geri Dön</button>
                    </a>

                </div>
            <?php endif; ?>

        </div>
    </div>
    <?php include "../includes/footer.inc.php"; ?>

   
</body>

</html>