<?php
require_once './includes/secureSession.inc.php';
require_once './includes/db.inc.php';


$error_message = '';
$success_message = '';
$success_details = '';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}


if (!isset($_POST['id']) || !isset($_POST['seat_number'])) {
    $error_message = "Hatalı istek: Sefer ID veya koltuk numarası eksik.";
} else {

    $tripId = (int) $_POST['id'];
    $userId = (int) $_SESSION['user_id'];
    $seatNumber = (int) $_POST['seat_number'];


    $db->exec('BEGIN TRANSACTION');

    try {
        $stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id");
        $stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
        $trip = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        $stmt = $db->prepare('SELECT * FROM "User" WHERE id = :id');
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if (!$trip || !$user) {
            throw new Exception("Geçersiz sefer veya kullanıcı bilgisi.");
        }

        if ($user['balance'] < $trip['price']) {
            throw new Exception("Bakiye yetersiz! Mevcut Bakiye: " . $user['balance'] . " ₺");
        }

        $stmt = $db->prepare("SELECT COUNT(*) as ticket_count FROM Tickets WHERE trip_id = :trip_id");
        $stmt->bindValue(':trip_id', $tripId, SQLITE3_INTEGER);
        $countResult = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if ($countResult['ticket_count'] >= $trip['capacity']) {
            throw new Exception("Bu seferde boş koltuk kalmamıştır.");
        }

        $stmt = $db->prepare("
            SELECT COUNT(*) as seat_count FROM Booked_Seats 
            JOIN Tickets ON Tickets.id = Booked_Seats.ticket_id 
            WHERE Tickets.trip_id = :trip_id AND Booked_Seats.seat_number = :seat_number
        ");
        $stmt->bindValue(':trip_id', $tripId, SQLITE3_INTEGER);
        $stmt->bindValue(':seat_number', $seatNumber, SQLITE3_INTEGER);
        $seatResult = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
        if ($seatResult['seat_count'] > 0) {
            throw new Exception("Seçtiğiniz " . $seatNumber . " numaralı koltuk dolu.");
        }


        $stmt = $db->prepare("
            INSERT INTO Tickets (trip_id, user_id, status, total_price) 
            VALUES (:trip_id, :user_id, 'active', :total_price)
        ");
        $stmt->bindValue(':trip_id', $tripId, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':total_price', $trip['price'], SQLITE3_INTEGER);
        $stmt->execute();
        $newTicketId = $db->lastInsertRowID();


        $stmt = $db->prepare("
            INSERT INTO Booked_Seats (ticket_id, seat_number) 
            VALUES (:ticket_id, :seat_number)
        ");
        $stmt->bindValue(':ticket_id', $newTicketId, SQLITE3_INTEGER);
        $stmt->bindValue(':seat_number', $seatNumber, SQLITE3_INTEGER);
        $stmt->execute();


        $newBalance = $user['balance'] - $trip['price'];
        $stmt = $db->prepare('UPDATE "User" SET balance = :balance WHERE id = :id');
        $stmt->bindValue(':balance', $newBalance, SQLITE3_FLOAT);
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $stmt->execute();

        $db->exec('COMMIT');


        $success_message = "Bilet Satın Alma İşlemi Başarılı!";
        $success_details = "
            <p><strong>Sefer Bilgisi:</strong> " . htmlspecialchars($trip['departure_city']) . " -> " . htmlspecialchars($trip['destination_city']) . "</p>
            <p><strong>Koltuk Numaranız:</strong> " . htmlspecialchars($seatNumber) . "</p>
            <p><strong>Ödenen Tutar:</strong> " . htmlspecialchars($trip['price']) . " ₺</p>
            <p><strong>Kalan Bakiye:</strong> " . $newBalance . " ₺</p>
        ";


    } catch (Exception $e) {
        $db->exec('ROLLBACK');

        $error_message = "İşlem Başarısız: " . $e->getMessage();
    }
}
?>

<html>
<?php $title = "Bilet İşlem Sonucu"; ?>
<?php include "./includes/head.inc.php"; ?>

<body>
    <?php include "./includes/navbar.inc.php"; ?>

    <div class="container">
        <div class="results-container">

            <div class="results-header">
                <i class="fa-solid fa-ticket"></i>
                <h4>Bilet İşlem Sonucu</h4>
            </div>

            <?php if ($error_message): ?>
                <div class="ticket-status canceled"
                    style="padding: 20px; font-size: 1.1em; margin-bottom: 25px; color: #721c24; text-align: center;">
                    <?= htmlspecialchars($error_message) ?>
                </div>

                <a href="index.php" class="button button_color_2" style="text-decoration: none; margin-top: 20px;">
                    <div class='button_bcg'></div>
                    <span>Geri Dön</span>
                </a>
            <?php endif; ?>


            <?php if ($success_message): ?>
                <div class="company-detail-card" style="text-align: left;">

                    <h2 class="ticket-status active"
                        style="padding: 15px; font-size: 1.5em; margin-bottom: 25px; color: #155724; text-align: center;">
                        <i class="fa-solid fa-check-circle"></i>
                        <?= htmlspecialchars($success_message) ?>
                    </h2>

                    <?= $success_details ?>

                    <a href="index.php" class="button button_color_2" style="text-decoration: none; margin-top: 20px;">
                        <div class='button_bcg'></div>
                        <span>Ana Sayfaya Dön</span>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <?php include "./includes/footer.inc.php"; ?>
</body>

</html>