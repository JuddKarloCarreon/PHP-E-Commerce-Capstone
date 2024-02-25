<?php
    class Payments extends CI_Controller {
        /* Loads models */
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Payment');
        }
        /* Handles the processing of payment, including the validation and the necessary database updates */
        public function process() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            $this->load->model('Catalogue');
            /* Validates billing and shipping */
            list($errors, $form) = $this->Payment->validate_checkout($post);
            $status = '';
            $checkout = '';
            if (empty($errors['ship'] && empty($errors['bill']))) {
                $continue = FALSE;
                /* Checks if stock is available */
                $stock_check = $this->Payment->check_stock($post);
                if ($stock_check === 'success') {
                    $continue = TRUE;
                }
                if ($continue) {
                    $continue = FALSE;
                    /* Attempts to make a payment */
                    $status = $this->Payment->make_payment($post);
                    if ($status == 'succeeded') {
                        $continue = TRUE;
                    }
                }
                /* Updates the database */
                if ($continue) {
                    $checkout = $this->Payment->add_details($post);
                }
            }
            $cart_param = $this->Catalogue->get_cart_param();
            echo json_encode(array(
                'checkout_form' => $this->load->view('partials/catalogues/checkout_form', array('errors' => $errors, 'form' => $form, 'cart_total' => $cart_param['cart_total'], 'shipping_fee' => $cart_param['shipping_fee'], 'grand_total' => $cart_param['grand_total']), TRUE),
                'view' => $this->load->view('partials/catalogues/cart_items', $cart_param, TRUE),
                'cart_count' => $cart_param['cart_count'],
                'grand_total' => $cart_param['grand_total'],
                'error_count' => (count($errors['bill']) + count($errors['ship'])),
                'stock_check' => $stock_check,
                'payment_check' => $status
            ));
        }

    }
?>