function get_base() {
    var base = $('.categories_form').attr('action');
    for (i = 0; i < 2; i++) {
        base = base.substring(0, base.lastIndexOf('/'));
    }
    return base;
}
function update_csrf() {
    var url = get_base();
    $.get(url + '/generals/get_csrf', function (res) {
        $('input[alt_name="csrf"]').attr('name', res.name);
        $('input[alt_name="csrf"]').attr('value', res.hash);
    }, 'JSON');
}
/* Counts data returned when using search */
function count_data() {
    /* Zero all category count */
    $('.categories_form button span').each(function () {
        $(this).text('0');
    });
    /* Counts data by category */
    var counts = {};
    var elem;
    var products = [$('.products_table tbody > tr'), $('section > div > ul > li')];
    for (var i = 0; i < products.length; i++) {
        if (products[i].length > 0) {
            elem = products[i];
            break;
        }
    }
    elem.each(function () {
        if ($(this).children('td:nth-child(4)').length > 0) {
            var text = $(this).children('td:nth-child(4)').children('span').text();
        } else {
            var text = $(this).attr('category');
        }
        if (counts[text] === undefined) {
            counts[text] = 0;
        }
        counts[text]++;
    });
    /* Sets count */
    for (var key in counts) {
        $('.categories_form').find('h4:contains("' + key + '")').siblings('span').text(counts[key]);
    }
    $('.categories_form').find('h4:contains("All Products")').siblings('span').text(elem.length);
}
$(document).ready(function () {
    $(document).on('click', '.categories_form button[type="submit"][value]', function (event) {
        event.preventDefault();
        $(this).closest('form').find('button[class="active"]').removeClass('active');
        $(this).addClass('active');
        $(this).closest('form').find('input[alt_name="for_button"]').attr('value', $(this).attr('value'))
        $(this).closest('form').trigger('submit');
    });
    $(document).on('submit', '.categories_form, .search_form', function () {
        var serialize = $(this).serialize();
        var form = $(this).attr('class');
        if (form == 'categories_form') {
            serialize += '&' + $('.search_form input[name="search"]').attr('name') + '=' + $('.search_form input[name="search"]').val();
        } else if (form == 'search_form') {
            $('.categories_form').find('button[class="active"]').removeClass('active');
            $('.categories_form').find('button[type="submit"][value="0"]').addClass('active');
        }
        $('.categories_form input, .categories_form button', '.search_form input', '.search_form button').prop('disabled', true);
        $.post($(this).closest('form').attr('action'), serialize, function (res) {
            var elem = $('section > div');
            if ($('.products_table tbody').length > 0) {
                var elem = $('.products_table tbody');
            }
            elem.html(res);
        }).always(function () {
            update_csrf();
            $('.categories_form input, .categories_form button', '.search_form input', '.search_form button').prop('disabled', false);
            /* Counts obtained data, and displays count in categories */
            if (form == 'search_form') {
                count_data();
            }
            /* Update product display label */
            var text = $('.categories_form button[class="active"] h4').text();
            if ($('.wrapper > section > div').length > 0) {
                $('.wrapper > section > div > h3').text(text + ' (' + $('.categories_form button[class="active"] span').text() + ')');
            } else {
                $('.products_table > thead > tr > th > h3').text(text);
            }
        });
        return false;
    });
    $(document).on('change', 'input[name="search"]', function () {
        $('.search_form').trigger('submit');
    });
});