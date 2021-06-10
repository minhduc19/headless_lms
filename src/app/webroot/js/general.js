function init_general() {
    $(function () {
//        init_moment_auto_numeric();
//        init_fancybox();
        init_select2();
        init_datepicker();
    });
}
function init_moment_auto_numeric() {
    $('.auto').autoNumeric({mDec: 0});
    $('.moment').each(function () {
        var $this = $(this);
        if ($this.hasClass('to-dmyhis')) {
            $this.text(moment($this.text()).format('DD-MM-YYYY HH:mm:ss'));
        }
    });
}

function format(state) {
    var originalOption = state.element;
    if($(originalOption).data('icon') == undefined){
        return state.text;
    }
    return "<img class='' src='" + $(originalOption).data('icon') + "' width='22px;' />&nbsp;&nbsp;&nbsp;" + state.text;
}
    
function init_select2() {
    $('select.select2').select2();
    $("select.select2image").select2({
        formatResult: format,
        formatSelection: format,
        escapeMarkup: function(m) { return m; }
    });
}
function init_datepicker() {
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
}
function init_other_fancybox() {
    $('.fancybox-button-1').fancybox();
}
function init_fancybox() {
    $('.fancybox-detail-view').fancybox({
        helpers: {
            overlay: {
                closeClick: false
            }
        }
    });
    $('.fancybox-button').fancybox({
        type: 'image'
    });
}
init_general();