<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap-select.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-select.min.css') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/custom/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom/product_view.css') ?>">
    <script src="<?= base_url('assets/js/global/functions.js') ?>"></script>
    <script src="<?= base_url('assets/js/global/product_view.js') ?>"></script>
</head>
<body>
    <div class="wrapper">
<?php $this->load->view('partials/catalogues/basic_header') ?>
        <aside>
            <a href="<?= base_url('catalogues') ?>"><img src="<?= base_url('assets/images/organic_shop_logo.svg') ?>" alt="Organic Shop"></a>
            <!-- <ul>
                <li class="active"><a href="#"></a></li>
                <li><a href="#"></a></li>
            </ul> -->
        </aside>
        <section>
            <form action="<?= base_url('catalogues/search/') ?>" method="post" class="search_form">
                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                <input type="text" name="search" placeholder="Search Products">
            </form>
            <a class="show_cart" href="<?= base_url('cart') ?>">Cart (<?= $cart_count ?>)</a>
            <a href="<?= base_url('catalogues') ?>">Go Back</a>
            <ul>
<?php $this->load->view('partials/catalogues/product_data'); ?>
            </ul>
            <section>
<?php $this->load->view('partials/catalogues/catalogue_products'); ?>
            </section>
        </section>
    </div>
</body>
</html>