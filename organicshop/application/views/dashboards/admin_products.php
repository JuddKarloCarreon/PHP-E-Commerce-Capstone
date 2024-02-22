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
    <script src="<?= base_url('assets/js/global/products.js') ?>"></script>
    <script src="<?= base_url('assets/js/global/admin_products.js') ?>"></script>
</head>
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
                <li><a href="admin_orders.html">Orders</a></li>
                <li class="active"><a href="#">Products</a></li>
            </ul>
        </aside>
        <section>
            <form action="<?= base_url('dashboards/filter') ?>" method="post" class="search_form">
                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                <input type="text" name="search" placeholder="Search Products">
            </form>
            <button class="add_product" data-toggle="modal" data-target="#add_product_modal">Add Product</button>
            <form action="<?= base_url('dashboards/filter') ?>" method="post" class="categories_form">
<?php $this->load->view('partials/global/categories_form') ?>
            </form>
            <div>
                <table class="products_table">
                    <thead>
                        <tr>
                            <th><h3>All Products</h3></th>
                            <th>ID #</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Inventory</th>
                            <th>Sold</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
<?php $this->load->view('partials/dashboards/admin_products_data'); ?>
                    </tbody>
                </table>
            </div>
        </section>
        <div class="modal fade form_modal" id="add_product_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <button data-dismiss="modal" aria-label="Close" class="close_modal"></button>
                    <form class="add_product_form" action="<?= base_url('dashboards/add_product') ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                        <input type="hidden" name="id" value="0">
                        <h2>Add a Product</h2>
                        <ul>
                            <li>
                                <?= (!empty($errors))?$errors['product_name']:"" ?>
                                <input type="text" name="product_name">
                                <label>Product Name</label>
                            </li>
                            <li>
                                <?= (!empty($errors))?$errors['description']:"" ?>
                                <textarea name="description"></textarea>
                                <label>Description</label>
                            </li>
                            <li>
                                <?= (!empty($errors))?$errors['category']:"" ?>
                                <label>Category</label>
                                <select class="selectpicker" name="category">
<?php
                            foreach ($prod_type as $key => $val) {
                                if ($key != 'All Products') {
?>
                                    <option value="<?= $val ?>"><?= $key ?></option>
<?php
                                }
                            }
?>
                                </select>
                            </li>
                            <li>
                                <?= (!empty($errors))?$errors['price']:"" ?>
                                <input type="number" name="price" value="1" required>
                                <label>Price</label>
                            </li>
                            <li>
                                <?= (!empty($errors))?$errors['stock']:"" ?>
                                <input type="number" name="stock" value="1" required>
                                <label>Inventory</label>
                            </li>
                            <li>
                                <?= (!empty($errors))?$errors['images']:"" ?>
                                <label>Upload Images (4 Max)</label>
                                <ul>
                                </ul>
                                <input type="file" id="images" name="images[]" accept="image/*" multiple>
                            </li>
                        </ul>
                        <button type="button" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="popover_overlay"></div>
</body>
</html>