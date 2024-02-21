function get_dashboard_url() {
    var base = $('#add_product_modal form').attr('action');
    return base.substring(0, base.lastIndexOf('/'));
}
function update_csrf() {
    var base = get_dashboard_url();
    $.get(base + '/get_csrf', function (res) {
        $('input[alt_name="csrf"]').attr('name', res.name);
        $('input[alt_name="csrf"]').attr('value', res.hash);
    }, 'JSON');
}
function prepare_form(arr) {
    var base = get_dashboard_url();
    $('#add_product_modal form').attr('action', base + '/' + arr.shift());

    var arr2 = ['h2', 'input[name="product_name"]', 'textarea', 'input[name="price"]', 'input[name="stock"]', 'input[name="id"]'];
    arr2.forEach(function (val, index) {
        $('#add_product_modal').find(val).val(arr[index]);
    });
    $('#add_product_modal').find('.selectpicker').selectpicker('refresh');
    $('#images').siblings('ul').empty();
    $('#images').val('');
    $('#add_product_modal').find('option').removeAttr('selected');
}
/* Counts data returned when using search */
function count_data() {
    /* Zero all category count */
    $('.categories_form button span').each(function () {
        $(this).text('0');
    });
    /* Counts data by category */
    var counts = {};
    $('.products_table tbody > tr').each(function () {
        var text = $(this).children('td:nth-child(4)').children('span').text();
        if (counts[text] === undefined) {
            counts[text] = 0;
        }
        counts[text]++;
    });
    /* Sets count */
    for (var key in counts) {
        $('.categories_form').find('h4:contains("' + key + '")').siblings('span').text(counts[key]);
    }
    $('.categories_form').find('h4:contains("All Products")').siblings('span').text($('.products_table tbody > tr').length);
}
$(document).ready(function () {
    if ($('.form_modal .errors').length) {
        $('.form_modal').modal('show');
    }

    function image_errors(str) {
        $('#images').parent().append($.parseHTML('<p class="errors">' + str + '</p>'));
        $('#images').val('');
        $('#images').siblings('ul').children().not('.old_img').remove();
    }

    $('#images').change(function () {
        $('#images').siblings('.errors').remove();
        var files = this.files;
        var old_imgs = $('#images').siblings('ul').children('.old_img').length;
        if (files && files.length < 5 - old_imgs) {
            $('#images').siblings('ul').children().not('.old_img').remove();
            /* Checks if all file sizes are correct. This cannot be included in the similar for loop
                below, otherwise the preview of successful files won't be deleted. */
            for (i = 0; i < files.length; i++) {
                var allowed_types = ['gif', 'jpg', 'jpeg', 'png', 'svg'];
                allowed_types = allowed_types.map(function (val) {
                    return 'image/' + val;
                });
                if (files[i].size > 2000000) {
                    image_errors(files[i].name + ' exceeds maximum size of 2MB');
                    break;
                } else if (!allowed_types.includes(files[i].type)) {
                    image_errors(files[i].name + ' is not an acceptable image');
                    break;
                }
            };
            if (!($('#images').siblings('.errors').length)) {
                for (i = 0; i < files.length; i++) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        var checked = '';
                        if (event.target.count == 0 && old_imgs == 0) {
                            checked = ' checked';
                        }
                        $('#images').siblings('ul').append($.parseHTML('<li><label><img src="' + event.target.result + '"><input type="radio" name="main_image" value="' + event.target.fileName + '"' + checked + '>Mark as main</label></li>'));
                    }
                    reader.fileName = files[i].name;
                    reader.count = i;
                    reader.readAsDataURL(files[i]);
                }
            }
        } else if (files.length + old_imgs >= 5) {
            image_errors('Maximum uploaded files exceeded');
        }
    });

    $(document).on('click', '.add_product', function () {
        prepare_form(['add_product', 'Add a Product', '', '', '1', '1', '0']);
        $('#add_product_modal').find('option').first().attr('selected', 'selected');
        $('#add_product_modal').find('.selectpicker').selectpicker('refresh');
    });

    $(document).on("click", ".edit_product", function () {
        console.log('click');
        $.get($(this).attr('get'), function (res) {
            if (res != 'null') {
                /* Set form parameters */
                prepare_form(['edit_product', 'Edit Product #' + res.id, res.name, res.description, res.price, res.stock, res.id]);
                $('#add_product_modal').find('option[value="' + res.product_type_id + '"]').attr('selected', 'selected');
                $('#add_product_modal').find('.selectpicker').selectpicker('refresh');

                /* Set old images */
                if (res.image_names_json != 'null') {
                    var base = $('#add_product_modal form').attr('action');
                    base = base.substring(0, base.lastIndexOf('dashboards/edit_product'));
                    url = base + 'assets/images/products/';
                    $.each(jQuery.parseJSON(res.image_names_json), function (key, val) {
                        var checked = '';
                        if (key == 0) {
                            checked = ' checked';
                        }
                        $('#images').siblings('ul').append($.parseHTML('<li class="old_img"><label><img src="' + url + res.id + '/' + val + '"><input type="radio" name="main_image" value="' + val + '"' + checked + '>Mark as main</label><a class="delete_image" href="' + base + 'dashboards/delete_image/' + res.id + '/' + val + '" product_id="' + res.id +'"></a></li>'));
                    });
                }
                $('.form_modal').modal('show');
            }
        }, 'JSON');
    });

    $(document).on('click', '.delete_image', function (event) {
        event.preventDefault();
        /* get .edit_product element via id */
        var id = $(this).attr('product_id');
        var parent = $('.edit_product[product_id="' + id + '"]');
        $.get($(this).attr('href'), function () {
            parent.click();
        });
    });

    $("body").on("click", ".delete_product", function () {
        $(this).closest("tr").addClass("show_delete");
        $(".popover_overlay").fadeIn();
        $("body").addClass("show_popover_overlay");
    });

    /* To cancel delete */
    $("body").on("click", ".cancel_remove", function () {
        $(this).closest("tr").removeClass("show_delete");
        $(".popover_overlay").fadeOut();
        $("body").removeClass("show_popover_overlay");
    });

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
            $('.products_table tbody').html(res);
        }).always(function () {
            update_csrf();
            $('.categories_form input, .categories_form button', '.search_form input', '.search_form button').prop('disabled', false);
            if (form == 'search_form') {
                count_data();
            }
        });
        return false;
    });
    $(document).on('change', 'input[name="search"]', function () {
        $('.search_form').trigger('submit');
    });
});

