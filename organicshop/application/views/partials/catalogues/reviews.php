                <form action="<?= base_url('users/post_review/') ?>" method="post" class="review_form">
                    <h3>Leave a Review: <input id="rating-input" type="text" name="rating" title="" value="4"></h3>
                    <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                    <input type="hidden" name="product_id" value="<?= $main_data['id'] ?>">
                    <textarea name="content"></textarea>
                    <p id="review_err" class="errors"></p>
                    <button type="submit">Post</button>
                </form>
<?php       foreach ($reviews as $row) { ?>
                <h4>
                    <?= $row['name'] ?> - <?= $row['date'] ?>
                    <ul class="rating">
<?php               for ($i = 0; $i < $row['full']; $i++) { ?>
                        <li></li>
<?php               } ?>
<?php               for ($i = 0; $i < $row['empty']; $i++) { ?>
                        <li class="empty"></li>
<?php               } ?>
                    </ul>
                </h4>
                <p><?= $row['content'] ?></p>
                <div>
<?php           foreach ($row['replies'] as $row2) { ?>
                    <h4><?= $row2['name'] ?> - <?= $row2['date'] ?></h4>
                    <p><?= $row2['content'] ?></p>
<?php           } ?>
                    <form action="<?= base_url('users/post_reply/') ?>" method="post" class="reply_form">
                        <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                        <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="product_id" value="<?= $main_data['id'] ?>">
                        <textarea name="content"></textarea>
                        <p class="errors reply"></p>
                        <button type="submit">Post</button>
                    </form>
                </div>
<?php       } ?>