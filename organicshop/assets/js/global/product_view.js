function message(mess) {
    $('span.added_to_cart').remove();
    $('<span class="added_to_cart">' + mess + '</span>')
    .insertAfter($('#add_to_cart'))
    .fadeIn()
    .delay(3000)
    .fadeOut(function() {
        $(this).remove();
    });
}
$(document).ready(function(){
    $('body > .wrapper > section > section > h3').text('Similar Items');

    $("body").on("click", ".increase_decrease_quantity", function() {
        let input = $(this).closest('ul').siblings('input');
        let input_val = parseInt(input.val());
        if($(this).attr("data-quantity-ctrl") == 1) {
            input.val(input_val + 1);
        } else {
            if(input_val != 1) {
                input.val(input_val - 1)
            }
        };
        input.trigger('change');
    });
    $(document).on('change', '#add_to_cart_form > ul > li > input[type="text"]', function () {
        /* Ensures the value in the input is an integer */
        let stock = parseInt($(this).closest('form').siblings('span.stock').text());
        stock = check_nan(stock);
        let value = check_nan(parseInt($(this).val()));
        $(this).val(value);
        if (value > stock) {
            message('Maximum amount reached.');
            $(this).val(stock);
        } else if (value < 1) {
            $(this).val(1);
        }
        /* total amount is scaled up by 100 to avoid using floating point */
        let total_amount = (parseInt($(this).val()) * parseInt(($(".amount").text()).substring(2).replace('.', ''))).toString();
        total_amount = total_amount.slice(0,-2) + '.' + total_amount.slice(-2);
        $("#add_to_cart_form").find(".total_amount").text("$ " + total_amount);
    });

    $("body").on("click", ".show_image", function() {
        let show_image_btn = $(this);
        show_image_btn.closest("ul").find(".active").removeClass("active");
        show_image_btn.closest("li").addClass("active");
        show_image_btn.closest("ul").closest("li").children().first().attr("src", show_image_btn.find("img").attr("src"));
    });

    $("body").on("submit", "#add_to_cart_form", function() {
        let form = $(this);
        $.post(form.attr("action"), form.serialize(), function(res) {
            message(res);
            if (res=='Successfully added to cart.') {
                /* Get number of cart items */
                let cart_num = $('a.show_cart').text();
                cart_num = check_nan(parseInt(cart_num.substring(6, cart_num.lastIndexOf(')'))), 0);
                /* Set cart items */
                $('a.show_cart').text('Cart (' + (cart_num + 1) + ')');
            }
        }, 'JSON').always(function () {
            update_csrf();
        });

        return false;
    });
});

