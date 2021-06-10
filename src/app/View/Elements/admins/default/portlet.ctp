<div class="panel panel-default" id='panel-<?php echo $id ?>' role="tab">
	<div class="panel-heading">
		<h4 class='panel-title'>
			<a class='accordion-toggle' data-toggle='collapse' data-parent='#<?php echo $parent_id ?>' href="#panel-body-<?php echo $id ?>"
			   <i class="fa fa-money"></i><?php echo $title ?>
				<div class="action btn-set pull-right">
					<?php echo $this->fetch($toolbar) ?>
				</div>
			</a>
		</h4>
	</div>
	<div class="panel-collapse collapse" id="panel-body-<?php echo $id ?>">
		<div class='panel-body'>
			<?php if (isset($body)): ?>
				<?php foreach ($body as $tag): ?>
					<?php echo $this->fetch($tag) ?>
				<?php endforeach; ?>
			<?php endif ?>
		</div>
	</div>
</div>