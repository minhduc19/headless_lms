<div class="row" id="<?php echo $id ?>">
	<?php if (!isset($is_bottom)): ?>
		<div class="col-md-12">
			<?php echo $this->Form->create(null, array('name' => 'filter', 'inputDefaults' => array('label' => false, 'div' => false))) ?>
			<?php echo __('View') . '&nbsp;'; ?>
			<?php echo
			$this->Form->select(null, array('5' => 5, '10' => 10, '20' => 20, '50' => 50, '100' => 100, '200' => 200, 'all' => __('All')), array('name' => 'limit', 'class' => 'form-control input-sm input-inline', "aria-controls" => "datatable_products", 'style' => "height: 33px", 'value' => isset($this->request->query['limit']) ? $this->request->query['limit'] : 10))
			?>
			<?php echo '&nbsp;' . __('records'); ?>
			<?php echo $this->Form->end() ?>
		</div>
	<?php endif; ?>
	<?php if (isset($is_bottom)): ?>
		<div class="col-md-5 pull-left">
			<?php echo $this->element('admins/paginator/paginator_left_footer'); ?>
		</div>

		<div class="col-md-7 text-right pull-right" >
			<?php if ($this->Paginator->param('pageCount') > 1): ?>
				<ul class="custom-pagination pagination">
					<?php
					echo $this->Paginator->first('‹‹', array('class' => '', 'tag' => 'li', 'escape' => false, 'currentClass' => 'disabled',), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
					echo $this->Paginator->prev('<', array('class' => '', 'tag' => 'li', 'escape' => false, 'currentClass' => 'disabled',), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
					echo $this->Paginator->numbers(array('modulus' => 6, 'tag' => 'li', 'separator' => FALSE, 'currentClass' => 'active', 'currentTag' => 'a'));
					echo $this->Paginator->next('>', array('class' => '', 'tag' => 'li', 'currentClass' => 'disabled', 'escape' => false), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
					echo $this->Paginator->last('››', array('class' => '', 'tag' => 'li', 'currentClass' => 'disabled', 'escape' => false), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
					?>
				</ul>
			<?php else: ?>
				<ul class="custom-pagination pagination">
					<li class="disabled"><a>‹‹</a></li>
					<li class="active"><a>1</a></li>
					<li class="disabled"><a>››</a></li>	
				</ul>
			<?php endif ?>
		</div>
	<?php endif; ?>
</div>
