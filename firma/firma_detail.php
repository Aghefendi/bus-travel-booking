<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT company_id FROM User WHERE id = :id");
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$companyId = $user['company_id'];

if (!$companyId) {
    die("Şirket bilgisi bulunamadı.");
}

$trip = null;

$departure = null;
$arrival = null;

if (isset($_GET['firma_detail_id'])) {
    $tripId = (int) $_GET['firma_detail_id'];

    $stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id AND company_id = :company_id");
    $stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
    $stmt->bindValue(':company_id', $companyId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $trip = $result->fetchArray(SQLITE3_ASSOC);

    if (!$trip) {
        die("Geçersiz sefer ID'si veya bu işlemi yapmaya yetkiniz yok.");
    }

   
    try {
        $departure = new DateTime($trip['departure_time']);
        $arrival = new DateTime($trip['arrival_time']);
    } catch (Exception $e) {
        $departure = null;
        $arrival = null;
    }

} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: moderator_panel.php");
    exit();
}
?>
<html>
<?php $title = "Sefer Detayları"; ?>
<?php include '../includes/head.inc.php';  ?>

<body>
    <?php include '../includes/navbar.inc.php';  ?>

    <div class="container">
        <?php if ($trip && $departure && $arrival): ?>
            <div class="results-container">
                <div class="results-header">
                    <i class="fa-solid fa-ticket-simple"></i>
                    <h4>Detaylar</h4>
                </div>
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Kalkış</th>
                            <th>Varış</th>
                            <th>Kalkış Zamanı</th>
                            <th>Varış Zamanı</th>
                            <th>Kapasite</th>
                            <th>Ücret</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                            <td><?= htmlspecialchars($trip['destination_city']) ?></td>
                            <td><?= htmlspecialchars($departure->format('d-m-Y H:i')) ?></td>
                            <td><?= htmlspecialchars($arrival->format('d-m-Y H:i')) ?></td>
                            <td><?= htmlspecialchars($trip['capacity']) ?></td>
                            <td><?= htmlspecialchars($trip['price']) ?> ₺</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Sefer bilgileri görüntülenemiyor.</p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.inc.php'; ?>
</body>

</html>