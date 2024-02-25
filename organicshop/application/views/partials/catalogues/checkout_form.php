                    
                    <label><input type="checkbox" name="type" value="3"<?= (empty($form['bill']))?" checked":"" ?>>Same in Billing</label>
                    <div id="shipping">
                        <h3>Shipping Information</h3>
                        <ul>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_first_name']:"" ?>
                                <input type="text" name="ship_first_name" value="<?= (!empty($form['ship']))?$form['ship']['ship_first_name']:"" ?>" required>
                                <label>First Name</label>
                            </li>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_last_name']:"" ?>
                                <input type="text" name="ship_last_name" value="<?= (!empty($form['ship']))?$form['ship']['ship_last_name']:"" ?>" required>
                                <label>Last Name</label>
                            </li>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_address_1']:"" ?>
                                <input type="text" name="ship_address_1" value="<?= (!empty($form['ship']))?$form['ship']['ship_address_1']:"" ?>" required>
                                <label>Address 1</label>
                            </li>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_address_2']:"" ?>
                                <input type="text" name="ship_address_2" value="<?= (!empty($form['ship']))?$form['ship']['ship_address_2']:"" ?>" required>
                                <label>Address 2</label>
                            </li>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_city']:"" ?>
                                <input type="text" name="ship_city" value="<?= (!empty($form['ship']))?$form['ship']['ship_city']:"" ?>" required>
                                <label>City</label>
                            </li>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_state']:"" ?>
                                <input type="text" name="ship_state" value="<?= (!empty($form['ship']))?$form['ship']['ship_state']:"" ?>" required>
                                <label>State</label>
                            </li>
                            <li>
                                <?= (!empty($errors['ship']))?$errors['ship']['ship_zip_code']:"" ?>
                                <input type="text" name="ship_zip_code" value="<?= (!empty($form['ship']))?$form['ship']['ship_zip_code']:"" ?>" required>
                                <label>Zip Code</label>
                            </li>
                        </ul>
                    </div>
                    <div id="billing" class="disappear">
                        <h3>Billing Information</h3>
                        <ul>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_first_name']:"" ?>
                                <input type="text" name="bill_first_name" value="<?= (!empty($form['bill']))?$form['bill']['bill_first_name']:"" ?>" required disabled>
                                <label>First Name</label>
                            </li>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_last_name']:"" ?>
                                <input type="text" name="bill_last_name" value="<?= (!empty($form['bill']))?$form['bill']['bill_last_name']:"" ?>" required disabled>
                                <label>Last Name</label>
                            </li>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_address_1']:"" ?>
                                <input type="text" name="bill_address_1" value="<?= (!empty($form['bill']))?$form['bill']['bill_address_1']:"" ?>" required disabled>
                                <label>Address 1</label>
                            </li>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_address_2']:"" ?>
                                <input type="text" name="bill_address_2" value="<?= (!empty($form['bill']))?$form['bill']['bill_address_2']:"" ?>" required disabled>
                                <label>Address 2</label>
                            </li>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_city']:"" ?>
                                <input type="text" name="bill_city" value="<?= (!empty($form['bill']))?$form['bill']['bill_city']:"" ?>" required disabled>
                                <label>City</label>
                            </li>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_state']:"" ?>
                                <input type="text" name="bill_state" value="<?= (!empty($form['bill']))?$form['bill']['bill_state']:"" ?>" required disabled>
                                <label>State</label>
                            </li>
                            <li>
                                <?= (!empty($errors['bill']))?$errors['bill']['bill_zip_code']:"" ?>
                                <input type="text" name="bill_zip_code" value="<?= (!empty($form['bill']))?$form['bill']['bill_zip_code']:"" ?>" required disabled>
                                <label>Zip Code</label>
                            </li>
                        </ul>
                    </div>
                    <section>
                        <h3>Order Summary</h3>
                        <h4>Items <span>$ <?= $cart_total ?></span></h4>
                        <h4>Shipping Fee <span>$ <?= $shipping_fee ?></span></h4>
                        <h4 class="total_amount">Total Amount <span>$ <?= $grand_total ?></span></h4>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#card_details_modal">Proceed to Checkout</button>
                    </section>