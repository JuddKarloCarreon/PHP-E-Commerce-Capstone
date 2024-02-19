<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <link rel="shortcut icon" href="<?= base_url('assets/images/organic_shop_fav.ico') ?>" type="image/x-icon">

    <script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/popper.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/vendor/bootstrap-select.min.js') ?>"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-select.min.css') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/custom/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom/product_dashboard.css') ?>">
</head>

<script>
    $(document).ready(function() {
    })
</script>
<body>
    <div class="wrapper">
        <header>
            <h1>Let’s order fresh items for you.</h1>
<?php   if (empty($user)) {?>
            <div>
                <a class="signup_btn" href="<?= base_url('signup') ?>">Signup</a>
                <a class="login_btn" href="<?= base_url('login') ?>">Login</a>
            </div>
<?php   } else { ?>
            <div class="dropdown show">
                <a class="btn btn-secondary dropdown-toggle profile_dropdown" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= base_url('assets/images/users/' . $user['image']) ?>" alt="#">
                </a>
                <section class="dropdown-menu user_dropdown" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a>
                </section>
            </div>
<?php   } ?>
        </header>
        <aside>
            <a href="<?= base_url('/') ?>"><img src="<?= base_url('assets/images/organic_shop_logo.svg') ?>" alt="Organic Shop"></a>
            <!-- <ul>
                <li class="active"><a href="#"></a></li>
                <li><a href="#"></a></li>
            </ul> -->
        </aside>
        <section>
            <form action="<?= base_url('dashboards/process_post') ?>" method="post" class="search_form">
                <input type="text" name="search" placeholder="Search Products">
            </form>
            <a class="show_cart" href="cart.html">Cart (0)</a>
            <form action="<?= base_url('dashboards/process_post') ?>" method="post" class="categories_form">
                <h3>Categories</h3>
                <ul>
                    <li>
                        <button type="submit" class="active">
                            <span>36</span><img src="<?= base_url('assets/images/apple.png') ?>" alt="#"><h4>All Products</h4>
                        </button>
                    </li>
                    <li>
                        <button type="submit">
                            <span>36</span><img src="<?= base_url('assets/images/apple.png') ?>" alt="#"><h4>Vegetables</h4>
                        </button>
                    </li>
                    <li>
                        <button type="submit">
                            <span>36</span><img src="<?= base_url('assets/images/apple.png') ?>" alt="#"><h4>Fruits</h4>
                        </button>
                    </li>
                    <li>
                        <button type="submit">
                            <span>36</span><img src="<?= base_url('assets/images/apple.png') ?>" alt="#"><h4>Pork</h4>
                        </button>
                    </li>
                    <li>
                        <button type="submit">
                            <span>36</span><img src="<?= base_url('assets/images/apple.png') ?>" alt="#"><h4>Beef</h4>
                        </button>
                    </li>
                    <li>
                        <button type="submit">
                            <span>36</span><img src="<?= base_url('assets/images/apple.png') ?>" alt="#"><h4>Chicken</h4>
                        </button>
                    </li>
                </ul>
            </form>
            <div>
                <h3>All Products(46)</h3>
                <ul>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                    <li>
                        <a href="product_view.html">
                            <img src="<?= base_url('assets/images/food.png') ?>" alt="#">
                            <h3>Vegetables</h3>
                            <ul class="rating">
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                            <span>36 Rating</span>
                            <span class="price">$ 10</span>
                        </a>
                    </li>
                </ul>
            </div>
        </section>
    </div>
</body>
</html>