<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$searchResults = null;

$userId = $_SESSION['user_id'];


$stmt = $db->prepare("SELECT company_id FROM User WHERE id = :id");
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);
$companyId = $user['company_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_trip_id'])) {
    $tripId = (int) $_POST['delete_trip_id'];

    $stmt = $db->prepare("DELETE FROM Trips WHERE id = :id AND company_id = :company_id");
    $stmt->bindValue(':id', $tripId, SQLITE3_INTEGER);
    $stmt->bindValue(':company_id', $companyId, SQLITE3_INTEGER);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}




$stmt = $db->prepare("SELECT * FROM Trips WHERE company_id = :company_id");
$stmt->bindValue(':company_id', $companyId, SQLITE3_INTEGER);
$searchResults = $stmt->execute();
?>

<html>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>

    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-ticket-simple"></i>
                <h4> Seferleriniz</h4>
            </div>

            <div>
                <form method='get' action='add_trip.php' style='margin:0;'>
                    <div class='button button_color_2 '>
                        <div class='button_bcg'></div>
                        <button type='submit'>Ekle</button>
                    </div>
                </form>
            </div>


            <table class=" table custom-table">
                <thead>
                    <tr>
                        <th>Kalkış Şehri</th>
                        <th>Varış Şehri</th>
                        <th style="width: 150px; text-align: center;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($searchResults) {
                        while ($row = $searchResults->fetchArray(SQLITE3_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['departure_city']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['destination_city']) . "</td>";
                            echo "<td style='text-align: center;'>
                <form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' style='margin:0;'>
                    <input type='hidden' name='delete_trip_id' value='" . htmlspecialchars($row['id']) . "'>
                    <div class='button button_color_2 button-small'>
                        <div class='button_bcg'></div>
                        <button type='submit'>Sil</button>
                    </div>
                </form>
              </td>";

                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include "../includes/footer.inc.php"; ?>
</body>

</html>