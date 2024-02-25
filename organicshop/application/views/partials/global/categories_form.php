                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                <input type="hidden" name="<?= (array_key_exists('All Products', $prod_count))?"product_type":"status" ?>" value="0" alt_name="for_button">
                <h3><?= (array_key_exists('All Products', $prod_count))?"Categories":"Status" ?></h3>
                <ul>
<?php           foreach ($prod_count as $key => $val) { ?>
                    <li>
                        <button type="submit" <?= ($key == 'All Products' || $key == 'All Orders')?'class="active" ':' ' ?>value="<?= $val[1] ?>">
                            <span><?= $val[0] ?></span><img src="<?= base_url('assets/images/' . str_replace('-', '_', str_replace(' ', '_', strtolower($key))) . ((array_key_exists('All Products', $prod_count))?".png":"_icon.svg")) ?>" alt="<?= str_replace(' ', '_', strtolower($key)) ?>"><h4><?= $key ?></h4>
                        </button>
                    </li>
<?php           } ?>
                </ul>