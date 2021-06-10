<div class="note note-success" id="success-validator">
    <?php echo h($message); ?>
    <button type="button" class="close"></button>
</div>
<script>
    $(function() {
	$('.close').click(function() {
	    $('#success-validator').addClass('hidden');
	});
    });
</script>
