<?php if (!empty($input)): ?>
    <?php foreach ($input as $val): ?>
	<div class="form-group">
	    <label class="col-md-4 control-label text-align-left" ><?php echo $val['label']; ?>
		<?php if (isset($val['required']) && $val['required'] == TRUE): ?>
	    	<span class="red"> * </span>
		<?php endif; ?>
	    </label>
	    <div class="<?php echo isset($val['description']) ? "col-md-7 col-ms-10" : "col-md-6 col-sm-12" ?>"
		 <?php echo isset($val['checkbox']) ? 'style="padding:10px"' : '' ?>>
		     <?php echo is_array($val['input']) ? '' : $val['input'] ?>
	    </div>
	</div>
    <?php endforeach; ?>
    <?php

 endif ?>