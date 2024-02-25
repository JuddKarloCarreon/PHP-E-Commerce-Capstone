$(document).ready(function() {
    /* $("body").on("click", ".switch", function() {
        window.open("/dashboard", '_blank');
    }); */

    // $("body").on("submit", ".update_status_form", function() {
    //     let form = $(this);
    //     $.post(form.attr("action"), form.serialize(), function(res) {
    //         $(".wrapper > section").html(res);
    //         $(".selectpicker").selectpicker("refresh");
    //     });

    //     return false;
    // });

    // $("body").on("click", ".status_form button", function() {
    //     let button = $(this);
    //     $(".status_form").find("input[name=status_id]").val(button.val());
    //     $(".status_form").find(".active").removeClass("active");
    //     button.addClass("active");
    // })
    
    $("body").on("change", "select.selectpicker", function() {
        $(this).closest("form").trigger("submit");
    });


    $("body").on("submit", ".set_status_form", function() {
        let form = $(this);
        let serialize = form.serialize();
        serialize += '&search=' + $('.search_form input[name="search"]').val();
        $.post(form.attr("action"), serialize, function(res) {
            /* Update the count numbers */
            console.log(res);
            res = JSON.parse(res);
            console.log(res);
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