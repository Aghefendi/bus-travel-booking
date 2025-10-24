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
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $company_id = (int) $_POST['company_id'];
    $role = 'company.admin';

    if (empty($full_name) || empty($email) || empty($password) || empty($company_id)) {
        $error_message = "Tüm alanlar zorunludur.";
    } elseif (strlen($password) < 6) {
        $error_message = "Parola en az 6 karakter olmalıdır.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare('
            INSERT INTO "User" (full_name, email, password, role, company_id)
            VALUES (:full_name, :email, :password, :role, :company_id)
        ');
        $stmt->bindValue(':full_name', $full_name, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
        $stmt->bindValue(':role', $role, SQLITE3_TEXT);
        $stmt->bindValue(':company_id', $company_id, SQLITE3_INTEGER);

        if ($stmt->execute()) {
            $success_message = "Firma admini başarıyla oluşturuldu ve atandı. Listeye yönlendiriliyorsunuz...";
            header("refresh:2;url=manage_users.php");
        } else {
            $error_message = "Kullanıcı oluşturulamadı. E-posta zaten kullanılıyor olabilir.";
        }
    }
}

$company_stmt = $db->query("SELECT id, name FROM Bus_Company");
$companies = [];
while ($row = $company_stmt->fetchArray(SQLITE3_ASSOC)) {
    $companies[] = $row;
}

?>

<html>
<?php $title = "Yeni Admin Ekle"; ?>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>
    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-user-plus"></i>
                <h4>Yeni Firma Admini Ekle ve Ata</h4>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div><?php endif; ?>

            <?php if (!$success_message): ?>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">

                    <div class="form-group">
                        <label for="full_name">Adı Soyadı:</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-posta:</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Parola:</label>
                        <input type="password" id="password" name="password" required>
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
                        <button type='submit'>Oluştur ve Ata</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php include "../includes/footer.inc.php"; ?>
</body>

</html>