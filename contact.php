<!DOCTYPE html>
<html lang="tr">

<?php $title = "İletişim"; ?>
<?php include './includes/head.inc.php'; ?>
<?php require_once './includes/secureSession.inc.php'; ?>

<body>

    <?php include './includes/navbar.inc.php'; ?>


    <div class="contact-page-wrapper">
        <div class="container">
            <div class="header-text">
                <h1>Bize Ulaşın</h1>
                <p>Herhangi bir sorunuz veya öneriniz mi var? Aşağıdaki formu doldurarak bize kolayca ulaşabilirsiniz.
                </p>
            </div>

            <div class="content">

                <div class="col-1">
                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-map-location-dot"></i></div>
                        <div class="contact-info">
                            <h4>Adres</h4>
                            <p>Teknoloji Cd. No:123, 34000, Teknopark/İstanbul</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="contact-info">
                            <h4>Telefon</h4>
                            <p>+90 (555) 123 45 67</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                        <div class="contact-info">
                            <h4>E-Posta</h4>
                            <p>destek@sirketadi.com</p>
                        </div>
                    </div>
                </div>


                <div class="col-2">
                    <div class="form-container">
                        <form action="#" method="post">
                            <div class="form-row">
                                <label class="label1" for="name">Adınız Soyadınız</label>
                                <input type="text" id="name" name="name" class="form-field" required>
                            </div>
                            <div class="form-row">
                                <label class="label1" for="email">E-Posta Adresiniz</label>
                                <input type="email" id="email" name="email" class="form-field" required>
                            </div>
                            <div class="form-row">
                                <label class="label1" for="message">Mesajınız</label>
                                <textarea id="message" name="message" class="form-field" rows="2" required></textarea>
                            </div>
                            <div class='button button_color_1 button-small'>
                                <div class='button_bcg'></div>
                                <button type='submit'>
                                    Gönder
                                </button>
                            </div>

                        </form>
                        </td>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include './includes/footer.inc.php'; ?>
</body>

</html>