<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organic Shop: Let’s order fresh items for you.</title>

    <link rel="shortcut icon" href="assets/images/organic_shop_favicon.ico" type="image/x-icon">

    <script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap-select.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-select.min.css') ?>">

    <script src="<?= base_url('assets/js/global/global.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/custom/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom/signup.css') ?>">
</head>
<script>
</script>
<body>
    <div class="wrapper">
        <a href="<?= base_url() ?>"><img src="<?= base_url('assets/images/organic_shop_logo_large.svg') ?>" alt="Organic Shop"></a>
        <form action="<?= base_url('users/process_post') ?>" method="post">
            <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>">
            <h2>Signup to order.</h2>
            <a href="<?= base_url('login') ?>">Already a member? Login here.</a>
            <ul>
                <li>
                    <?= (!empty($errors))?$errors['first_name']:"" ?>
                    <input type="text" name="first_name" placeholder = ' ' value="<?= (!empty($form))?$form['first_name']:"" ?>">
                    <label>First Name</label>
                </li>
                <li>
                    <?= (!empty($errors))?$errors['last_name']:"" ?>
                    <input type="text" name="last_name" placeholder = ' ' value="<?= (!empty($form))?$form['last_name']:"" ?>">
                    <label>Last Name</label>
                </li>
                <li>
                    <?= (!empty($errors))?$errors['email']:"" ?>
                    <input type="text" name="email" placeholder = ' ' value="<?= (!empty($form))?$form['email']:"" ?>">
                    <label>Email</label>
                </li>
                <li>
                    <?= (!empty($errors))?$errors['password']:"" ?>
                    <input type="password" name="password" placeholder = ' '>
                    <label>Password</label>
                </li>
                <li>
                    <?= (!empty($errors))?$errors['confirm_password']:"" ?>
                    <input type="password" name="confirm_password" placeholder = ' '>
                    <label>Confirm Password</label>
                </li>
            </ul>
            <button class="signup_btn" type="submit">Signup</button>
            <input type="hidden" name="action" value="signup">
        </form>
    </div>
</body>
</html>