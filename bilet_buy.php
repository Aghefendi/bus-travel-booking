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

<style>
    /* Koltuk seçimi için basit CSS stilleri */
    .seat-map {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        max-width: 300px;
        margin: 20px auto;
    }

    .seat {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-weight: bold;
    }

    .corridor {
        grid-column: 3;
    }

    .seat.available {
        background-color: #d4edda;
        color: #155724;
        cursor: pointer;
    }

    .seat.available:hover {
        background-color: #c3e6cb;
    }

    .seat.booked {
        background-color: #f8d7da;
        color: #721c24;
        cursor: not-allowed;
    }

    .seat.selected {
        background-color: #007bff;
        color: white;
        border-color: #0056b3;
    }
</style>

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

    <script>
        const seatMap = document.querySelector('.seat-map');
        const selectedSeatInput = document.getElementById('selectedSeat');
        const buyButton = document.getElementById('buyButton');
        const selectionMessage = document.getElementById('selectionMessage');

        seatMap.addEventListener('click', function (e) {
            
            if (e.target.classList.contains('available')) {
           
                const currentlySelected = seatMap.querySelector('.selected');
                if (currentlySelected) {
                    currentlySelected.classList.remove('selected');
                }

                
                e.target.classList.add('selected');
                const seatNumber = e.target.dataset.seatNumber;

               
                selectedSeatInput.value = seatNumber;

              
                buyButton.disabled = false;
                selectionMessage.style.display = 'none';
            }
        });
    </script>
</body>

</html>