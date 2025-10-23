<?php
require_once './includes/secureSession.inc.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}


if (!isset($_POST['id']) || !isset($_POST['seat_number'])) {
    die("Hatalı istek: Sefer ID veya koltuk numarası eksik.");
}

require_once './includes/db.inc.php';



$tripId = (int) $_POST['id'];
$userId = (int) $_SESSION['user_id'];
$seatNumber = (int) $_POST['seat_number'];

$db->exec('BEGIN TRANSACTION');

try {
    $stmt = $db->prepare("SELECT * FROM Trips WHERE id = :id");
    if ($stmt === false) {
        die("SQL Hatası (Trips): " . $db->lastErrorMsg());
    }
    $stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
    $trip = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    $stmt = $db->prepare('SELECT * FROM "User" WHERE id = :id');
    if ($stmt === false) {
        die("SQL Hatası (User Select): " . $db->lastErrorMsg());
    }
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
    if ($stmt === false) {
        die("SQL Hatası (User Update): " . $db->lastErrorMsg());
    }
    $stmt->bindValue(':balance', $newBalance, SQLITE3_FLOAT);
    $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
    $stmt->execute();

    $db->exec('COMMIT');

    echo "<h3>Bilet satın alma işlemi başarılı!</h3>";
    echo "<p>Sefer Bilgisi: " . htmlspecialchars($trip['departure_city']) . " -> " . htmlspecialchars($trip['destination_city']) . "</p>";
    echo "<p>Koltuk Numaranız: " . htmlspecialchars($seatNumber) . "</p>";
    echo "<p>Ödenen Tutar: " . htmlspecialchars($trip['price']) . " ₺</p>";
    echo "<p>Kalan Bakiye: " . $newBalance . " ₺</p>";
    echo "<a href='index.php'>Ana Sayfaya Dön</a>";


} catch (Exception $e) {
    $db->exec('ROLLBACK');
    die("İşlem Başarısız: " . $e->getMessage());
}
?>