<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

$error_message = '';
$success_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int) $_POST['user_id'];
    $company_id = (int) $_POST['company_id'];
    $role = 'company'; 

    if (empty($user_id) || empty($company_id)) {
        $error_message = "Kullanıcı ve firma seçimi zorunludur.";
    } else {
        
        $stmt = $db->prepare('
            UPDATE "User" 
            SET role = :role, company_id = :company_id 
            WHERE id = :user_id
        ');

        $stmt->bindValue(':role', $role, SQLITE3_TEXT);
        $stmt->bindValue(':company_id', $company_id, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $success_message = "Kullanıcı başarıyla firma admini olarak atandı. Listeye yönlendiriliyorsunuz...";
            
            header("refresh:2;url=manage_users.php");
        } else {
            $error_message = "Kullanıcı atanamadı. Bir veritabanı hatası oluştu.";
        }
    }
}


$company_stmt = $db->query("SELECT id, name FROM Bus_Company");
$companies = [];
while ($row = $company_stmt->fetchArray(SQLITE3_ASSOC)) {
    $companies[] = $row;
}


$user_stmt = $db->query("
    SELECT id, full_name, email 
    FROM \"User\" 
    WHERE (role = 'user' OR role IS NULL) AND company_id IS NULL
");
$users = [];
while ($row = $user_stmt->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

?>

<html>
<?php $title = "Firma Admini Ata"; ?>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>
    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-user-plus"></i>
                <h4>Mevcut Kullanıcıyı Firma Admini Olarak Ata</h4>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>

            <?php if (!$success_message): ?>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">

                    <div class="form-group">
                        <label for="user_id">Atanacak Kullanıcı:</label>
                        <select id="user_id" name="user_id" required>
                            <option value="">-- Kullanıcı Seçin --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= htmlspecialchars($user['id']) ?>">
                                    <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                                <option value="" disabled>Atanacak uygun kullanıcı bulunamadı.</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="company_id">Atanacak Firma:</label>
                        <select id="company_id" name="company_id" required>
                            <option value="">-- Firma Seçin --</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?= htmlspecialchars($company['id']) ?>">
                                    <?= htmlspecialchars($company['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class='button button_color_2'>
                        <div class='button_bcg'></div>
                        <button type='submit'>Ata</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php include "../includes/footer.inc.php"; ?>
</body>

</html>