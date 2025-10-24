<?php
require_once '../includes/db.inc.php';
require_once '../includes/secureSession.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php?error=unauthorized");
    exit();
}

$company_id = null;
$company_name = '';
$company_logo_path = '';
$error_message = '';
$success_message = '';
$upload_dir = '../assets/images';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company_id'])) {
    $company_id = $_POST['company_id'];
    $company_name = trim($_POST['name']);
    $new_logo_path = null;

    if (empty($company_name)) {
        $error_message = "Firma adı boş bırakılamaz.";
    } else {


        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {


            $file_name = uniqid() . '-' . basename($_FILES['logo']['name']);
            $target_file = $upload_dir . $file_name;
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


            if (in_array($image_type, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                    $new_logo_path = $target_file;
                } else {
                    $error_message = "Logo yüklenirken bir hata oluştu.";
                }
            } else {
                $error_message = "Geçersiz dosya formatı. Sadece JPG, PNG, GIF izin verilir.";
            }
        }


        if (empty($error_message)) {
            if ($new_logo_path) {

                $stmt = $db->prepare("UPDATE Bus_Company SET name = :name, logo_path = :logo_path WHERE id = :id");
                $stmt->bindValue(':logo_path', $new_logo_path, SQLITE3_TEXT);
            } else {

                $stmt = $db->prepare("UPDATE Bus_Company SET name = :name WHERE id = :id");
            }

            $stmt->bindValue(':name', $company_name, SQLITE3_TEXT);
            $stmt->bindValue(':id', $company_id, SQLITE3_INTEGER);

            if ($stmt->execute()) {
                $success_message = "Firma başarıyla güncellendi. Listeye yönlendiriliyorsunuz...";
                header("refresh:2;url=./admin_dashboard.php");
            } else {
                $error_message = "Güncelleme sırasında hata oluştu.";
            }
        }
    }
} elseif (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];

    $stmt = $db->prepare("SELECT name, logo_path FROM Bus_Company WHERE id = :id");
    $stmt->bindValue(':id', $company_id, SQLITE3_INTEGER);
    $result = $stmt->execute();

    if ($company = $result->fetchArray(SQLITE3_ASSOC)) {
        $company_name = $company['name'];
        $company_logo_path = $company['logo_path'];
    } else {
        $error_message = "Firma bulunamadı.";
        $company_id = null;
    }
} else {
    header("Location: ./admin_dashboard.php");
    exit();
}
?>

<html>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>
    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-pen-to-square"></i>
                <h4>Firma Bilgilerini Güncelle</h4>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                <script>
                    setTimeout(function () {
                        window.location.href = './admin_dashboard.php';
                    }, 3000); 
                </script>
            <?php endif; ?>

            <?php if ($company_id && !$success_message): ?>

                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="company_id" value="<?= htmlspecialchars($company_id) ?>">

                    <div class="form-group">
                        <label for="name">Firma Adı:</label>
                        <input type="text" id="name" class="form-control" name="name"
                            value="<?= htmlspecialchars($company_name) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Mevcut Logo:</label>
                        <?php if ($company_logo_path && file_exists($company_logo_path)): ?>
                            <img src="<?= htmlspecialchars($company_logo_path) ?>" alt="Mevcut Logo"
                                style="width: 150px; border-radius: 5px; margin-top: 10px; display: block;">
                        <?php else: ?>
                            <p style="margin-top: 10px;">Logo yok.</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" style="margin-top: 20px;">
                        <label for="logo">Logoyu Değiştir (İsteğe bağlı):</label>
                        <input type="file" id="logo" class="form-control" name="logo"
                            accept="image/png, image/jpeg, image/gif">
                    </div>

                    <div class='button button_color_2'>
                        <div class='button_bcg'></div>
                        <button type='submit'>Güncelle</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php include "../includes/footer.inc.php"; ?>
</body>

</html>