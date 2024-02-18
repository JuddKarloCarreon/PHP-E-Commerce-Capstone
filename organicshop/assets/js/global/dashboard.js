$(document).ready(function() {
    $("body").on("click", ".categories_form button", function() {
        let button = $(this);
        let form = button.closest("form");

        form.find("input[name=category]").val(button.attr("data-category"));
        form.find("input[name=category_name]").val(button.attr("data-category-name"));
        button.closest("ul").find(".active").removeClass("active");
        button.addClass("active");

        filterProducts(form);

        return false;
    });

    $("body").on("keyup", ".search_form", function() {
        let form = $(this);
        filterProducts(form);
        $(".categories_form").find(".active").removeClass("active");
        return false;
    });
})

/* Ajax to filter products */
function filterProducts(form) {
    $.post(form.attr("action"), form.serialize(), function(res) {
        $(".products_container").html(res);
        console.log(res);
    });
}