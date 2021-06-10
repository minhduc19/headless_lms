<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo $title_for_layout; ?>
        </title>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
            <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
            <?php
            echo $this->Html->meta('icon');
            echo $this->fetch('meta');
            echo $this->Html->css('font-awesome/font-awesome.min');
            echo $this->Html->css('simple-line-icons/simple-line-icons.min');
            echo $this->Html->css('bootstrap/bootstrap.min');
            echo $this->Html->css('uniform/uniform.default');
            echo $this->Html->css('bootstrap-switch/bootstrap-switch.min');
            echo $this->Html->css('select2/select2');
            echo $this->Html->css('bootstrap/dataTables.bootstrap');
            echo $this->Html->css('vendor/components');
            echo $this->Html->css('vendor/plugins');
            echo $this->Html->css('admin/layout');
            echo $this->Html->css('themes/darkblue');
            echo $this->Html->css('datatable/dataTables.colReorder.min');
            echo $this->Html->css('fancybox/jquery.fancybox');
            //echo $this->Html->css('bootstrap-toastr/toastr.min');
            echo $this->Html->css('admin/custom');
            echo $this->fetch('css');

            echo $this->Html->script('plugins/jquery.min');
            echo $this->Html->script('plugins/jquery-migrate.min');
            echo $this->Html->script('plugins/fancybox/jquery.fancybox');
            echo $this->Html->script('plugins/select2/select2.min');
            ?>
    </head>
    <body class="page-header-fixed page-quick-sidebar-over-content page-sidebar-closed-hide-logo">
        <?php
        echo $this->element('admins/default/header');
        ?>
        <div class="clearfix"></div>
        <div class="page-container">
            <div class="page-sidebar-wrapper">
                <?php echo $this->element('admins/default/left-sidebar'); ?>
            </div>
            <div class="page-content-wrapper">
                <div class="page-content">
                    <?php echo $this->fetch('content'); ?>
                </div>
            </div>
            <a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-close"></i></a>
            <div class="page-quick-sidebar-wrapper">
            </div>
        </div>
        <?php
        echo $this->element('admins/default/footer');
        ?>
        <script>

            jQuery(document).ready(function () {
                Metronic.init(); // init metronic core components
                Layout.init(); // init current layout
                QuickSidebar.init(); // init quick sidebar
                Demo.init(); // init demo features
            });
        </script>
        <?php
            echo $this->Html->script('plugins/jquery-ui/jquery-ui.min');
            echo $this->Html->script('plugins/bootstrap/bootstrap.min');
            echo $this->Html->script('plugins/jquery-slimscroll/jquery.slimscroll.min');
            echo $this->Html->script('plugins/jquery.cokie.min');
            echo $this->Html->script('plugins/uniform/jquery.uniform.min');
            echo $this->Html->script('plugins/datatables/jquery.dataTables.min');
            echo $this->Html->script('plugins/datatables/dataTables.bootstrap');
            echo $this->Html->script('plugins/bootstrap-datepicker/bootstrap-datepicker');
            echo $this->Html->script('plugins/scripts/metronic');
            echo $this->element('validation_errors_language');
            echo $this->Html->script('plugins/scripts/layout');
            echo $this->Html->script('plugins/scripts/quick-sidebar');
            echo $this->Html->script('plugins/scripts/demo');
            echo $this->Html->script('plugins/datatables/datatable');

            echo $this->Html->script('plugins/datatables/extensions/dataTables.tableTools.min.js');
            echo $this->html->script('plugins/datatables/extensions/dataTables.colReorder.min.js');
            echo $this->Html->script('table-advanced.js');
            echo $this->Html->script('bootstrap-filestyle.min');
            

            echo $this->Html->script('plugins/ckeditor/ckeditor');
            echo $this->Html->script('plugins/ckfinder/ckfinder');
            echo $this->Html->script('plugins/form-validator/jquery.form-validator');
            echo $this->Html->script('moment');
            echo $this->Html->script('ramda');
            echo $this->Html->script('uri');
            echo $this->Html->script('plugins/jquery.deserialize.min');
            echo $this->Html->script('plugins/a_clientstorage');
            echo $this->Html->script('purl');
            echo $this->Html->script('plugins/autoNumeric');
            echo $this->Html->script('general');
            
            echo $this->fetch('dataTableSettings');
            echo $this->fetch('script');
            
        ?>
    </body>
</html>