<?php

require_once './includes/db.inc.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $password = test_input($_POST['password']);





    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Tüm alanlar zorunludur.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Geçerli bir email giriniz.';

    } else {

        $check = $db->prepare('SELECT * FROM User WHERE email = :email LIMIT 1');
        $check->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $check->execute();


        if ($result->fetchArray()) {
            $error = 'Bu email zaten kayıtlı.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);



            $query = $db->prepare('INSERT INTO User (full_name, email, password, role) VALUES (:name, :email, :password, :role)');
            $query->bindValue(':name', $name, SQLITE3_TEXT);
            $query->bindValue(':email', $email, SQLITE3_TEXT);
            $query->bindValue(':password', $hash, SQLITE3_TEXT);
            $query->bindValue(':role', 'user', SQLITE3_TEXT);
            $result = $query->execute();



            if ($result) {
                header('Location: login.php');
                exit();
            } else {
                $error = 'Kayıt sırasında hata.';
            }
        }


    }
}

?>


<html>

<?php $title = "Kayıt"; ?>
<?php include './includes/head.inc.php'; ?>
<?php include 'includes/secureSession.inc.php'; ?>

<body>

    <?php include 'includes/navbar.inc.php'; ?>

    <div class="container">
        <div class="register-form-container">
            <h2 class="mt-5">Kayıt ol</h2>


            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>



            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <div class="form-group">

                    <label for="name">İsim:</label>


                    <input type="text" class="form-control" id="name" name="name">
                </div>
                <div class="form-group">

                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>

                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="button button_color_2">
                    <div class="button_bcg"></div>
                    <button type="submit">
                        Kayıt Ol
                    </button>
                </div>
            </form>

        </div>
    </div>
    <?php include './includes/footer.inc.php'; ?>



</body>

</html>