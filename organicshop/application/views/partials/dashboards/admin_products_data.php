<?php
                if (!empty($data)) {
                    foreach ($data as $row) {
?>
                        <tr>
                            <td>
                                <span>
                                    <img src="<?= base_url('assets/images/' . (($row['main_img'] != '')?'products/' . $row['id'] . '/' . $row['main_img']:'close.svg')) ?>" alt="#">
                                    <?= $row['name'] ?>
                                </span>
                            </td>
                            <td><span><?= $row['id'] ?></span></td>
                            <td><span>$ <?= $row['price'] ?></span></td>
                            <td><span><?= $row['category'] ?></span></td>
                            <td><span><?= $row['stock'] ?></span></td>
                            <td><span><?= $row['sold'] ?></span></td>
                            <td>
                                <span>
                                    <button class="edit_product" get="<?= base_url('dashboards/get_record/' . $row['id']) ?>" product_id="<?= $row['id'] ?>">Edit</button>
                                    <button class="delete_product">X</button>
                                </span>
                                <form class="delete_product_form" action="<?= base_url('dashboards/delete_product') ?>" method="post">
                                <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                                    <p>Are you sure you want to remove this item?</p>
                                    <button type="button" class="cancel_remove">Cancel</button>
                                    <button type="submit" name="id" value="<?= $row['id'] ?>">Remove</button>
                                </form>
                            </td>
                        </tr>
<?php
                    }
                }
?>