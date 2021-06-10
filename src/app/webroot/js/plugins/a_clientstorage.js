(function (exports) {
	function _add_to_ss_loc(page, loc, val) {
		var current = get_from_ss(page, loc, val), found = R.find(R.eqDeep(val))(current);
		if (!found) {
			current.push(val);
		}
		set_to_ss(page, loc, current);
	}
	function add_to_ss_loc(page, loc, val, is_only_keep_on_adding_page) {
		_add_to_ss_loc(page, loc, val);
		set_to_ss('__', window.location.pathname, page_loc(page, loc));
	}
	function remove_from_ss_loc(page, loc, val) {
		var current = get_from_ss(page, loc, val)
			, newArr = R.reject(R.eqDeep(val))(current);
		set_to_ss(page, loc, newArr);
	}
	function get_from_ss(page, loc, val) {
		var obj_str = sessionStorage.getItem(page_loc(page, loc)),
			current;
		if (obj_str) {
			current = JSON.parse(obj_str);
		} else {
			current = [];
		}
		return current;
	}
	function delete_from_ss(page, loc) {
		sessionStorage.removeItem(page_loc(page, loc));
	}
	function set_to_ss(page, loc, val) {
		if (R.is(Number, val) || R.is(String, val)) {
			//do nothing
		} else {
			val = JSON.stringify(val);
		}
		sessionStorage.setItem(page_loc(page, loc), val);
	}
	function page_loc(page, loc) {
		return page + '_' + loc;
	}
	function collect_gabage_ss() {
		var marks_keep_only_on_same_page = R.pickAll(R.filter(function (k) {
			return k.startsWith('__') && !k.startsWith(page_loc('__', window.location.pathname));
		}, R.keys(sessionStorage)))(sessionStorage);
		console.log(marks_keep_only_on_same_page);
		R.keys(marks_keep_only_on_same_page).forEach(function (k) {
			sessionStorage.removeItem(marks_keep_only_on_same_page[k]);
			sessionStorage.removeItem(k);
		});
	}
	exports.ClientStorage = {
		add_to_ss_loc: add_to_ss_loc,
		remove_from_ss_loc: remove_from_ss_loc,
		get_from_ss: get_from_ss,
		delete_from_ss: delete_from_ss,
		set_to_ss: set_to_ss,
		collect_gabage_ss: collect_gabage_ss
	};
})(window);