                        <input type="hidden" name="<?= $csrf['name'] ?>" value="<?= $csrf['hash'] ?>" alt_name="csrf">
                        <h2>Card Details</h2>
                        <p class="success disappear"></p>
                        <p class="pay_error disappear">Error occured while making the payment</p>
                        <ul>
                            <li>
                                <input type="text" name="card_name" required>
                                <label>Card Name</label>
                            </li>
                            <li>
                                <input type="number" name="card_number" required>
                                <label>Card Number</label>
                            </li>
                            <li>
                                <input type="month" name="expiration" required>
                                <label>Exp Date</label>
                            </li>
                            <li>
                                <input type="password" name="cvc" required>
                                <label>CVC</label>
                            </li>
                        </ul>
                        <h3>Total Amount <span>$ <?= $grand_total ?></span></h3>
                        <button type="submit">Pay</button>