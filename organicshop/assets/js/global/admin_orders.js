$(document).ready(function() {
    /* $("body").on("click", ".switch", function() {
        window.open("/dashboard", '_blank');
    }); */

    $("body").on("change", ".status_selectpicker", function() {
        $(this).closest("form").find("input[name=status_id]").val($(this).val());
        $(this).closest("form").trigger("submit");
    });

    $("body").on("submit", ".update_status_form", function() {
        let form = $(this);
        $.post(form.attr("action"), form.serialize(), function(res) {
            $(".wrapper > section").html(res);
            $(".selectpicker").selectpicker("refresh");
        });

        return false;
    });

    $("body").on("click", ".status_form button", function() {
        let button = $(this);
        $(".status_form").find("input[name=status_id]").val(button.val());
        $(".status_form").find(".active").removeClass("active");
        button.addClass("active");

    })

    $("body").on("submit", ".status_form", function() {
        let form = $(this);
        $.post(form.attr("action"), form.serialize(), function(res) {
            $("tbody").html(res);
            $(".selectpicker").selectpicker("refresh");
        });
        return false;
    });
});