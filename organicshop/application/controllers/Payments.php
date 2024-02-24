<?php
    class Payments extends CI_Controller {
        public function __construct() {
            parent::__construct();
            $this->load->model('General');
            $this->load->model('Payment');
        }
        public function process() {
            $post = $this->General->clean();
            if (empty($post)) {
                redirect('/');
            }
            $this->load->model('Catalogue');
            list($errors, $form) = $this->Payment->validate_checkout($post);
            $status = '';
            $checkout = '';
            if (empty($errors['ship'] && empty($errors['bill']))) {
                $stock_check = $this->Payment->check_stock($post);
                if ($stock_check === 'success') {
                    $status = $this->Payment->make_payment($post);
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
                'stock_check' => $stock_check
            ));
        }

    }
?>