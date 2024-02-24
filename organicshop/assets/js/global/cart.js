$(document).ready(function() {
    // console.log($('form.cart_items_form').serialize().replaceAll('=', '%5B%5D='));
    /* ------------------------ STRIPE PAYMENT ------------------------ */
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
    function stripeResponseHandler(status, res) {
        if (res.error) {
            console.log(res.error);
            $('.pay_error')
                .removeClass('disappear')
                .text(res.error.message);
        } else {
            var token = res['id'];
            $stripeForm.find('input[name=cvc]').empty();
            $('form input[alt_name]').prop('disabled', true);
            var checkout = $('form.checkout_form').serialize();
            var cart_items = $('form.cart_items_form').serialize().replaceAll('=', '%5B%5D=');
            $('form input[alt_name]').prop('disabled', false);
            let serialize = start_forms($stripeForm);
            serialize += '&' + checkout;
            serialize += '&stripeToken=' + token;
            serialize += '&' + cart_items;
            $.post($stripeForm.attr("action"), serialize, function(res) {
                console.log(res);
                // $(".wrapper > section > section > ul").html(res.view);
                // $('form.checkout_form').html(res.checkout_form);
                // $('form.payment_form > h3 > span').text('$ ' + res.grand_total);
                // $('button.show_cart').text('Cart (' + res.cart_count + ')');
                // if (res.error_count > 0) {
                //     $stripeForm.find('p.pay_error').removeClass('disappear').text('Errors found in checkout details.');
                // }
                // if (res.stock_check != 'success') {
                //     $stripeForm.find('p.pay_error').removeClass('disappear').text(res.stock_check);
                // }
                // if ($stripeForm.find('p.pay_error').hasClass('disappear')){
                //     $stripeForm.find('p.success').removeClass('disappear').text('Payment successful. Order is being processed.');
                // }
            }, 'JSON').always(function () {
                after_forms();
                // $('form.cart_items_form > input[alt_name="csrf"]').prop('disabled', false);
            });
            // console.log($stripeForm.get(0));
            // $stripeForm.get(0).submit();
        }
    }
    /* ------------------------ STRIPE PAYMENT END ------------------------ */

    /* Form submit handler for payment. Also includes contents of the checkout form */
    // $(document).on('submit', 'form.payment_form', function () {
    //     let form = $(this);
    //     let serialize = start_forms(form);
    //     serialize += '&' + $('form.checkout_form').serialize();
    //     // console.log(serialize);
    //     // after_forms();
    //     $.post(form.attr("action"), serialize, function(res) {
    //         console.log(res.error_count);
    //         $(".wrapper > section > section > ul").html(res.view);
    //         $('form.checkout_form').html(res.checkout_form);
    //         $('form.payment_form > h3 > span').text('$ ' + res.grand_total);
    //         $('button.show_cart').text('Cart (' + res.cart_count + ')');
    //         if (res.error_count > 0) {
    //             form.find('p.pay_error').removeClass('disappear').text('Errors found in checkout details.');
    //         } else {
    //             form.find('p.success').removeClass('disappear').text('Payment successful. Order is being processed.');
    //         }
    //     }, 'JSON').always(function () {
    //         after_forms();
    //     });
    //     return false;
    // });
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
    $("body").on("submit", ".pay_form", function() {
        let form = $(this);
        let serialize = start_forms(form);
        $(this).find("button").addClass("loading");
        $.post(form.attr("action"), serialize, function(res) {
            setTimeout(function(res) {
                $("#card_details_modal").find("button").removeClass("loading").addClass("success").find("span").text("Payment Successfull!");
            }, 2000, res);
            setTimeout(function(res) {
                $("#card_details_modal").modal("hide");
            }, 3000, res);
            setTimeout(function(res) {
                $(".wrapper > section").html(res);
            }, 3200, res);
        }).after(function () {
            after_forms();
        });
        return false;
    });
});