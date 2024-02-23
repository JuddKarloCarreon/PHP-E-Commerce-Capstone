$(document).ready(function () {
    $(document).on('click', 'form.page > button', function (event) {
        event.preventDefault();
        var min = $(this).siblings('select').children('option').first().val();
        var max = $(this).siblings('select').children('option').last().val();
        var current = $(this).siblings('select').children('option[selected]');
        var change = false;
        if ($(this).attr('id') == 'page_prev' && parseInt(current.val()) > min) {
            current.prev().attr('selected', 'selected');
            change = true;
        } else if ($(this).attr('id') == 'page_next' && parseInt(current.val()) < max) {
            current.next().attr('selected', 'selected');
            change = true;
        }
        if (change) {
            current.removeAttr('selected');
            $(this).closest('form').trigger('submit');
        }

    });
    $(document).on('click', '.categories_form button[type="submit"][value]', function (event) {
        event.preventDefault();
        $(this).closest('form').find('button[class="active"]').removeClass('active');
        $(this).addClass('active');
        $(this).closest('form').find('input[alt_name="for_button"]').attr('value', $(this).attr('value'));
        $('form.page > select > option[selected]').removeAttr('selected');
        $('form.page > select > option').first().attr('selected', 'selected');
        $(this).closest('form').trigger('submit');
    });
    $(document).on('submit', 'form.categories_form, form.search_form, form.page', function () {
        var serialize = $(this).serialize();
        var form = $(this).attr('class');
        if (form == 'categories_form') { /* Add search value to include in post */
            serialize += '&' + $('.search_form input[name="search"]').attr('name') + '=' + $('.search_form input[name="search"]').val();
        } else if (form == 'search_form') { /* Reset active category */
            $('.categories_form').find('button[class="active"]').removeClass('active');
            $('.categories_form').find('button[type="submit"][value="0"]').addClass('active');
        } else if (form == 'page') { /* Add search value and product_type to include in post */
            serialize += '&' + $('.search_form input[name="search"]').attr('name') + '=' + $('.search_form input[name="search"]').val();
            serialize += '&product_type=' + $('form.categories_form').find('button[class="active"]').val();
        }
        console.log(serialize);
        $('.categories_form input, .categories_form button', '.search_form input', '.search_form button').prop('disabled', true);
        $.post($(this).closest('form').attr('action'), serialize, function (res) {
            var elem = $('section > div');
            if ($('.products_table tbody').length > 0) {
                var elem = $('.products_table tbody');
            }
            elem.html(res.data);
            $('#page_sel').html(res.page);
            /* Counts obtained data, and displays count in categories */
            if (form == 'search_form') {
                $('form.categories_form').html(res.categories);
            }
        }, 'JSON').always(function () {
            update_csrf();
            $('.categories_form input, .categories_form button', '.search_form input', '.search_form button').prop('disabled', false);
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
    $(document).on('change', 'input[name="search"], form.page > select', function () {
        $(this).closest('form').trigger('submit');
    });
});