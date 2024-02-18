$(document).ready(function() {

    /* To delete a product */
    $("body").on("click", ".delete_product", function() {
        $(this).closest("tr").addClass("show_delete");
        $(".popover_overlay").fadeIn();
        $("body").addClass("show_popover_overlay");
    });

    /* To cancel delete */
    $("body").on("click", ".cancel_remove", function() {
        $(this).closest("tr").removeClass("show_delete");
        $(".popover_overlay").fadeOut();
        $("body").removeClass("show_popover_overlay");
    });

    /* To trigger input file */
    $("body").on("click", ".upload_image", function() {
        $(".image_input").trigger("click");
    });

    /* To trigger image upload */
    $("body").on("change", ".image_input", function() {
        $('.form_data_action').val("upload_image");
        $(".add_product_form").trigger("submit");
    });

    /* To delete an image */
    $("body").on("click", ".delete_image", function() {
        $("input[name=image_index]").val($(this).attr("data-image-index"));
        $('.form_data_action').val("remove_image");
        $(".add_product_form").trigger("submit");
    });

    /*  */
    $("body").on("change", "input[name=main_image]", function() {
        $("input[name=image_index]").val($(this).val());
        $(".form_data_action").val("mark_as_main");
        $(".add_product_form").trigger("submit");
    });

    $("body").on("hidden.bs.modal", "#add_product_modal", function() {
        $(".form_data_action").val("reset_form");
        $(".add_product_form").trigger("submit");
        $(".add_product_form").attr("data-modal-action", 0);
        $(".form_data_action").find("textarea").addClass("jhaver");

    });

    $("body").on("submit", ".add_product_form", function() {
        $.ajax({
            url: $(this).attr("action"),
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            success: function(res) {
                let form_data_action = $('.form_data_action').val();
                
                if(form_data_action == "add_product" || form_data_action == "edit_product") {
                    if(parseInt(res) == 0) {
                        $(".product_content").html(res);
                        resetAddProductForm();
                        $("#add_product_modal").modal("hide");
                    }
                    else {
                        $(".image_label").html("Upload Images (4 Max) <span>* Please add an image.</span>");
                    };
                }
                else if(form_data_action == "upload_image" || form_data_action == "remove_image") {
                    $(".image_preview_list").html(res);
                }
                else if(form_data_action == "reset_form") {
                    resetAddProductForm();
                };
                ($(".add_product_form").attr("data-modal-action") == 0) ? $(".form_data_action").val("add_product") : $(".form_data_action").val("edit_product");
                ($(".image_preview_list").children().length >= 4) ? $(".upload_image").addClass("hidden") : $(".upload_image").removeClass("hidden");
            }
        });
 
        return false;
    }); 

    $("body").on("submit", ".categories_form", function() {
        filterProducts(form)
        return false;
    });

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
        filterProducts($(this));
        $(".categories_form").find(".active").removeClass("active");
    });

    $("body").on("submit", ".delete_product_form", function() {
        filterProducts($(this));
        $("body").removeClass("show_popover_overlay");
        $(".popover_overlay").fadeOut();
        return false;
    });

    $("body").on("click", ".edit_product", function() {
        $("input[name=edit_product_id]").val($(this).val());
        $("#add_product_modal").modal("show");
        $(".form_data_action").val("edit_product");
        $(".add_product_form").attr("data-modal-action", 1);
        $("#add_product_modal").find("h2").text("Edit product #" + $(this).val());
    });

    $("body").on("submit", ".get_edit_data_form", function() {
        let form = $(this);
        $.post(form.attr("action"), form.serialize(), function(res) {
            $(".add_product_form").find(".form_control").html(res);
            $('.selectpicker').selectpicker('refresh');
        });

        return false;
    });

});

function resetAddProductForm() {
    $(".add_product_form").find("textarea, input[name=product_name], input[name=price], input[name=inventory]").attr("value", "").text("");
    $('select[name=categories]').find("option").removeAttr("selected").closest("select").val("1").selectpicker('refresh');
    $(".add_product_form")[0].reset();
    $(".image_label").find("span").remove();
    $(".image_preview_list").children().remove();
    $("#add_product_modal").find("h2").text("Add a Product");
};

function filterProducts(form) {
    $.post(form.attr("action"), form.serialize(), function(res) {
        $(".product_content").html(res);
        console.log(res);
    });
}