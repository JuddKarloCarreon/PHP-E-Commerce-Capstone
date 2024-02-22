                <h3>All Products (<?= count($data) ?>)</h3>
<?php       if (!empty($data)) { ?>
                <ul>
<?php           foreach ($data as $row) { ?>
                    <li category="<?= $row['category'] ?>">
                        <a href="<?= base_url('catalogues/product_view/' . $row['id']) ?>">
                            <img src="<?= base_url('assets/images/' . (($row['main_img'] != '')?'products/' . $row['id'] . '/' . $row['main_img']:'close.svg')) ?>" alt="#">
                            <h3><?= $row['name'] ?></h3>
                            <ul class="rating">
<?php                       for ($i = 0; $i < $row['full']; $i++) { ?>
                                <li></li>
<?php                       } ?>
<?php                       for ($i = 0; $i < $row['half']; $i++) { ?>
                                <li class="half_l"></li>
                                <li class="half_r"></li>
<?php                       } ?>
<?php                       for ($i = 0; $i < $row['empty']; $i++) { ?>
                                <li class="empty"></li>
<?php                       } ?>
                            </ul>
                            <span><?= $row['rating'] ?> (<?= $row['rating_count'] ?> Reviews)</span>
                            <span class="price">$ <?= $row['price'] ?></span>
                        </a>
                    </li>
<?php           } ?>
                </ul>
<?php       } ?>