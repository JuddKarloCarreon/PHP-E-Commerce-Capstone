$(document).ready(function() {
    /* ------------------------ STRIPE PAYMENT ------------------------ */
    /* Declare variables globally, so they can be accessed anywhere */
    var serialize = '';
    var checkout = '';
    var cart_items = '';
    var $stripeForm = $(".payment_form");
    $('form.payment_form').bind('submit', function (e) {
        var $stripeForm = $(".payment_form"),
            inputSelector = [
                'input[type=email]', 'input[type=password]',
                'input[type=text]', 'input[type=file]',
                'input[type=month]', 'input[type=number]',
                'textarea'
            ].join(', '),
            $inputs = $stripeForm.find('.required').find(inputSelector),
            $errorMessage = $stripeForm.find('p.pay_error'),
            valid = true;
        
        /* Serialize forms, to prepare so I can disable the forms later to prevent multiple submissions */
        $('form input[alt_name]').prop('disabled', true);
        checkout = $('form.checkout_form').serialize();
        cart_items = $('form.cart_items_form').serialize().replaceAll('=', '%5B%5D=');
        $('form input[alt_name]').prop('disabled', false);
        var cvc = $stripeForm.find('input[name="cvc"]').val();
        $stripeForm.find('input[name="cvc"]').val('');
        serialize = $stripeForm.serialize();
        $stripeForm.find('input[name="cvc"]').val(cvc);
        cvc = 0;
        start_forms($stripeForm);

        $stripeForm.find('p.success').addClass('disappear');
        $errorMessage.addClass('disappear');
        $('.has-error').removeClass('has-error');
        $inputs.each(function (i, el) {
            var $input = $(el);
            if ($input.val() === '') {
                $input.parent().addClass('has-error');
                $errorMessage.removeClass('disappear');
                e.preventDefault();
            }
        });
        if (!$stripeForm.data('cc-on-file')) {
            e.preventDefault();
            Stripe.setPublishableKey($stripeForm.data('stripe-publishable-key'));
            var exp = $stripeForm.find('input[name=expiration]').val().split('-');
            Stripe.createToken({
                number: $stripeForm.find('input[name=card_number]').val(),
                cvc: $stripeForm.find('input[name=cvc]').val(),
                exp_month: exp[1],
                exp_year: exp[0]
            }, stripeResponseHandler);
        }
    });
    /* Checks validity of card info, and proceeds to post to the server if card is valid */
    function stripeResponseHandler(status, res) {
        $stripeForm.find('input[name="cvc"]').val('');
        if (res.error) {
            $('.pay_error')
                .removeClass('disappear')
                .text(res.error.message);
        } else {
            var token = res['id'];
            serialize += '&' + checkout;
            serialize += '&stripeToken=' + token;
            serialize += '&' + cart_items;
            $.post($stripeForm.attr("action"), serialize, function(res) {
                $(".wrapper > section > section > ul").html(res.view);
                $('form.checkout_form').html(res.checkout_form);
                $('form.payment_form > h3 > span').text('$ ' + res.grand_total);
                $('button.show_cart').text('Cart (' + res.cart_count + ')');
                if (res.error_count > 0) {
                    $stripeForm.find('p.pay_error').removeClass('disappear').text('Errors found in checkout details.');
                }
                let err_messages = [res.stock_check, res.payment_check];
                for (i in err_messages) {
                    if (err_messages[i] != 'success' && err_messages[i] != 'succeeded') {
                        $stripeForm.find('p.pay_error').removeClass('disappear').text(err_messages[i]);
                        break;
                    }
                }
                if ($stripeForm.find('p.pay_error').hasClass('disappear')){
                    $stripeForm.find('p.success').removeClass('disappear').text('Payment successful. Order is being processed. Redirecting to catalogues...');
                    setTimeout(function () {
                        window.location.href = get_base() + '/catalogues';
                    }, 2000);
                    $('form.payment_form > input, form.payment_form > button').prop('disabled', true);
                    $('form.payment_form > button').remove();
                }
            }, 'JSON').always(function () {
                after_forms();
            });
        }
    }
    /* ------------------------ STRIPE PAYMENT END ------------------------ */

    /* Displays/hides billing fields */
    $(document).on('change', 'form.checkout_form > label > input[type="checkbox"]', function () {
        $('#billing').removeClass('disappear');
        var check = $(this).is(':checked');
        if (check) {
            $('#billing').addClass('disappear');
        }
        $('#billing input').prop('disabled', check);
    });
    /* Confirmation of removal of cart items */
    $("body").on("click", ".remove_item", function() {
        $(this).closest("ul").closest("li").addClass("confirm_delete");
        $(".popover_overlay").fadeIn();
    });
    /* Cancellation of cart item removal */
    $("body").on("click", ".cancel_remove", function() {
        $(this).closest("li").removeClass("confirm_delete");
        $(".popover_overlay").fadeOut();
        $(".cart_items_form").find("input[name=action]").val("update_cart");
    });
    /* Handles removal of cart items */
    $("body").on("click", "form.cart_items_form > div > a", function(event) {
        event.preventDefault();
        let link = $(this).attr('href');
        $('form > input, form > select, form > button, form.cart_items_form > div > a').prop('disabled', true);
        $.get(link, function (res) {
            $(".wrapper > section > section > ul").html(res.view);
            $('form.checkout_form > h4').first().children('span').text('$ ' + res.cart_total);
            $('form.checkout_form > h4.total_amount > span').text('$ ' + res.grand_total);
            $('form.payment_form > h3 > span').text('$ ' + res.grand_total);
            $('button.show_cart').text('Cart (' + res.cart_count + ')');
        }, 'JSON').always(function () {
            $('form > input, form > select, form > button, form.cart_items_form > div > a').prop('disabled', false);
            $(".popover_overlay").fadeOut();
        });
    });
    /* Handles the quantity buttons */
    $("body").on("click", ".increase_decrease_quantity", function() {
        change_quantity_button($(this));
    });
    /* Submits modification of cart items upon change in quantity value */
    $(document).on('change', 'form.cart_items_form > ul > li > input[type="text"]', function () {
        change_quantity_form($(this), $(this).closest('form'));
        $(this).closest('form').trigger('submit');
    });
    /* Handles modification of cart item quantity */
    $("body").on("submit", ".cart_items_form", function() {
        let form = $(this);
        let serialize = start_forms(form);
        $.post(form.attr("action"), serialize, function(res) {
            $(".wrapper > section > section > ul").html(res.view);
            $('form.checkout_form > h4').first().children('span').text('$ ' + res.cart_total);
            $('form.checkout_form > h4.total_amount > span').text('$ ' + res.grand_total);
            $('button.show_cart').text('Cart (' + res.cart_count + ')');
            $(".popover_overlay").fadeOut();
        }, 'JSON').always(function () {
            after_forms();
        });
        return false;
    });
    /* Shows payment modal on checkout form submit. */
    $("body").on("submit", ".checkout_form", function() {
        $(".wrapper > section").html(res);
        $("#card_details_modal").modal("show");
        return false;
    });
});