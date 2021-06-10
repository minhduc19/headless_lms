var ContactUs = function() {

    return {
	//main function to initiate the module
	init: function() {
	    var map;
	    $(document).ready(function() {
		map = new GMaps({
		    div: '#map',
		    lat: "<?php echo $arr_map['lat'] ?>",
		    lng: "<?php echo $arr_map['long'] ?>",
		});
		var marker = map.addMarker({
		    lat: "<?php echo $arr_map['lat'] ?>",
		    lng: "<?php echo $arr_map['long'] ?>",
		    title: 'Loop, Inc.',
		    infoWindow: {
			content: "<?php echo $arr_map['content'] ?>"
		    }
		});

		marker.infoWindow.open(map, marker);
	    });
	}
    };

}();