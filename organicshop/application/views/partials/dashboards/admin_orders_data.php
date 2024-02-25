<?php               foreach ($data as $key => $row) { ?>
                        <tr>
                            <td><span><a href="#"><?= $row['id'] ?></a></span></td>
                            <td><span><?= $row['order_date'] ?></span></td>
                            <td><span><?= $row['name'] ?><span><?= $row['address'] ?></span></span></td>
                            <td><span>$ <?= $row['total_amount'] ?></span></td>
                            <td>
                                <form action="<?= base_url('orders/set_status') ?>" method="post" class="set_status_form">
                                    <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <select name="status" class="selectpicker">
<?php                               foreach ($status_types as $key2 => $val) { ?>
                                        <option value="<?= $key2 + 1 ?>"<?= ($row['status'] == $key2 + 1)?" selected":"" ?>><?= $val ?></option>
<?php                               } ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
<?php               } ?>