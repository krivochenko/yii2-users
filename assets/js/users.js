$(function() {
    $('.permission-children-editor').on('keyup', '.listFilter', function() {
        $(this).parents('.children-list').find('select option').show();

        var search = $(this).val();
        if (search != '') {
            $(this).parents('.children-list').find('select option:not(:contains("' + search + '"), [value *= "' + search + '"])').hide();
        }
    });
});