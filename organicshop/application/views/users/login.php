<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organic Shop: Let’s order fresh items for you.</title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/organic_shop_favicon.ico') ?>" type="image/x-icon">

    <script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap-select.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-select.min.css') ?>">

    <script src="<?= base_url('assets/js/global/dashboard.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom/signup.css') ?>">
</head>
<script>
    $(document).ready(function() {
        $("input[name=email]").focus();
        // $("form").submit(function(event) {
        //     event.preventDefault();
        //     return false;
        // });
        /* prototype add */
        // $(".login_btn").click(function() {
        //     window.location.href = "catalogue.html";
        // });
    });
</script>
<body>
    <div class="wrapper">
        <a href="<?= base_url() ?>"><img src="<?= base_url('assets/images/organic_shop_logo_large.svg') ?>" alt="Organic Shop"></a>
        <form action="<?= base_url('users/process_post') ?>" method="post" class="login_form">
            <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>">
            <h2>Login to order.</h2>
            <?= (!empty($errors))?$errors[0]:"" ?>
            <a href="<?= base_url('signup') ?>">New Member? Register here.</a>
            <ul>
                <li>
                    <input type="text" name="email" placeholder = ' ' value="<?= (!empty($form))?$form['email']:"" ?>">
                    <label>Email</label>
                </li>
                <li>
                    <input type="password" name="password" placeholder = ' '>
                    <label>Password</label>
                </li>
            </ul>
            <button type="submit" class="login_btn">Login</button>
            <input type="hidden" name="action" value="login">
        </form>
    </div>
</body>
</html>