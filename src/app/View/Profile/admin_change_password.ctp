<?php
$btn_save = $this->Form->button("<i class='fa fa-check'></i>" . '&nbsp;' . __('Save'), array('type' => 'submit', 'class' => 'btn btn-sm green pull', 'div' => false, 'label' => false, 'escape' => false));
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>" . '&nbsp;' . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn btn-sm default', 'escape' => false));
$current_password = $this->Form->input('Admin.current_password', array('type' => 'password', "data-validation" => "required", 'class' => 'form-control input-sm txtmedium', 'div' => FALSE, 'label' => FALSE));
$password = $this->Form->input('Admin.password', array('type' => 'password', "data-validation" => "required", 'class' => 'form-control input-sm txtmedium', 'div' => FALSE, 'label' => FALSE));
$confirm_password = $this->Form->input('Admin.confirm_password', array('type' => 'password', "data-validation" => "required", 'class' => 'form-control input-sm txtmedium', 'div' => FALSE, 'label' => FALSE));
$arrInput = array(
    array('label' => __('Old password'), 'input' => $current_password),
    array('label' => __('New password'), 'input' => $password),
    array('label' => __('Confirm new password'), 'input' => $confirm_password)
);

?>

<?php echo $this->element('admins/default/header-body') ?>
<div class="row">
    <div class="alert-success">
        <?php echo $this->Session->flash(); ?>
    </div>    
    <div class="col-md-12">
        <?php echo $this->Form->create('Admin', array('inputDefaults' => array('label' => false), 'novalidate' => true, 'class' => 'form-horizontal form-row-seperated', 'type' => 'POST')); ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-user"></i>
                    <?php echo __('Change password'); ?>
                </div>
                <div class="action btn-set pull-right">
                    <?php echo $btn_back . $btn_save ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="tab-content no-space">
                    <div class="tab-pane active" id="tab_general">
                        <div class="form-body">
                            <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>
<script>
    var myLanguage = {
        requiredFields: '<?php echo __('Fields is required'); ?>',
    };
    $.validate({
        language: myLanguage,
        onValidate: function() {
            $('#error-validator').removeClass('hidden');
        }
    });

    $(function() {
        $('.close').click(function() {
            $('#error-validator').addClass('hidden');
        });
    });

</script>