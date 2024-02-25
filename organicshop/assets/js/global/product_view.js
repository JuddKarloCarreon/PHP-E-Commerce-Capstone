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
function set_rating() {
    /* Set rating parameters */
    $('#rating-input').rating({
        min: 0,
        max: 5,
        step: 1,
        size: 'sm',
        showClear: false
    });
}
$(document).ready(function(){
    set_rating();

    $(document).on('submit', 'form.review_form, form.reply_form', function () {
        console.log('submitted');
        $.post($(this).attr('action'), $(this).serialize(), function (res) {
            console.log(res);
            if (res.hasOwnProperty('errors')) {
                $('#review_err').text(res.errors);
            } else {
                $('.wrapper > section > div').html(res.view);
                $('.wrapper > section > ul').html(res.product_data);
            }
        }, 'JSON').always(function () {
            update_csrf();
            set_rating();
        });
        return false;
    });

    /* Set title of area */
    $('body > .wrapper > section > section > h3').text('Similar Items');

    $("body").on("click", ".increase_decrease_quantity", function() {
        change_quantity_button($(this));
    });
    $(document).on('change', '#add_to_cart_form > ul > li > input[type="text"]', function () {
        change_quantity_form($(this), $(this).closest('form'));
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

