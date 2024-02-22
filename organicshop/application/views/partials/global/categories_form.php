                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                <input type="hidden" name="product_type" value="0" alt_name="for_button">
                <h3>Categories</h3>
                <ul>
<?php           foreach ($prod_count as $key => $val) { ?>
                    <li>
                        <button type="submit" <?= ($key == 'All Products')?'class="active" ':' ' ?>value="<?= $val[1] ?>">
                            <span><?= $val[0] ?></span><img src="<?= base_url('assets/images/' . str_replace(' ', '_', strtolower($key)) . '.png') ?>" alt="<?= str_replace(' ', '_', strtolower($key)) ?>"><h4><?= $key ?></h4>
                        </button>
                    </li>
<?php           } ?>
                </ul>