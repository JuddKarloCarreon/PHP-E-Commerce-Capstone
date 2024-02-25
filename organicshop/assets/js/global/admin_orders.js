$(document).ready(function() {
    /* Submits when a different item is selected */
    $("body").on("change", "select.selectpicker", function() {
        $(this).closest("form").trigger("submit");
    });
    /* Handles submission of new status */
    $("body").on("submit", ".set_status_form", function() {
        let form = $(this);
        let serialize = form.serialize();
        /* Include search contents for ajax purposes */
        serialize += '&search=' + $('.search_form input[name="search"]').val();
        $.post(form.attr("action"), serialize, function(res) {
            /* Update the count numbers */
            res = JSON.parse(res);
            $('form.categories_form button span').each(function (index) {
                $(this).text(res[index][0]);
            });
            /* Trigger change in categories to refresh data shown, but not before refreshing csrf */
            var url = get_base();
            $.get(url + '/generals/get_csrf', function (res) {
                $('input[alt_name="csrf"]').attr('name', res.name);
                $('input[alt_name="csrf"]').attr('value', res.hash);
                $('form.categories_form').trigger('submit');
            }, 'JSON');
        }).always(function () {
            update_csrf();
        });
        return false;
    });
});