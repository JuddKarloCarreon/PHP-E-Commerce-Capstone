
/* Prepares the contents of the form depending on whether add or edit is clicked */
function prepare_form(arr) {
    var base = get_base();
    $('#add_product_modal form').attr('action', base + '/dashboards/' + arr.shift());

    var arr2 = ['h2', 'input[name="product_name"]', 'textarea', 'input[name="price"]', 'input[name="stock"]', 'input[name="id"]'];
    arr2.forEach(function (val, index) {
        $('#add_product_modal').find(val).val(arr[index]);
    });
    $('#add_product_modal').find('.selectpicker').selectpicker('refresh');
    $('#images').siblings('ul').empty();
    $('#images').val('');
    $('#add_product_modal').find('option').removeAttr('selected');
}
$(document).ready(function () {
    /* Shows the modal if there are errors */
    if ($('.form_modal .errors').length) {
        $('.form_modal').modal('show');
    }
    /* Handles error display */
    function image_errors(str) {
        $('#images').parent().append($.parseHTML('<p class="errors">' + str + '</p>'));
        $('#images').val('');
        $('#images').siblings('ul').children().not('.old_img').remove();
    }
    /* Handles image upload */
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
            /* Handles image preview */
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
    /* Prepares the form when the add product button is clicked */
    $(document).on('click', '.add_product', function () {
        prepare_form(['add_product', 'Add a Product', '', '', '1', '1', '0']);
        $('#add_product_modal').find('option').first().attr('selected', 'selected');
        $('#add_product_modal').find('.selectpicker').selectpicker('refresh');
    });
    /* Obtain the product data when edit product modal is opened */
    $(document).on("click", ".edit_product", function () {
        console.log('click');
        $.get($(this).attr('get'), function (res) {
            if (res != 'null') {
                /* Set form parameters */
                prepare_form(['edit_product', 'Edit Product #' + res.id, res.name, res.description, res.price, res.stock, res.id]);
                $('#add_product_modal').find('option[value="' + res.product_type + '"]').attr('selected', 'selected');
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
    /* Handles deletion of image from the edit products form */
    $(document).on('click', '.delete_image', function (event) {
        event.preventDefault();
        /* get .edit_product element via id */
        var id = $(this).attr('product_id');
        var parent = $('.edit_product[product_id="' + id + '"]');
        $.get($(this).attr('href'), function () {
            parent.click();
        });
    });
    /* Handles click event for delete product */
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
});