                <li>
                    <img src="<?= base_url('assets/images/' . $main_data['images'][0]) ?>" alt="food">
                    <ul>
<?php               foreach ($main_data['images'] as $key => $img) { ?>
                        <li class="<?= ($key == 0 && $img != 'close.svg')?"active":"" ?>"><button class="show_image"<?= ($img == 'close.svg')?" disabled":"" ?>><img src="<?= base_url('assets/images/' . $img) ?>" alt="<?= $img ?>"></button></li>
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
                    <form action="<?= base_url('catalogues/add_cart') ?>" method="post" id="add_to_cart_form">
                        <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                        <input type="hidden" name="id" value="<?= $main_data['id'] ?>">
                        <ul>
                            <li>
                                <label>Quantity</label>
                                <input type="text" min-value="1" max-value="<?= $main_data['stock'] ?>" name="amount" value="1">
                                <ul>
                                    <li><button type="button" class="increase_decrease_quantity" data-quantity-ctrl="1"></button></li>
                                    <li><button type="button" class="increase_decrease_quantity" data-quantity-ctrl="0"></button></li>
                                </ul>
                            </li>
                            <li>
                                <label>Total Amount</label>
                                <span class="total_amount">$ <?= $main_data['price'] ?></span>
                            </li>
                            <li><button type="submit" id="add_to_cart"<?= (intval($main_data['stock']) < 1)?" disabled":"" ?>>Add to Cart</button></li>
                        </ul>
                    </form>
                </li>