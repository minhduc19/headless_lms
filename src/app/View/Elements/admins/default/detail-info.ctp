
<?php
if (!empty($input)) {
    foreach ($input as $val) {
	?>  
	<div class="row">
	    <div class="form-group" style="margin: 10px 0px 0px 85px;">
		<label class="col-md-2 control-label text-align-left" ><?php echo $val['label']; ?>
		    <?php
		    if (isset($val['required']) && $val['required'] == TRUE) {
			echo '<span class="red"> * </span>';
		    }
		    ?>
		</label>
		<div class="<?php
		if (isset($val['description'])) {
		    echo "col-md-9 col-sm-12";
		} else {
		    echo "col-md-5 col-sm-12";
		}
		?>" <?php
		if (isset($val['checkbox'])) {
		    echo 'style="padding:10px"';
		}
		?>><?php
			 if (is_array($val['input'])) {
			     
			 } else {
			     echo $val['input'];
			 }
			 ?>
		</div>
	    </div>
	</div>
	<?php
    }
}
?>
