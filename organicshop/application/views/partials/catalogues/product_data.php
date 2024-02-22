                <li>
                    <img src="<?= base_url('assets/images/products/' . $main_data['id'] . '/' . $main_data['images'][0]) ?>" alt="food">
                    <ul>
<?php               foreach ($main_data['images'] as $key => $img) { ?>
                        <li class="<?= ($key == 0)?"active":"" ?>"><button class="show_image"><img src="<?= base_url('assets/images/products/' . $main_data['id'] . '/' . $img) ?>" alt="<?= $img ?>"></button></li>
<?php               } ?>
                    </ul>
                </li>
                <li>
                    <h2><?= $main_data['name'] ?></h2>
                    <ul class="rating">
<?php               for ($i = 0; $i < $main_data['full']; $i++) { ?>
                        <li></li>
<?php               } ?>
<?php               for ($i = 0; $i < $main_data['half']; $i++) { ?>
                        <li class="half_l"></li>
                        <li class="half_r"></li>
<?php               } ?>
<?php               for ($i = 0; $i < $main_data['empty']; $i++) { ?>
                        <li class="empty"></li>
<?php               } ?>
                    </ul>
                    <span><?= $main_data['rating'] ?> (<?= $main_data['rating_count'] ?> Reviews)</span>
                    <span class="amount">$ <?= $main_data['price'] ?></span>
                    <p><?= $main_data['description'] ?></p>
                    <form action="" method="post" id="add_to_cart_form">
                        <ul>
                            <li>
                                <label>Quantity</label>
                                <input type="text" min-value="1" max-value="<?= $main_data['stock'] ?>" value="1">
                                <ul>
                                    <li><button type="button" class="increase_decrease_quantity" data-quantity-ctrl="1"></button></li>
                                    <li><button type="button" class="increase_decrease_quantity" data-quantity-ctrl="0"></button></li>
                                </ul>
                            </li>
                            <li>
                                <label>Total Amount</label>
                                <span class="total_amount">$ <?= $main_data['price'] ?></span>
                            </li>
                            <li><button type="submit" id="add_to_cart">Add to Cart</button></li>
                        </ul>
                    </form>
                </li>