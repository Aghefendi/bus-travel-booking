<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $user_id_to_delete = $_POST['delete_user_id'];
    $stmt = $db->prepare('DELETE FROM "User" WHERE id = :id AND role = "company.admin"');
    $stmt->bindValue(':id', $user_id_to_delete, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
    exit();
}


$stmt = $db->prepare('
    SELECT u.id, u.full_name, u.email, b.name as company_name 
    FROM "User" u
    LEFT JOIN Bus_Company b ON u.company_id = b.id
    WHERE u.role = :role
');
$stmt->bindValue(':role', 'company.admin', SQLITE3_TEXT);
$results = $stmt->execute();

?>

<html>
<?php $title = "Kullanıcı Yönetimi"; ?>
<?php include '../includes/head.inc.php'; ?>

<body>
    <?php include '../includes/navbar.inc.php'; ?>

    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-users"></i>
                <h4>Firma Adminleri Yönetimi</h4>
            </div>

            <div>
                <form method='get' action='add_company_admin.php' style='margin:0;'>
                    <div class='button button_color_2 '>
                        <div class='button_bcg'></div>
                        <button type='submit'>Yeni Admin Ekle</button>
                    </div>
                </form>
            </div>

            <table class=" table custom-table">
                <thead>
                    <tr>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>Atandığı Firma</th>
                        <th style="width: 150px; text-align: center;">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($results) {
                        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . ($row['company_name'] ? htmlspecialchars($row['company_name']) : '<i>Atanmamış</i>') . "</td>";
                            echo "<td style='text-align: center;'>
                                <form method='post' action='" . htmlspecialchars($_SERVER['PHP_SELF']) . "' style='margin:0;'>
                                    <input type='hidden' name='delete_user_id' value='" . htmlspecialchars($row['id']) . "'>
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

    <?php include '../includes/footer.inc.php'; ?>
</body>

</html>