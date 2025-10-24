<?php




require_once './includes/secureSession.inc.php';





require_once './includes/db.inc.php';


$searchResults = null;
$searchPerformed = false;

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
<?php $title = "AnaSayfa"; ?>
<?php include './includes/head.inc.php'; ?>




<body>

    <?php include './includes/navbar.inc.php'; ?>
    <div class="container">


        <div class="search-card">
            <h3>Hayalindeki Yolculuğu Planla</h3>
            <form method="post">
                <div class="search-form-row">

                    <div class="search-form-group">
                        <label for="from" class="form-label">Kalkış Noktası</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-bus"></i>
                            <input type="text" class="form-control" id="from" name="from" placeholder="Örn: İstanbul"
                                required>
                        </div>
                    </div>

                    <div class="search-form-group">
                        <label for="to" class="form-label">Varış Noktası</label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <input type="text" class="form-control" id="to" name="to" placeholder="Örn: Ankara"
                                required>
                        </div>
                    </div>

                    <div class="button button_color_2">
                        <div class="button_bcg"></div>
                        <button type="submit">Sefer Ara</button>
                    </div>

                </div>
            </form>
        </div>


    </div>
    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-ticket-simple"></i>
                <h4><?php echo $searchPerformed ? 'Arama Sonuçları' : 'Tüm Seferler'; ?></h4>
            </div>

            <table class="table custom-table">
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
                                    <form method='post' action='book_detail.php' style='margin: 0;'>
                                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                        <div class='button button_color_2 button-small'>
                                            <div class='button_bcg'></div>
                                            <button type='submit'>Detaylar</button>
                                        </div>
                                    </form> 
                                  </td>";


                            if ($_SESSION['role'] === 'user'):
                                echo "<td style='text-align: center;'>
                                    <form method='post' action='bilet_buy.php' style='margin: 0;'>
                                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                        <div class='button button_color_2 button-small'>
                                            <div class='button_bcg'></div>
                                            <button type='submit'>Bilet al</button>
                                        </div>
                                    </form> 
                                  </td>";
                            endif;

                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include './includes/footer.inc.php'; ?>



</body>



</html>