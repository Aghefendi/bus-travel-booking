<?php
require_once './includes/secureSession.inc.php';
require_once './includes/db.inc.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_POST['id'])) {
    die("Sefer ID'si belirtilmedi.");
}

$tripId = (int) $_POST['id'];


$stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id");
$stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
$trip = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

if (!$trip) {
    die("Sefer bulunamadı.");
}


$stmt = $db->prepare("
    SELECT bs.seat_number FROM Booked_Seats bs
    JOIN Tickets t ON t.id = bs.ticket_id
    WHERE t.trip_id = :trip_id
");
$stmt->bindValue(':trip_id', $tripId, SQLITE3_INTEGER);
$results = $stmt->execute();

$booked_seats_array = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $booked_seats_array[] = $row['seat_number'];
}
?>

<html>
<?php $title = "Sefer Detayları"; ?>
<?php include './includes/head.inc.php'; ?>



<body>
    <?php include './includes/navbar.inc.php'; ?>

    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-bus-simple"></i>
                <h4>Sefer Detayları ve Koltuk Seçimi</h4>
            </div>
                
            <h3><?= htmlspecialchars($trip['departure_city']) ?> -> <?= htmlspecialchars($trip['destination_city']) ?>
            </h3>
            <p><strong>Tarih:</strong> <?= date('d/m/Y H:i', strtotime($trip['departure_time'])) ?></p>
            <p><strong>Fiyat:</strong> <?= htmlspecialchars($trip['price']) ?> ₺</p>
            <hr>

            <form action="bilet_buy2.php" method="post" id="bookingForm">
                <input type="hidden" name="id" value="<?= $tripId ?>">
                <input type="hidden" name="seat_number" id="selectedSeat" value="">

                <h5>Lütfen Koltuğunuzu Seçin:</h5>
                <div class="seat-map">
                    <?php for ($i = 1; $i <= $trip['capacity']; $i++): ?>
                        <?php
                        $isBooked = in_array($i, $booked_seats_array);
                        $seatClass = $isBooked ? 'booked' : 'available';

                     
                        if ($i % 3 === 0) {
                            echo "<div class='seat corridor'></div>";
                        }
                        ?>
                        <div class="seat <?= $seatClass ?>" data-seat-number="<?= $i ?>">
                            <?= $i ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class='button button_color_2' id="buyButtonContainer">
                    <div class='button_bcg'></div>
                    <button type="submit" id="buyButton" disabled>Satın Al</button>
                </div>
                <small id="selectionMessage" style="color: red; display: block; margin-top: 10px;">Lütfen bir koltuk
                    seçin.</small>
            </form>
        </div>
    </div>

    <?php include './includes/footer.inc.php'; ?>

   
</body>

</html>