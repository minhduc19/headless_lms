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
$limit_select = $this->Form->input('limit_page', array('type' => 'select', 'default' => isset($limit_page) ? $limit_page : 10, 'options' => $arr_limit_page, 'div' => FALSE, 'label' => FALSE, 'id' => 'pagination_select', 'class' => 'form-control input-xsmall input-inline', 'style' => "height: 33px"));
$firtPage = 1;
$current = $this->Paginator->current();
$pageCount = $this->Paginator->param('pageCount');
?>
<?php
if ($pageCount > 1) {
	?>
	<!--<div class="row">-->
	<!--<div class="col-md-6">-->
	<ul class = "pagination pull-right">
		<?php
		echo $this->Paginator->first('‹‹', array('class' => '', 'tag' => 'li', 'escape' => false, 'currentClass' => 'disabled',), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
		echo $this->Paginator->prev('‹', array('class' => '', 'tag' => 'li', 'escape' => false, 'currentClass' => 'disabled',), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
		echo $this->Paginator->numbers(array('modulus' => 4, 'tag' => 'li', 'separator' => FALSE, 'currentClass' => 'active', 'currentTag' => 'a'));
		echo $this->Paginator->next('›', array('class' => '', 'tag' => 'li', 'currentClass' => 'disabled', 'escape' => false), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
		echo $this->Paginator->last('››', array('class' => '', 'tag' => 'li', 'currentClass' => 'disabled', 'escape' => false), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a', 'escape' => false));
		?>
	</ul>
	<!--</div>-->
	<!--    <div class="col-md-6">
	<div class='limit-page pull-left'>
	<?php // echo __('View') . '&nbsp;'; ?>
	<?php // echo $limit_select; ?>
	<?php // echo '&nbsp;' . __('Record'); ?>
	</div>
	</div>-->
	<!--</div>-->
	<?php
} else {
	?>
	<ul class="pagination pull-right">
		<li class="disabled"><a>‹‹</a></li>
		<li class="active"><a>1</a></li>
		<li class="disabled"><a>››</a></li>	
	</ul>
	<?php
}
?>
<script>
//    $(function() {
//	$('#pagination_select_footer').change(function() {
//	    $(this).parents('form').submit();
//	});
//    });
</script>

