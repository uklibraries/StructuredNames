jQuery(document).bind("omeka:elementformload", function () {
    jQuery("input[data-type='name']").each(function () {
        var $ = jQuery;

        function updateName(element_id, key) {
            var parent_id = '#' + $(element_id).data('parent');
            var value = $.parseJSON($(parent_id).val());
            value[key] = $(element_id).val();
            value = JSON.stringify(value);
            $(parent_id).val(value);
        }

        var first_id = '#' + jQuery(this).data('first');
        $(first_id).change(function () {
            updateName(first_id, 'first');
        }).focusout(function () {
            updateName(first_id, 'first');
        });

        var middle_id = '#' + jQuery(this).data('middle');
        $(middle_id).change(function () {
            updateName(middle_id, 'middle');
        }).focusout(function () {
            updateName(middle_id, 'middle');
        });

        var last_id = '#' + jQuery(this).data('last');
        $(last_id).change(function () {
            updateName(last_id, 'last');
        }).focusout(function () {
            updateName(last_id, 'last');
        });
    });
});
