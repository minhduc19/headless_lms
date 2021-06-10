<style>
    .limit-page{margin-top: 10px;}
</style>
<?php
$limit = $this->Paginator->param('limit');
$firtPage = 1;
$current = $this->Paginator->current();
$pageCount = $this->Paginator->param('pageCount');
?>

<div class = "row">
    <div class = "col-md-4 col-sm-6">
	<ul class = "pagination">
	    <?php
	    echo $this->Paginator->prev('<', array('class' => '', 'tag' => 'li', 'escape' => false, 'currentClass' => 'disabled',), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
	    echo $this->Paginator->numbers(array('modulus' => 1, 'tag' => 'li', 'separator' => FALSE, 'currentClass' => 'active', 'currentTag' => 'a'));
	    echo $this->Paginator->next('>', array('class' => '', 'tag' => 'li', 'currentClass' => 'disabled', 'escape' => false), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
	    ?>
	</ul>
    </div>
    <div class="col-md-4 col-sm-6" style="margin-left: -85px;">
	<div class="limit-page">
	    <?php echo $this->Filter->begin_form($arrParam['action']); ?>
	    <?php echo __('View') . '&nbsp;'; ?>
	    <?php echo $this->Filter->pagination_select(array('class' => 'form-control input-xsmall input-inline', "aria-controls" => "datatable_products", 'style' => "height: 33px"), isset($limit) ? $limit : 5); ?>
	    <?php echo '&nbsp;' . __('Record'); ?>
	    <?php echo $this->Filter->end_form(); ?>
	</div>
    </div>
</div>