<?php
if (!isset($partial_filter_form_name)) {
	$partial_filter_form_name = 'filter';
}
?>
<script>
	$(function () {

		var action_name = "<?php echo $arrParam['action'] ?>",
			table_action_form = "<?php echo $table_action_form ?>",
			delete_check_all = '#' + action_name + '-delete-check',
			delete_check = '.' + action_name + '-delete-check';
			console.log(table_action_form);
		partial_filter_form_name = "<?php echo $partial_filter_form_name ?>";
		var $table = $('#' + action_name),
			$form = $table.find('[name="' + table_action_form + '"]');
			console.log(action_name );
		var $delete_check = $(delete_check, $table);
		$('[name="limit"]', $table).on('change', function (e) {
			$('[name="limit"]').val($(this).val());
			$.partialForm.submit(partial_filter_form_name, null, {'page': 1});
		});
		$('[name="limit"]').select2();
		$.partialForm.create("<?php echo $partial_filter_form_name ?>");
		$(delete_check_all, $table).on('click', function () {
			$this = $(this);
			if ($this.prop('checked')) {
				$(delete_check, $table).filter(function () {
					return !$(this).prop('checked');
				}).click();
			} else {
				$(delete_check, $table).filter(function () {
					return $(this).prop('checked');
				}).click();
			}
		});
		function remember_checkbox($checkbox) {
			ClientStorage.add_to_ss_loc(action_name, 'ids', $checkbox.serializeArray()[0]);
		}
		function forget_checkbox($checkbox) {
			ClientStorage.remove_from_ss_loc(action_name, 'ids', {name: $checkbox.attr('name'), value: 'on'});
		}
		$delete_check.on('change', function () {
			$this = $(this);
			if ($this.is(':checked')) {
				remember_checkbox($this);
			} else {
				forget_checkbox($this);
			}
		});

		ClientStorage.collect_gabage_ss();
		var prev_selected = ClientStorage.get_from_ss(action_name, 'ids');
		$form.deserialize(prev_selected);
		prev_selected.forEach(function (select) {
			toggle_hidden_selected(select);
		});
		function toggle_hidden_selected(select) {
			var $checkbox = $('[name="' + select['name'] + '"]'),
				is_found = $checkbox.length;
			if (!is_found) {
				$form.append("<input type='hidden' name='" + select['name'] + "' value='on' />");
			}
		}
		$(':checkbox', $form).uniform();
	});
</script> 

<script>
	$(function ($) {
		$('.auto').autoNumeric('init');
	});
</script>