// $(document).ready(function () {

//     /* To delete a product */
//     $("body").on("click", ".delete_product", function () {
//         $(this).closest("tr").addClass("show_delete");
//         $(".popover_overlay").fadeIn();
//         $("body").addClass("show_popover_overlay");
//     });

//     /* To cancel delete */
//     $("body").on("click", ".cancel_remove", function () {
//         $(this).closest("tr").removeClass("show_delete");
//         $(".popover_overlay").fadeOut();
//         $("body").removeClass("show_popover_overlay");
//     });

//     /* To trigger input file */
//     $("body").on("click", ".upload_image", function () {
//         $(".image_input").trigger("click");
//     });

//     /* To trigger image upload */
//     $("body").on("change", ".image_input", function () {
//         $('.form_data_action').val("upload_image");
//         $(".add_product_form").trigger("submit");
//     });

//     /* To delete an image */
//     $("body").on("click", ".delete_image", function () {
//         $("input[name=image_index]").val($(this).attr("data-image-index"));
//         $('.form_data_action').val("remove_image");
//         $(".add_product_form").trigger("submit");
//     });

//     /*  */
//     $("body").on("change", "input[name=main_image]", function () {
//         $("input[name=image_index]").val($(this).val());
//         $(".form_data_action").val("mark_as_main");
//         $(".add_product_form").trigger("submit");
//     });

