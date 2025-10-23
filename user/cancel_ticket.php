<?php
require_once '../includes/secureSession.inc.php';
require_once '../includes/db.inc.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['ticket_id'])) {
    header("Location: index.php");
    exit();
}

$userId = (int) $_SESSION['user_id'];
$ticketId = (int) $_POST['ticket_id'];


$db->exec('BEGIN TRANSACTION');

try {
    $stmt = $db->prepare("
        SELECT t.*, tr.departure_time FROM Tickets t
        JOIN Trips tr ON t.trip_id = tr.id
        WHERE t.id = :ticket_id AND t.user_id = :user_id
    ");
    $stmt->bindValue(':ticket_id', $ticketId, SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $ticket = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$ticket) {
        throw new Exception("Bilet bulunamadı veya bu bileti iptal etme yetkiniz yok.");
    }

    if ($ticket['status'] !== 'active') {
        throw new Exception("Sadece aktif biletler iptal edilebilir.");
    }

 
    $departureTimestamp = strtotime($ticket['departure_time']);
    if (($departureTimestamp - time()) <= 3600) {
        throw new Exception("Sefer saatine 1 saatten az kaldığı için bilet iptal edilemez.");
    }

  
    $stmt = $db->prepare("UPDATE Tickets SET status = 'canceled' WHERE id = :ticket_id");
    $stmt->bindValue(':ticket_id', $ticketId, SQLITE3_INTEGER);
    $stmt->execute();


    $stmt = $db->prepare('UPDATE "User" SET balance = balance + :refund_amount WHERE id = :user_id');
    $stmt->bindValue(':refund_amount', $ticket['total_price'], SQLITE3_FLOAT);
    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $stmt->execute();


    $stmt = $db->prepare("DELETE FROM Booked_Seats WHERE ticket_id = :ticket_id");
    $stmt->bindValue(':ticket_id', $ticketId, SQLITE3_INTEGER);
    $stmt->execute();

  
    $db->exec('COMMIT');

    header(header: "Location: ./my_ticket.php?success=cancellation_successful");
    exit();

} catch (Exception $e) {
   
    $db->exec('ROLLBACK');
    die("İptal işlemi başarısız: " . $e->getMessage());
}