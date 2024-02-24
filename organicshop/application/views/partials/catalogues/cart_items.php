<?php               foreach ($data as $row) { ?>
                        <li>
                            <form action="<?= base_url('catalogues/modify_cart') ?>" method="post" class="cart_items_form">
                                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                                <input type="hidden" name="id" value="<?= $row['product_id'] ?>">
                                <img src="<?= base_url('assets/images/' . $row['img']) ?>" alt="">
                                <h3><?= $row['name'] ?></h3>
                                <span class="amount">$ <?= $row['price'] ?></span>
                                <ul>
                                    <li>
                                        <label>Quantity</label>
                                        <input type="text" name="amount" min-value="1" max-value="<?= $row['stock'] ?>" value="<?= $row['amount'] ?>">
                                        <ul>
                                            <li><button type="button" class="increase_decrease_quantity" data-quantity-ctrl="1"></button></li>
                                            <li><button type="button" class="increase_decrease_quantity" data-quantity-ctrl="0"></button></li>
                                        </ul>
                                    </li>
                                    <li>
                                        <label>Total Amount</label>
                                        <span class="total_amount">$ <?= $row['total'] ?></span>
                                    </li>
                                    <li>
                                        <button type="button" class="remove_item"></button>
                                    </li>
                                </ul>
                                <div>
                                    <p>Are you sure you want to remove this item?</p>
                                    <button type="button" class="cancel_remove">Cancel</button>
                                    <a href="<?= base_url('catalogues/delete_cart_item/' . $row['id']) ?>"><button type="button" class="remove">Remove</button></a>
                                </div>
                            </form>
                        </li>
<?php               } ?>