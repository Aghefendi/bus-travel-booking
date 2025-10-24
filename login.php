<?php
require_once './includes/db.inc.php';
require_once './includes/secureSession.inc.php';
require_once './rehash_plaintext.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }



    $email = test_input($_POST["email"]);
    $password = test_input($_POST["password"]);

    $stmt = $db->prepare("SELECT * FROM User WHERE email = :email LIMIT 1");
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["name"] = $user["full_name"];
        $_SESSION["role"] = $user["role"];

        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
}
?>

<html>

<?php $title = "Giriş"; ?>
<?php include './includes/secureSession.inc.php'; ?>
<?php include './includes/head.inc.php'; ?>

<body>

    <?php include 'includes/navbar.inc.php'; ?>

    <div class="container">
        <div class="register-form-container">
            <h2 class="mt-5">Login</h2>
            <?php if (isset($_GET['error']) && $_GET['error'] === 'login_required'): ?>
                <div class="alert alert-warning">
                    Lütfen giriş yapın!
                </div>
            <?php elseif (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                <div class="alert alert-danger">
                    Email veya şifre yanlış!
                </div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="button button_color_2">
                    <div class="button_bcg"></div>
                    <button type="submit">
                        Giriş Yap
                    </button>
                </div>
            </form>
            <p class="mt-3">Hesabınız yok mu? <a href="register.php">Kayıt Ol</a></p>

        </div>
    </div>
    <?php include './includes/footer.inc.php'; ?>

</body>


</html>