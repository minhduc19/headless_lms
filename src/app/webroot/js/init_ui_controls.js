$(function () {
    $('.select2').select2({
        dropdownAutoWidth: 'true'
    });
    $('.select2tag').select2({
        dropdownAutoWidth: 'true',
        tags: []
    });
    $('.select2-multi').select2({
        closeOnSelect: false,
        placeholder: 'Click here to show list',
        dropdownAutoWidth: 'true'
    });
    $('.select2-view').select2({
        closeOnSelect: false,
        placeholder: '--Empty--',
        dropdownAutoWidth: 'true'
    });
    $('.autoNumeric').autoNumeric({
        mDec: 0
    });
    //fix autoNumeric
    $('form').on('submit', function () {
        var $this = $(this);
        $this.find('.autoNumeric').each(function () {
            var $this = $(this);
            $this.val($this.autoNumeric('get'));
        });
    });
});