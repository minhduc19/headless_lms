<div class="portlet" id='<?php echo $id ?>'>
	<div class="portlet-title">
		<div class="caption pull-left">
			<i class="fa fa-money"></i><?php echo $title ?>
		</div>
	</div>
	<div class="portlet-body">
		<div class='panel-group accordion' id="<?php echo $acc_id ?>" role="tablist">
			<?php if (isset($body)): ?>
				<?php foreach ($body as $tag): ?>
					<?php echo $tag ?>
				<?php endforeach; ?>
			<?php endif ?>
		</div>
	</div>
</div>