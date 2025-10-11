<?php

session_start();
?>



<nav class="main_nav">
    <div class="container">
        <div class="row">
            <div class="col main_nav_col d-flex flex-row align-items-center justify-content-start">
                <div class="logo_container">
                    <div class="logo"><a href="#"><img src="../assets/images/logo.png" alt="">BusFlixe</a></div>
                </div>
                <div class="main_nav_container ml-auto">
                    <ul class="main_nav_list">
                        <li class="main_nav_item"><a href="../dashboard.php">Anasayfa</a></li>
                        <li class="main_nav_item"><a href="../contact.php">İletişim</a></li>

                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li class="main_nav_item"><a href="../register.php">Kayıt</a></li>
                            <li class="main_nav_item"><a href="../login.php">Giriş</a></li>
                        <?php else: ?>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <li class="main_nav_item"><a href="../admin_dashboard.php">Admin Paneli</a></li>
                                <li class="main_nav_item"><a href="../manage_users.php">Kullanıcı Yönetimi</a></li>
                            <?php elseif ($_SESSION['role'] === 'company'): ?>
                                <li class="main_nav_item"><a href="../firma/moderator_panel.php">Şirket Paneli</a></li>
                            <?php elseif ($_SESSION['role'] === 'user'): ?>
                                <li class="main_nav_item"><a href="../user_home.php">Profilim</a></li>
                            <?php endif; ?>

                            <li class="main_nav_item"><a href="../logout.php">Çıkış</a></li>
                        <?php endif; ?>
                    </ul>
                </div>


            </div>
        </div>
    </div>
</nav>