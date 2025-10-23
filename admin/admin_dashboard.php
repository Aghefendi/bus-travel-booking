<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_company_id'])) {
    $company_id_to_delete = $_POST['delete_company_id'];

   
    $stmt = $db->prepare("DELETE FROM Bus_Company WHERE id = :id");
    $stmt->bindValue(':id', $company_id_to_delete, SQLITE3_INTEGER);
    $result = $stmt->execute();

   
    header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
    exit();
}
$results = null;

$stmt = $db->prepare("SELECT id, name, logo_path FROM Bus_Company");
$results = $stmt->execute();


?>

<html>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>

    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-ticket-simple"></i>
                <h4> Otobüs firmaları</h4>
            </div>

            <div>
                <form method='get' action='add_company.php' style='margin:0;'>
                    <div class='button button_color_2 '>
                        <div class='button_bcg'></div>
                        <button type='submit'>Ekle</button>
                    </div>
                </form>
            </div>


            <table class=" table custom-table">
                <thead>
                    <tr>
                        <th>name</th>
                        <th>logo</th>
                        <th style="width: 150px; text-align: center;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($results) {
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td><img src='" . htmlspecialchars($row['logo_path']) . "' alt='" . htmlspecialchars($row['name']) . "' style='width: 100px; border-radius: 5px;'></td>";

                            echo "<td style='text-align: center; display: flex; gap: 10px; align-items: center; border-top: none;'>
            
            <form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' style='margin:0;'>
                <input type='hidden' name='delete_company_id' value='" . htmlspecialchars($row['id']) . "'>
                <div class='button button_color_2 button-small'>
                    <div class='button_bcg'></div>
                    <button type='submit'>Sil</button>
                </div>
            </form>

            <form method='get' action='./update_company.php' style='margin:0;'>
                <input type='hidden' name='company_id' value='" . htmlspecialchars($row['id']) . "'>
                <div class='button button_color_2 button-small'>
                    <div class='button_bcg'></div>
                    <button type='submit'>Güncelle</button>
                </div>
            </form>

            <form method='get' action='./company_detail.php' style='margin:0;'>
                <input type='hidden' name='company_id' value='" . htmlspecialchars($row['id']) . "'>
                <div class='button button_color_2 button-small'>
                    <div class='button_bcg'></div>
                    <button type='submit'>Detay</button>
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