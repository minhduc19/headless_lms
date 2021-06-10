<h3 class="page-title">
    <?php echo $title; ?>
    <small><?php
	if (isset($small_title)) {
	    echo $small_title;
	}
	?></small>
</h3>
<div class="page-bar">
    <?php
    if (isset($breadcrumbs)) {
	echo $this->Breadcrumb->display($breadcrumbs);
    }
    ?>
</div>