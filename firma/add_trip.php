<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Company ID al
$userId = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT company_id FROM User WHERE id = :id");
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$companyId = $user['company_id'];

if (!$companyId)
    die("Şirket bilgisi bulunamadı.");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departure_time = date('Y-m-d H:i', strtotime($_POST['departure_time']));
    $arrival_time = date('Y-m-d H:i', strtotime($_POST['arrival_time']));

    $sql = "INSERT INTO Trips (company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity)
            VALUES (:company_id, :departure_city, :destination_city, :departure_time, :arrival_time, :price, :capacity)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':company_id', $companyId, SQLITE3_INTEGER);
    $stmt->bindValue(':departure_city', $_POST['departure_city'], SQLITE3_TEXT);
    $stmt->bindValue(':destination_city', $_POST['destination_city'], SQLITE3_TEXT);
    $stmt->bindValue(':departure_time', $departure_time, SQLITE3_TEXT);
    $stmt->bindValue(':arrival_time', $arrival_time, SQLITE3_TEXT);
    $stmt->bindValue(':price', $_POST['price'], SQLITE3_INTEGER);
    $stmt->bindValue(':capacity', $_POST['capacity'], SQLITE3_INTEGER);

    $result = $stmt->execute();
    if ($result) {
        header("Location: moderator_panel.php");
        exit();
    } else {
        $message = "Sefer eklenirken bir hata oluştu.";
    }
}
?>

<html>
<?php include '../includes/head.inc.php'; ?>

<body>
    <?php include '../includes/navbar.inc.php'; ?>

    <div class="container">
        <h2>Yeni Sefer Ekle</h2>

        <?php if ($message): ?>
            <div class="error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post" action="">

            <div class="search-form-group">
                <label for="to" class="form-label">Kalkış Noktası</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-location-dot"></i>
                    <input type="text" class="form-control" id="departure_city" name="departure_city" required>
                </div>
            </div>

            <div class="search-form-group">
                <label for="to" class="form-label">Varış Noktası</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-location-arrow"></i>
                    <input type="text" class="form-control" id="destination_city" name="destination_city" required>
                </div>
            </div>

            <div class="search-form-group">
                <label for="departure_time" class="form-label">Kalkış Zamanı</label>
                <input type="text" class="form-control" id="departure_time" name="departure_time"
                    placeholder="Tarih ve saat seçin" required>
            </div>

            <div class="search-form-group">
                <label for="arrival_time" class="form-label">Varış Zamanı</label>
                <input type="text" class="form-control" id="arrival_time" name="arrival_time"
                    placeholder="Tarih ve saat seçin" required>
            </div>

            <div class="search-form-group">
                <label for="to" class="form-label">Ücret (₺)</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>
            </div>

            <div class="search-form-group">
                <label for="to" class="form-label">Kapasite</label>
                <div class="input-with-icon">
                    <i class="fa-solid fa-users"></i>
                    <input type="number" class="form-control" id="capacity" name="capacity" required>
                </div>
            </div>

            <div class="button button_color_2">
                <div class="button_bcg"></div>
                <button type="submit">Ekle</button>
            </div>
        </form>

    </div>




    <script>
        document.addEventListener('DOMContentLoaded', () => {
            flatpickr("#departure_time", {
                enableTime: true,       
                noCalendar: false,    
                dateFormat: "Y-m-d H:i", 
                time_24hr: true,      
                minuteIncrement: 1,      
                allowInput: true,      
            });

            flatpickr("#arrival_time", {
                enableTime: true,
                noCalendar: false,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                minuteIncrement: 1,
                allowInput: true,
            });
        });
    </script>

    <?php include '../includes/footer.inc.php'; ?>
</body>

</html>