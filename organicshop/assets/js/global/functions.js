/* These are the functions generally used by most pages */

/* Obtain the base url via the search form */
function get_base() {
    var base = $('.search_form').attr('action');
    for (i = 0; i < 3; i++) {
        base = base.substring(0, base.lastIndexOf('/'));
    }
    return base;
}
/* Obtains and sets the csrf data */
function update_csrf() {
    var url = get_base();
    $.get(url + '/generals/get_csrf', function (res) {
        $('input[alt_name="csrf"]').attr('name', res.name);
        $('input[alt_name="csrf"]').attr('value', res.hash);
    }, 'JSON');
}
/* returns int, or 1 if item Nan */
function check_nan(item, def = 0) {
    if (isNaN(item)) {
        return def;
    }
    return item;
}
/* Handles the changing of quantity for cart items */
function change_quantity_button(elem) {
    let input = elem.closest('ul').siblings('input');
    let input_val = parseInt(input.val());
    if(elem.attr("data-quantity-ctrl") == 1) {
        input.val(input_val + 1);
    } else {
        if(input_val != 1) {
            input.val(input_val - 1)
        }
    };
    input.trigger('change');
}
/* Handles changes in the quantity field of the item */
function change_quantity_form(elem, form) {
    /* Ensures the value in the input is an integer */
    let stock = parseInt(elem.attr('max-value'));
    stock = check_nan(stock);
    let value = check_nan(parseInt(elem.val()));
    elem.val(value);
    if (value > stock) {
        if ($('#add_to_cart').length > 0) {
            message('Maximum amount reached.');
        }
        elem.val(stock);
    } else if (value < 1) {
        elem.val(1);
    }
    /* total amount is scaled up by 100 to avoid using floating point */
    let amount = $(elem).closest('ul').siblings("span.amount");
    if (amount.length == 0) {
        amount = $('span.amount');
    }
    let total_amount = (parseInt(elem.val()) * parseInt((amount.text()).substring(2).replace('.', ''))).toString();
    total_amount = total_amount.slice(0,-2) + '.' + total_amount.slice(-2);
    form.find(".total_amount").text("$ " + total_amount);
}
/* Serializes form then disables them to prevent multiple inputs */
function start_forms(form) {
    var serialize = form.serialize();
    $('form input, form select, form button').prop('disabled', true);
    return serialize;
}
/* Updates the csrf and enables the form items */
function after_forms() {
    update_csrf();
    $('form input, form select, form button').prop('disabled', false);
}