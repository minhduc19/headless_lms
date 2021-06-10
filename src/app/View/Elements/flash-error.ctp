<div class="Metronic-alerts alert alert-danger fade in" id="error-validator">
    <i class="fa-lg fa fa-warning"></i><?php echo h($message); ?>
    <button type="button" class="close"></button>
</div>
<script>
    $(function() {
	$('.close').click(function() {
	    $('#error-validator').addClass('hidden');
	});
    });
</script>