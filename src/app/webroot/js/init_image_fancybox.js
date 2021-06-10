$('[id^="Small-image"]').hover(function (e) {
    var $this = $(this);
    $this.css({"border": "2px solid #94f76c", "cursor": "zoom-in"});
});

$('[id^="Small-image"]').mouseleave(function (e) {
    var $this = $(this);
    $this.css({"border": "0px"});
});

$('[id^="Small-image"]').click(function (e) {
    $('#Image' + $(this).data('id')).click();
});
$('.fancybox-btn').fancybox({
    'hideOnContentClick': true,
    'overlayShow': true,
    'autoScale': false
});