//Partial form
//Usage:
//$.partialForm.create('someForm')
//$.partialForm.submit('someForm')
(function () {
	$.partialForm = {
		__forms: {},
		create: function (formName) {
			this.__forms[formName] = $("form[name='" + formName + "']");
		},
		submit: function (formName, beforeSending, override_params) {
			if (beforeSending) {
				beforeSending();
			}
			if (R.isNil(this.__forms[formName])) {
				throw new Exception('partial form named `' + formName + '` is not defined');
			}
			var data_filter_form = this.__forms[formName].map(function () {
				return $(this).serializeArray();
			});
			var params = {};
			data_filter_form.each(function (_, i) {
				if (i['name'] !== '_method' && !params[i['name']]) {
					params[i['name']] = i['value'];
				}
			});
			params = R.merge(parseUri(window.location).queryKey, params);
			if (!R.isNil(override_params)) {
				params = R.merge(params, override_params);
			}
			window.location.search = $.param(params);
		},
		destroy: function (formName) {
			this.forms[formName] = null;
		}
	};
})();

//Work with checked item inside table
//Usage
//<button data-action='someActionReceivingIds' class='btn-action'></button>
$(function () {
	$('.btn-action').on('click', function (e) {
		$this = $(this);
		$checkbox = $("table [type='checkbox']:checked");
		if ($checkbox.length == 0) {
			alert('You have to select items to execute this function');
			return;
		}
		var c = confirm("Are you sure?");
		if (c) {
			$form = $("form[name='" + $(this).data('form') + "']");
			$form.prop('action', $this.data('action'));
			$form.submit();
		}
	});

	$('.btn-action-all').on('click', function (e) {
		$this = $(this);
		var c = confirm("Are you sure?");
		if (c) {
			$form = $("form[name='" + $(this).data('form') + "']");
			$form.prop('action', $this.data('action'));
			$form.submit();
		}
	});
});

$(function () {
	//Format date
	$('.date').each(function () {
		var $this = $(this);
		$this.text(moment($this.text()).format('DD-MM-YYYY'));
		$this.val(moment($this.val()).format('DD-MM-YYYY'));
	});
});

//Calling ajax when click link button
//Usage: <a href='toSomeAction' data-confirm='someConfirm' ></a>
$(function () {
	var ACTION_ERROR = 'There is something wrong happend';
	//ajax delete button
	var SUCCESS = 1, ERROR = 0;
	var result_dom = function (type, message) {
		var mark = "danger";
		if (type === SUCCESS) {
			mark = "success";
		}
		return $("<div class='note note-" + mark + "'><p>" + message + "</div></p>").fadeIn(2000).fadeOut(5000);
	};
	var server_error_message = "<?php echo __('Có lỗi xảy ra') ?>";

	$('.btn-calling-ajax').on('click', function (e) {
		var $this = $(this), $container = $($this.data('container'));
		if ($this.data('confirm')) {
			if (!confirm($this.data('confirm'))) {
				e.preventDefault();
				return;
			}
		}
		e.preventDefault();
                var url = $(this).attr('href');
                var cascade = 0;
                if ($this.data('confirmlevel2')) {
			if (confirm($this.data('confirmlevel2'))) {
				cascade = 1;
			}
                        url =  url + '/' + cascade;
		}
		$.post(url)
			.done(function (data) {
				data = JSON.parse(data);
				if (data.redirectUrl !== undefined) {
					window.location = data.redirectUrl;
					return;
				}
				if (data.message !== undefined) {
					$container.append(result_dom(SUCCESS, data.message));
				} else {
					$container.append(result_dom(ERROR, data.error));
				}
			})
			.fail(function (err) {
				$container.append(result_dom(ERROR, ACTION_ERROR));
			});
	});
});


//Send form button
$.fn.asSubmitButton = function () {
	var $form = $('[name="' + this.data('form') + '"]'),
		self = this;
	function onClick() {
		$form.prop('action', self.data('action'));
		$form.submit();
	}
	this.on('click', onClick);
	return function () {
		self.off('click', onClick);
	};
};