//     $("body").on("hidden.bs.modal", "#add_product_modal", function () {
//         $(".form_data_action").val("reset_form");
//         $(".add_product_form").trigger("submit");
//         $(".add_product_form").attr("data-modal-action", 0);
//         $(".form_data_action").find("textarea").addClass("jhaver");

//     });

//     $("body").on("submit", ".add_product_form", function () {
//         $.ajax({
//             url: $(this).attr("action"),
//             type: 'POST',
//             data: new FormData(this),
//             contentType: false,
//             cache: false,
//             processData:false,
//             success: function (res) {
//                 let form_data_action = $('.form_data_action').val();
                
//                 if(form_data_action == "add_product" || form_data_action == "edit_product") {
//                     if(parseInt(res) == 0) {
//                         $(".product_content").html(res);
//                         resetAddProductForm();
//                         $("#add_product_modal").modal("hide");
//                     }
//                     else {
//                         $(".image_label").html("Upload Images (4 Max) <span>* Please add an image.</span>");
//                     };
//                 }
//                 else if(form_data_action == "upload_image" || form_data_action == "remove_image") {
//                     $(".image_preview_list").html(res);
//                 }
//                 else if(form_data_action == "reset_form") {
//                     resetAddProductForm();
//                 };
//                 ($(".add_product_form").attr("data-modal-action") == 0) ? $(".form_data_action").val("add_product") : $(".form_data_action").val("edit_product");
//                 ($(".image_preview_list").children().length >= 4) ? $(".upload_image").addClass("hidden") : $(".upload_image").removeClass("hidden");
//             }
//         });
 
//         return false;
//     }); 

//     $("body").on("submit", ".categories_form", function () {
//         filterProducts(form)
//         return false;
//     });

//     $("body").on("click", ".categories_form button", function () {
//         let button = $(this);
//         let form = button.closest("form");

//         form.find("input[name=category]").val(button.attr("data-category"));
//         form.find("input[name=category_name]").val(button.attr("data-category-name"));
//         button.closest("ul").find(".active").removeClass("active");
//         button.addClass("active");

//         filterProducts(form);

//         return false;
//     });

//     $("body").on("keyup", ".search_form", function () {
//         filterProducts($(this));
//         $(".categories_form").find(".active").removeClass("active");
//     });

//     $("body").on("submit", ".delete_product_form", function () {
//         filterProducts($(this));
//         $("body").removeClass("show_popover_overlay");
//         $(".popover_overlay").fadeOut();
//         return false;
//     });

//     $("body").on("click", ".edit_product", function () {
//         $("input[name=edit_product_id]").val($(this).val());
//         $("#add_product_modal").modal("show");
//         $(".form_data_action").val("edit_product");
//         $(".add_product_form").attr("data-modal-action", 1);
//         $("#add_product_modal").find("h2").text("Edit product #" + $(this).val());
//     });

//     $("body").on("submit", ".get_edit_data_form", function () {
//         let form = $(this);
//         $.post(form.attr("action"), form.serialize(), function (res) {
//             $(".add_product_form").find(".form_control").html(res);
//             $('.selectpicker').selectpicker('refresh');
//         });

//         return false;
//     });

// });

// function resetAddProductForm() {
//     $(".add_product_form").find("textarea, input[name=product_name], input[name=price], input[name=inventory]").attr("value", "").text("");
//     $('select[name=categories]').find("option").removeAttr("selected").closest("select").val("1").selectpicker('refresh');
//     $(".add_product_form")[0].reset();
//     $(".image_label").find("span").remove();
//     $(".image_preview_list").children().remove();
//     $("#add_product_modal").find("h2").text("Add a Product");
// };

// function filterProducts(form) {
//     $.post(form.attr("action"), form.serialize(), function (res) {
//         $(".product_content").html(res);
//         console.log(res);
//     });
// }