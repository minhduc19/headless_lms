$(function () {
	var get_new_range = new_range_from($('.transfer-range').length),
		$add_range = $('#add-range');
		$range_first = $('.transfer-range:first').parents('.form-group');
	$('#add-range').on('click', function (e) {
		e.preventDefault();
		append_new_range();
	});
	$('.remove-range')
		.off('click')
		.on('click', on_click_delete);
	function append_new_range() {
		var $new_range = get_new_range();
		$new_range.insertBefore($add_range.parents('.form-group'));
			//.parent().append($new_range);
		$new_range.show();
		$('body').scrollTop($new_range.offset().top);
	}
	function on_click_delete(e) {
		e.preventDefault();
		$remove_range = $('.remove-range');
		if ($remove_range.length == 1) {
			alert('Sorry you cannot delete this last one');
			return;
		}
		$(this).parents('.form-group').remove();
	}
	function new_range_from(from_count) {
		from_count = from_count || 0;
		return function () {
			from_count += 1;
			var $ret = $range_first.clone();
			$ret.find('input')
				.each(function () {
					$this = $(this);
					$this.attr('name', $this.attr('name').replace(0, from_count));
				});
			$ret.find('input').val('');
			$ret.find('.remove-range').show();
			$ret.find('.remove-range').on('click', on_click_delete);
			return $ret;
		};
	}
});


