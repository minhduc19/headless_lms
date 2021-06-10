<?php
$current = $this->Paginator->current();
$pageCount = $this->Paginator->param('pageCount');
$limit = $this->Paginator->param('limit');
$count = $this->Paginator->param('count');
$from = '';
if ($current == 1) {
    $from = 1;
} else {
    $from = (($current - 1) * $limit) + 1;
}
$to = '';
if ($current == 1 && $count < ($current * $limit)) {
    $to = $count;
} elseif ($current == $pageCount) {
    $to = $count;
} elseif ($limit == -1) {
    $to = $count;
} else {
    $to = $current * $limit;
}
?>
<div class="margin-top-5">
    <?php echo __('Showing') . '  ' . $from . '  ' . __('to') . '  ' . $to . '   ' . __('of') . '  ' . $count . '   ' . __('entries') ?>
</div>