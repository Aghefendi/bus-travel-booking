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
    $company_name = trim($_POST['name']);
    $logo_file = $_FILES['logo'];


    if (empty($company_name)) {
        $error_message = "Firma adı zorunludur.";
    } elseif (!isset($logo_file) || $logo_file['error'] !== UPLOAD_ERR_OK) {
        $error_message = "Logo yüklenmesi zorunludur veya yükleme sırasında bir hata oluştu.";
    } else {

        $upload_dir = '../uploads/logos/';
        $file_extension = pathinfo($logo_file['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];


        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            $error_message = "Geçersiz dosya formatı. Sadece JPG, JPEG, PNG, GIF veya SVG izinlidir.";
        } elseif ($logo_file['size'] > 5242880) {
            $error_message = "Dosya boyutu çok büyük. (Maksimum 5MB)";
        } else {
            $new_filename = uniqid('logo_', true) . '.' . $file_extension;
            $target_path = $upload_dir . $new_filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($logo_file['tmp_name'], $target_path)) {

                $db_path = $target_path;

                $stmt = $db->prepare("INSERT INTO Bus_Company (name, logo_path) VALUES (:name, :logo_path)");
                $stmt->bindValue(':name', $company_name, SQLITE3_TEXT);
                $stmt->bindValue(':logo_path', $db_path, SQLITE3_TEXT);

                if ($stmt->execute()) {
                    $success_message = "Firma başarıyla eklendi. Listeye yönlendiriliyorsunuz...";
                    header("refresh:2;url=./admin_dashboard.php");
                } else {
                    $error_message = "Veritabanına kaydederken bir hata oluştu.";
                    unlink($target_path);
                }
            } else {
                $error_message = "Dosya sunucuya yüklenirken bir hata oluştu.";
            }
        }
    }
}
?>

<html>
<?php $title = "Yeni Firma Ekle"; ?>
<?php include "../includes/head.inc.php"; ?>

<body>
    <?php include "../includes/navbar.inc.php"; ?>
    <div class="container">
        <div class="results-container">
            <div class="results-header">
                <i class="fa-solid fa-building"></i>
                <h4>Yeni Otobüs Firması Ekle</h4>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if (!$success_message): ?>
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post" enctype="multipart/form-data">

                    <div class="form-group">
                        <label for="name">Firma Adı:</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="logo">Firma Logosu:</label>
                        <input type="file" id="logo" name="logo" required
                            accept="image/png, image/jpeg, image/gif, image/svg+xml">
                    </div>

                    <div class='button button_color_2'>
                        <div class='button_bcg'></div>
                        <button type='submit'>Ekle</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php include "../includes/footer.inc.php"; ?>
</body>

</html>