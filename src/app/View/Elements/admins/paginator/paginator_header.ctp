<?php
$limit = $this->Paginator->param('limit');
$arr_limit_page = array(
    '5' => '5',
    '10' => '10',
    '20' => '20',
    '30' => '30',
    '50' => '50',
    '100' => '100',
    '-1' => 'All'
);
$limit_select = $this->Form->input('limit_page', array('type' => 'select', 'default' => isset($limit_page) ? $limit_page : 10, 'options' => $arr_limit_page, 'div' => FALSE, 'label' => FALSE, 'id' => 'pagination_select', 'class' => 'form-control input-xsmall input-sm input-inline'));
$firtPage = 1;
$current = $this->Paginator->current();
$pageCount = $this->Paginator->param('pageCount');
?>
    <!--    <div class="col-md-6">
	    <ul class = "pagination pull-left">
    <?php
//	    echo $this->Paginator->prev('‹‹', array('class' => '', 'tag' => 'li', 'escape' => false, 'currentClass' => 'disabled',), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
//	    echo $this->Paginator->numbers(array('modulus' => 2, 'tag' => 'li', 'separator' => FALSE, 'currentClass' => 'active', 'currentTag' => 'a'));
//	    echo $this->Paginator->next('››', array('class' => '', 'tag' => 'li', 'currentClass' => 'disabled', 'escape' => false), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
    ?>
	    </ul>
	</div>-->
    <!--<div class="col-md-">-->
    <div class='dataTables_paginate paging_bootstrap_extended'>
	<?php echo __('View') . '&nbsp;'; ?>
	<?php echo $limit_select; ?>
	<?php echo '&nbsp;' . __('Record'); ?>
    </div>
    <!--</div>-->
<script>
    $(function() {
	$('#pagination_select').change(function() {
	    $(this).parents('form').submit();
	});
        $('#pagination_select').select2();
    });
</script>
