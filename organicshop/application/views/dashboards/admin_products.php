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
    <script src="<?= base_url('assets/js/global/admin_products.js') ?>"></script>
</head>
<script>
    // $(document).ready(function() {
    //     // $("form").submit(function(event) {
    //     //     event.preventDefault();
    //     //     return false;
    //     // });
    //     /* prototype add */
    //     $(".switch").click(function() {
    //         window.location.href = "products_dashboard.html";
    //     });
    // });
</script>
<body>
    <div class="wrapper">
        <header>
            <h1>Let’s provide fresh items for everyone.</h1>
            <h2>Products</h2>
            <div>
                <a class="switch" href="catalogue.html">Switch to Shop View</a>
            </div>
            <div class="dropdown show">
                <a class="btn btn-secondary dropdown-toggle profile_dropdown" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= base_url('assets/images/users/' . $user['image']) ?>" alt="#">
                </a>
                <div class="dropdown-menu admin_dropdown" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="login.html">Logout</a>
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
            <form action="process.php" method="post" class="search_form">
                <input type="text" name="search" placeholder="Search Products">
            </form>
            <button class="add_product" data-toggle="modal" data-target="#add_product_modal">Add Product</button>
            <form action="<?= base_url('dashboards/change_category') ?>" method="post" class="categories_form">
                <h3>Categories</h3>
                <ul>
<?php           foreach ($prod_count as $key => $val) { ?>
                    <li>
                        <button type="submit" class="active" name="product_type" value="<?= str_replace(' ', '_', strtolower($key)) ?>">
                            <span><?= $val ?></span><img src="<?= base_url('assets/images/' . str_replace(' ', '_', strtolower($key)) . '.png') ?>" alt="<?= str_replace(' ', '_', strtolower($key)) ?>"><h4><?= $key ?></h4>
                        </button>
                    </li>
<?php           } ?>
                </ul>
            </form>
            <div>
                <table class="products_table">
                    <thead>
                        <tr>
                            <th><h3>All Products</h3></th>
                            <th>ID #</th>
                            <th>Price</th>
                            <th>Caregory</th>
                            <th>Inventory</th>
                            <th>Sold</th>
                            <th></th>
                        </tr>
                    </thead>
<?php           if (!empty($data)) { ?>
                    <tbody>
<?php               foreach ($data as $row) { ?>
                        <tr>
                            <td>
                                <span>
                                    <img src="<?= base_url('assets/images/products/' . $data['id'] . $data['image']) ?>" alt="#">
                                    <?= $data['name'] ?>
                                </span>
                            </td>
                            <td><span><?= $data['id'] ?></span></td>
                            <td><span>$ <?= $data['price'] ?></span></td>
                            <td><span><?= $data['category'] ?></span></td>
                            <td><span><?= $data['stock'] ?></span></td>
                            <td><span><?= $data['sold'] ?></span></td>
                            <td>
                                <span>
                                    <button class="edit_product">Edit</button>
                                    <button class="delete_product">X</button>
                                </span>
                                <form class="delete_product_form" action="<?= base_url('dashboards/delete_product') ?>" method="post">
                                    <p>Are you sure you want to remove this item?</p>
                                    <button type="button" class="cancel_remove">Cancel</button>
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
<?php               } ?>
                    </tbody>
<?php           } ?>
                </table>
            </div>
        </section>
        <div class="modal fade form_modal" id="add_product_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <button data-dismiss="modal" aria-label="Close" class="close_modal"></button>
                    <form class="add_product_form" action="<?= base_url('dashboards/add_product') ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>">
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
                            foreach ($prod_count as $key => $val) {
                                if ($key != 'All Products') {
?>
                                    <option value="<?= $key ?>"><?= $key ?></option>
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
                                <label>Upload Images (5 Max)</label>
                                <ul>
                                    <li><button type="button" class="upload_image"></button></li>
                                </ul>
                                <input type="file" name="images[]" accept="image/*" multiple>
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