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

$message = "";
$trip = null; 


if (isset($_GET['update_trip_id'])) {
    $tripId = (int) $_GET['update_trip_id'];

    $stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id AND company_id = :company_id");
    $stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
    $stmt->bindValue(':company_id', $companyId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $trip = $result->fetchArray(SQLITE3_ASSOC);

    if (!$trip) {
        die("Geçersiz sefer ID'si veya bu işlemi yapmaya yetkiniz yok.");
    }
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   
    header("Location: moderator_panel.php");
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tripIdToUpdate = (int) $_POST['trip_id'];

    $departure_time = date('Y-m-d H:i', strtotime($_POST['departure_time']));
    $arrival_time = date('Y-m-d H:i', strtotime($_POST['arrival_time']));

    $sql = "UPDATE Trips 
            SET 
                departure_city = :departure_city,
                destination_city = :destination_city,
                departure_time = :departure_time,
                arrival_time = :arrival_time,
                price = :price,
                capacity = :capacity
            WHERE id = :trip_id AND company_id = :company_id"; 

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':departure_city', $_POST['departure_city'], SQLITE3_TEXT);
    $stmt->bindValue(':destination_city', $_POST['destination_city'], SQLITE3_TEXT);
    $stmt->bindValue(':departure_time', $departure_time, SQLITE3_TEXT);
    $stmt->bindValue(':arrival_time', $arrival_time, SQLITE3_TEXT);
    $stmt->bindValue(':price', $_POST['price'], SQLITE3_INTEGER);
    $stmt->bindValue(':capacity', $_POST['capacity'], SQLITE3_INTEGER);
    $stmt->bindValue(':trip_id', $tripIdToUpdate, SQLITE3_INTEGER);
    $stmt->bindValue(':company_id', $companyId, SQLITE3_INTEGER); // <-- GÜVENLİK EKLEMESİ

    $result = $stmt->execute();
    if ($result) {
        header("Location: moderator_panel.php");
        exit();
    } else {
        $message = "Sefer güncellenirken bir hata oluştu.";
    }
}
?>
<html>
<?php include '../includes/head.inc.php'; ?>

<body>
    <?php include '../includes/navbar.inc.php'; ?>

    <div class="container">
        <h2>Seferi Güncelle</h2>

        <?php if ($message): ?>
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($trip): ?>
            <form method="post" action="">
                <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip['id']); ?>">

                <div class="search-form-group">
                    <label for="departure_city" class="form-label">Kalkış Noktası</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-location-dot"></i>
                        <input type="text" class="form-control" id="departure_city" name="departure_city"
                            value="<?php echo htmlspecialchars($trip['departure_city']); ?>" required>
                    </div>
                </div>

                <div class="search-form-group">
                    <label for="destination_city" class="form-label">Varış Noktası</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-location-arrow"></i>
                        <input type="text" class="form-control" id="destination_city" name="destination_city"
                            value="<?php echo htmlspecialchars($trip['destination_city']); ?>" required>
                    </div>
                </div>

                <div class="search-form-group">
                    <label for="departure_time" class="form-label">Kalkış Zamanı</label>
                    <input type="text" class="form-control" id="departure_time" name="departure_time"
                        value="<?php echo htmlspecialchars($trip['departure_time']); ?>" required>
                </div>

                <div class="search-form-group">
                    <label for="arrival_time" class="form-label">Varış Zamanı</label>
                    <input type="text" class="form-control" id="arrival_time" name="arrival_time"
                        value="<?php echo htmlspecialchars($trip['arrival_time']); ?>" required>
                </div>

                <div class="search-form-group">
                    <label for="price" class="form-label">Ücret (₺)</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <input type="number" class="form-control" id="price" name="price"
                            value="<?php echo htmlspecialchars($trip['price']); ?>" required>
                    </div>
                </div>

                <div class="search-form-group">
                    <label for="capacity" class="form-label">Kapasite</label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-users"></i>
                        <input type="number" class="form-control" id="capacity" name="capacity"
                            value="<?php echo htmlspecialchars($trip['capacity']); ?>" required>
                    </div>
                </div>

                <div class="button button_color_2">
                    <div class="button_bcg"></div>
                    <button type="submit">Güncelle</button>
                </div>
            </form>
        <?php endif; ?>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            flatpickr("#departure_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });

            flatpickr("#arrival_time", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true
            });
        });
    </script>

    <?php include '../includes/footer.inc.php'; ?>
</body>

</html>