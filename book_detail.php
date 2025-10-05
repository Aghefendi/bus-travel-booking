<?php
session_start();
require_once './includes/db.inc.php';
require_once './includes/secureSession.inc.php';

if (!isset($_POST['id'])) {
    die("Geçersiz ID");
}

$tripId = (int) $_POST['id'];
$stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id");
$stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
$result = $stmt->execute();
$trip = $result->fetchArray(SQLITE3_ASSOC);

if (!$trip) {
    die("Seyahat bulunamadı.");
}


$arrival = new DateTime($trip['arrival_time']);
$departure = new DateTime($trip['departure_time']);
?>

<html>
<?php $title = "Sefer Detayları"; ?>
<?php include './includes/head.inc.php'; ?>

<body>
    <?php include './includes/navbar.inc.php'; ?>

    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-ticket-simple"></i>
                <h4> Detaylar</h4>
            </div>
            <table class="table custom-table ">
                <thead>

                    <th>Kalkış</th>
                    <th>Varış</th>
                    <th>Kalkış Zamanı</th>
                    <th>Varış Zamanı</th>

                    <th>Kapasite</th>
                    <th>Ücret</th>

                </thead>

                <tbody>

                    <tr>

                        <td><?= htmlspecialchars($trip['departure_city']) ?></td>
                        <td> <?= htmlspecialchars($trip['destination_city']) ?></td>
                        <td><?= htmlspecialchars($departure->format('Y-m-d H:i')) ?></td>
                        <td><?= htmlspecialchars($arrival->format('Y-m-d H:i')) ?></td>

                        <td><?= htmlspecialchars($trip['capacity']) ?></td>
                        <td><?= htmlspecialchars($trip['price']) ?> ₺
                        </td>


                    </tr>
                </tbody>

            </table>
        </div>
    </div>


</body>
<?php include './includes/footer.inc.php'; ?>

</html>