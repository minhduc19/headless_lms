<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	<?php echo $this->Html->charset(); ?>
        <title>
	    <?php echo $title_for_layout; ?>
        </title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        </meta>

        <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"/>
	<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css"/>

	<?php echo $this->Html->css('font-awesome/css/font-awesome.min.css') ?>
	<?php echo $this->Html->css('bootstrap.min.css') ?>

	<?php echo $this->Html->css('fancybox/jquery.fancybox.css') ?>
	<?php echo $this->Html->css('owl.carousel.css') ?>

	<?php echo $this->Html->css('uniform/uniform.default.css') ?>
	<?php echo $this->Html->css('rateit.css') ?>
	<?php echo $this->Html->css('jquery-ui.css') ?>

	<?php echo $this->Html->css('vendor/components.css'); ?>
	<?php echo $this->Html->css('style.css') ?>    
	<?php echo $this->Html->css('style-shop.css') ?>
	<?php echo $this->Html->css('style-responsive.css') ?>
	<?php echo $this->Html->css('themes/red.css') ?>
	<?php echo $this->Html->css('custom.css') ?>

	<?php echo $this->Html->script('plugins/jquery.min.js') ?>
	<?php echo $this->Html->script('plugins/jquery-migrate.min.js') ?>
	<?php echo $this->Html->script('plugins/bootstrap/bootstrap.min.js') ?>
	<?php echo $this->Html->script('plugins/back-to-top.js') ?>
	<?php echo $this->Html->script('plugins/jquery-slimscroll/jquery.slimscroll.min.js') ?>

	<?php echo $this->Html->script('plugins/fancybox/jquery.fancybox.pack.js') ?>
	<?php echo $this->Html->script('plugins/owl.carousel.min.js') ?>
	<?php echo $this->Html->script('plugins/jquery.zoom.min.js') ?>
	<?php echo $this->Html->script('plugins/bootstrap-touchspin/bootstrap.touchspin.min.js') ?>

	<?php echo $this->Html->script('plugins/scripts/metronic.js'); ?>
	<?php echo $this->Html->script('plugins/uniform/jquery.uniform.min.js') ?>
	<?php echo $this->Html->script('plugins/jquery.rateit.js') ?>
	<?php echo $this->Html->script('plugins/jquery-ui.js') ?>
	<?php echo $this->Html->script('plugins/scripts/layout.js') ?>
	<?php echo $this->Html->script('plugins/scripts/product-detail/layout.js') ?>
    </head>
    <body class="ecommerce">
	<div class="row">
	    <div class="col-md-12">
		<?php echo $this->element('default/header_f_e') ?>
	    </div>
	</div>
	<div class="row">
	    <?php echo $this->fetch('content'); ?>
	</div>
	<div class="row">
	    <div class="col-md-12">
		<?php echo $this->element('default/footer_f_e'); ?>
	    </div>
	</div>
    </body>
</html>