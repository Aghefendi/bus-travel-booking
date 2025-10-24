<?php
require_once '../includes/secureSession.inc.php';
require_once '../includes/db.inc.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=login_required");
    exit();
}

$userId = (int) $_SESSION['user_id'];


$stmt = $db->prepare('
    SELECT 
        t.id as ticket_id,
        t.status,
        t.total_price,
        tr.departure_city,
        tr.destination_city,
        tr.departure_time,
        bs.seat_number,
        bc.name as company_name,
        bc.logo_path
    FROM Tickets t
    JOIN Trips tr ON t.trip_id = tr.id
    JOIN Booked_Seats bs ON bs.ticket_id = t.id
    JOIN Bus_Company bc ON tr.company_id = bc.id
    WHERE t.user_id = :user_id
    ORDER BY tr.departure_time DESC
');

$stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
$tickets = $stmt->execute();

?>

<html>
<?php $title = "Biletlerim"; ?>
<?php include '../includes/head.inc.php'; ?>



<body>
    <?php include '../includes/navbar.inc.php'; ?>

    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-ticket-simple"></i>
                <h4>Biletlerim</h4>
            </div>

            <?php
            $hasTickets = false;
            if ($tickets) {
             
                $firstTicket = $tickets->fetchArray(SQLITE3_ASSOC);
                if ($firstTicket) {
                    $hasTickets = true;
                  
                    do {
                        ?>
                        <div class="ticket-card">
                            <div class="ticket-company">
                                <img src="<?= htmlspecialchars($firstTicket['logo_path']) ?>"
                                    alt="<?= htmlspecialchars($firstTicket['company_name']) ?>">
                                <strong><?= htmlspecialchars($firstTicket['company_name']) ?></strong>
                            </div>
                            <div class="ticket-details">
                                <div class="trip-info">
                                    <div>
                                        <h4><?= htmlspecialchars($firstTicket['departure_city']) ?> →
                                            <?= htmlspecialchars($firstTicket['destination_city']) ?>
                                        </h4>
                                        <small><?= date('d F Y, H:i', strtotime($firstTicket['departure_time'])) ?></small>
                                    </div>
                                    <div class="seat-info">
                                        KOLTUK
                                        <div class="seat-number"><?= htmlspecialchars($firstTicket['seat_number']) ?></div>
                                    </div>
                                </div>
                                <div class="ticket-footer">
                                    <div class="ticket-status <?= htmlspecialchars($firstTicket['status']) ?>">
                                        <?= strtoupper(htmlspecialchars($firstTicket['status'])) ?></div>
                                    <strong>Fiyat: <?= htmlspecialchars($firstTicket['total_price']) ?> ₺</strong>

                                    <?php
                                    $departureTimestamp = strtotime($firstTicket['departure_time']);
                                    $nowTimestamp = time();
                                    $canCancel = ($departureTimestamp - $nowTimestamp) > 3600; // 3600 saniye = 1 saat
                        
                                    if ($firstTicket['status'] === 'active' && $canCancel):
                                        ?>
                                        <form action="cancel_ticket.php" method="POST"
                                            onsubmit="return confirm('Bileti iptal etmek istediğinizden emin misiniz?');">
                                            <input type="hidden" name="ticket_id" value="<?= $firstTicket['ticket_id'] ?>">
                                            <button type="submit" class="button button_color_danger button-small">İptal Et</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <?php
                    } while ($firstTicket = $tickets->fetchArray(SQLITE3_ASSOC));
                }
            }

            if (!$hasTickets) {
                echo "<p>Henüz satın alınmış bir biletiniz bulunmamaktadır.</p>";
            }
            ?>
                </div>
            </div>

            <?php include '../includes/footer.inc.php'; ?>
</body>

</html>