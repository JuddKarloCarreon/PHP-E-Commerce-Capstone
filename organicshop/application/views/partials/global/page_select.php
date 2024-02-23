                    <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                    <label for="page_sel">Page:</label>
                    <button type="submit" id="page_prev"><</button>
                    <select name="page" id="page_sel">
<?php               for ($i = 1; $i <= $page['count']; $i++) { ?>
                        <option value="<?= $i ?>"<?= ($page['current'] == $i)?" selected":"" ?>><?= $i ?></option>
<?php               } ?>
                    </select>
                    <button type="submit" id="page_next">></button>