<?php
require_once 'includes/db.inc.php';
$searchResults = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $from = strtolower($_POST['from']);
    $to = strtolower($_POST['to']);

    $stmt = $db->prepare("SELECT * FROM Trips WHERE LOWER(departure_city) = :from AND LOWER(destination_city) = :to");
    $stmt->bindValue(':from', $from, SQLITE3_TEXT);
    $stmt->bindValue(':to', $to, SQLITE3_TEXT);
    $searchResults = $stmt->execute();
} else {
    $searchResults = $db->query("SELECT * FROM Trips");
}
?>



<html>
<?php $title = "Dashboard"; ?>
<?php include 'includes/head.inc.php'; ?>
<?php include 'includes/secureSession.inc.php'; ?>




<body>
    <?php include 'includes/navbar.inc.php'; ?>
    <form method="post" class="container mt-5">
        <div class="mb-3">
            <label for="from" class="form-label">From</label>
            <input type="text" class="form-control" id="from" name="from" required>
        </div>
        <div class="mb-3">
            <label for="to" class="form-label">To</label>
            <input type="text" class="form-control" id="to" name="to" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <div class="container">
        <h1 class="mt-5">Dashboard</h1>
        <p>Burada otobüs seferlerini görebilirsiniz.</p>
        <table class="table mt-5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>arrival</th>
                    <th>departure</th>
                    <th>capacity</th>
                    <th>price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $searchResults->fetchArray(SQLITE3_ASSOC)) {
                    $arrival = new DateTime($row['arrival_time']);
                    $departure_time = new DateTime($row['departure_time']);

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['departure_city']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['destination_city']) . "</td>";
                    echo "<td>" . htmlspecialchars($arrival->format('Y-m-d H:i')) . "</td>";
                    echo "<td>" . htmlspecialchars($departure_time->format('Y-m-d H:i')) . "</td>";
                    echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>





    </div>
</body>




</html>