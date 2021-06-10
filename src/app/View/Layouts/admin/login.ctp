<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	<?php echo $this->Html->charset(); ?>
        <title>
	    <?php echo $title_for_layout; ?>
        </title>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
	<?php
	echo $this->Html->meta('icon');
	echo $this->fetch('meta');
	echo $this->Html->css('admin/custom');
	echo $this->Html->css('admin/login');
	echo $this->Html->css('bootstrap/bootstrap.min');
        echo $this->Html->css('font-awesome/font-awesome.min');
	echo $this->Html->css('vendor/components');
	echo $this->Html->css('themes/darkblue');
	echo $this->fetch('css');
        
        
        echo $this->Html->script('plugins/jquery.min');
	echo $this->fetch('script');
	?>
    </head>
    <body class="login">
	<div class="menu-toggler sidebar-toggler">
	</div>
	<div class="logo">
	</div>
	<div class="content">
	    <?php echo $this->fetch('content'); ?>
	</div>
	<div class="copyright">
	    <?php echo date('Y');?> © necampus.com
	</div>
    </body>
</html>