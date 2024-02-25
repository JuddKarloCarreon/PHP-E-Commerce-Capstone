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

    <link rel="stylesheet" href="<?= base_url('assets/css/custom/admin_global.css') ?>">
    <script src="<?= base_url('assets/js/global/functions.js') ?>"></script>
    <script src="<?= base_url('assets/js/global/products.js') ?>"></script>
    <script src="<?= base_url('assets/js/global/admin_orders.js') ?>"></script>
</head>
<script>
    //  $(document).ready(function() {
    //     $('.profile_dropdown').on('click', function() {
    //         let newTop = $(this).offset().top + $(this).outerHeight();
    //         let newLeft = $(this).offset().left;
            
    //         $('.admin_dropdown').css({
    //             'top': newTop + 'px',
    //             'left': newLeft + 'px'
    //         });
    //     });
    // });
</script>
<body>
    <div class="wrapper">
        <header>
            <div>
                <section>
                    <h1>Let’s provide fresh items for everyone.</h1>
                    <h2>Products</h2>
                </section>
                <div>
                    <div>
                        <a class="switch" href="<?= base_url('catalogues') ?>">Switch to Shop View</a>
                    </div>
                    <div class="dropdown show">
                        <a class="btn btn-secondary dropdown-toggle profile_dropdown" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="<?= base_url('assets/images/users/' . $user['image']) ?>" alt="#">
                            <?= $user['name'] ?>
                        </a>
                        <div class="dropdown-menu admin_dropdown" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <aside>
            <a href="#"><img src="<?= base_url('assets/images/organi_shop_logo_dark.svg') ?>" alt="Organic Shop"></a>
            <ul>
                <li class="active"><a href="#">Orders</a></li>
                <li><a href="<?= base_url('dashboards') ?>">Products</a></li>
            </ul>
        </aside>
        <section>
            <form action="<?= base_url('generals/filter/order') ?>" method="post" class="search_form">
                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                <input type="text" name="search" placeholder="Search Orders">
            </form>
            <form action="<?= base_url('generals/filter/order') ?>" method="post" class="categories_form">
<?php $this->load->view('partials/global/categories_form'); ?>
            </form>
            <div>
                <form class="page" action="<?= base_url('generals/filter/order') ?>" method="post">
<?php $this->load->view('partials/global/page_select'); ?>
                </form>
                <h3>All Orders (<?= $prod_count['All Orders'][0] ?>)</h3>
                <table class="orders_table">
                    <thead>
                        <tr>
                            <th>Order ID #</th>
                            <th>Order Date</th>
                            <th>Receiver</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
<?php $this->load->view('partials/dashboards/admin_orders_data'); ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>