$(function () {
    var $errors_wrapper = $('#errors_wrapper');
    var $errors_list = $errors_wrapper.find('#errors');
//    $('input').removeAttr('maxlength');
    $.validate({
        modules: 'html5',
        language: validation_errors_language,
        onError: function () {
            $errors_wrapper.show();
            var $invalid_inputs = $('input.error');
            var $invalid_form_groups = $invalid_inputs.parents('.form-group');
            var $errors = $invalid_form_groups.map(function () {
                var $invalid_form_group = $(this);
                var field = $invalid_form_group.find('.control-label').html();
                var error_message = $invalid_form_group.find('.form-input-error').html();
                return '<li>' + field + ': ' + error_message + '</li>';
            });
            $errors_list.html($errors.toArray().join(''));
        },
        onSuccess: function () {
            $errors_wrapper.hide();
        }
    });
});

