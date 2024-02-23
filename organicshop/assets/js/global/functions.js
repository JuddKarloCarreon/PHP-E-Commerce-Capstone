function get_base() {
    var base = $('.search_form').attr('action');
    for (i = 0; i < 3; i++) {
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
/* returns int, or 1 if item Nan */
function check_nan(item, def = 0) {
    if (isNaN(item)) {
        return def;
    }
    return item;
}