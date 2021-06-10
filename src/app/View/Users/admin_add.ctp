<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'User';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$name = $this->Form->input("$model_name.name", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$type = $this->Form->input("$model_name.type", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$pubkey = $this->Form->input("$model_name.pubkey", array('type' => 'textarea', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$status = $this->Form->input("$model_name.status", array('options' => ['enable' => 'Enable', 'disable' => 'Disable'], 'class' => 'form-control input-sm select2', 'label' => false, 'div' => false));

$arrInput = array(
    array('label' => __('Name'), 'input' => $name, 'required' => true),
    array('label' => __('Type'), 'input' => $type, 'required' => true),
    array('label' => __('Status'), 'input' => $status, 'required' => true),
    array('label' => __('Pubkey'), 'input' => $pubkey, 'required' => true)
);
?>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create($model_name, array('type' => 'file', 'class' => 'form-horizontal form-row-seperated', 'id' => 'appForm', 'url' => array('controller' => 'users', 'action' => 'add'))) ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-eye "></i>
                    <?php echo $small_title ?>
                </div>
                <div class="action btn-set pull-right">
                    <?php echo $this->Form->hidden("$model_name.id") ?>
                    <?php echo $btn_back ?> 
                    <?php echo $btn_save ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-body">
                    <div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#new-info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Bot Info') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="new-info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php echo $this->Form->end() ?>
</div>

<?php echo $this->Html->script('init_validation') ?>
<?php echo $this->Html->script('init_ui_controls') ?>

<script type="text/javascript">
    $('#save-btn').on('click', function(e) {
        e.preventDefault();
        $('#appForm').submit();
    });

</script>