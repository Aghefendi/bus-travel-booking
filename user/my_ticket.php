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
$ticketsResult = $stmt->execute();

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
            
            
            if ($ticketsResult) {

              
                while ($ticket = $ticketsResult->fetchArray(SQLITE3_ASSOC)) {
                    $hasTickets = true; 
                    ?>

                    <div class="ticket-card">
                        <div class="ticket-company">
                            <img src="<?= htmlspecialchars($ticket['logo_path']) ?>"
                                alt="<?= htmlspecialchars($ticket['company_name']) ?>">
                            <strong><?= htmlspecialchars($ticket['company_name']) ?></strong>
                        </div>
                        <div class="ticket-details">
                            <div class="trip-info">
                                <div>
                                    <h4><?= htmlspecialchars($ticket['departure_city']) ?> →
                                        <?= htmlspecialchars($ticket['destination_city']) ?>
                                    </h4>
                                    <small><?= date('d F Y, H:i', strtotime($ticket['departure_time'])) ?></small>
                                </div>
                                <div class="seat-info">
                                    KOLTUK
                                    <div class="seat-number"><?= htmlspecialchars($ticket['seat_number']) ?></div>
                                </div>
                            </div>
                            <div class="ticket-footer">
                                <div class="ticket-status <?= htmlspecialchars($ticket['status']) ?>">
                                    <?= strtoupper(htmlspecialchars($ticket['status'])) ?>
                                </div>
                                <strong>Fiyat: <?= htmlspecialchars($ticket['total_price']) ?> ₺</strong>

                                <?php
                                $departureTimestamp = strtotime($ticket['departure_time']);
                                $nowTimestamp = time();
                              
                                $canCancel = ($departureTimestamp - $nowTimestamp) > 3600;

                                if ($ticket['status'] === 'active' && $canCancel):
                                    ?>
                                    <form action="cancel_ticket.php" method="POST"
                                        onsubmit="return confirm('Bileti iptal etmek istediğinizden emin misiniz?');">
                                        <input type="hidden" name="ticket_id" value="<?= $ticket['ticket_id'] ?>">
                                        <button type="submit" class="button button_color_danger button-small">İptal Et</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div> <?php
                } 
            }

       
            if (!$hasTickets) {
                echo "<p style='color: #000; text-align: center; padding: 20px;'>Henüz satın alınmış bir biletiniz bulunmamaktadır.</p>";
            }
            ?>

        </div>
    </div> <?php include '../includes/footer.inc.php'; ?>
</body>

</html>