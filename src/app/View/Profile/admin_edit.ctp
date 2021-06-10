<?php
$btn_save = $this->Form->button("<i class='fa fa-check'></i>" . '&nbsp;' . __('Save'), array('type' => 'submit', 'class' => 'btn btn-sm green pull', 'div' => false, 'label' => false, 'escape' => false));
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>" . '&nbsp;' . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn btn-sm default', 'escape' => false));
$btn_change_password = $this->Html->link("<i class='fa fa-edit'></i>" . '&nbsp;' . __('Change password'), array('action' => 'change_password', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn green btn-sm', 'escape' => false));
$arr_status = Configure::read('user_status');
$id = $this->Form->input('User.id', array('value' => $auth_user['id'], 'type' => 'hidden', 'label' => FALSE));
$first_name = $this->Form->input('User.first_name', array('type' => 'text', 'value' => $auth_user['first_name'], 'placeholder' => array('text' => 'Input first name'), 'class' => 'form-control input-sm', 'label' => false, 'div' => FALSE));
$last_name = $this->Form->input('User.last_name', array('type' => 'text', 'value' => $auth_user['last_name'], 'placeholder' => array('text' => 'Input last name'), 'class' => 'form-control input-sm', 'label' => false, 'div' => FALSE));
$phone_number = $this->Form->input('User.phone_number', array('type' => 'text', 'pattern' => "(\+|\(\+)?[\d|\-|\(|\)|\s]{3,}", 'value' => $auth_user['phone_number'], 'placeholder' => array('text' => 'Input phone number'), 'class' => 'form-control input-sm input-medium', 'label' => false, 'div' => FALSE));
$address = $this->Form->input('User.address', array('type' => 'text', 'value' => $auth_user['address'], 'placeholder' => array('text' => 'Input address'), 'class' => 'form-control input-sm', 'label' => false, 'div' => FALSE));
$email = $this->Form->label('User.email', $auth_user['email'], array('class' => 'input-sm', 'label' => FALSE, 'div' => FALSE, 'data-validation' => 'required', 'disabled'));
$status = $this->Form->input('User.status', array('options' => $arr_status, isset($arr_status[$auth_user['status']]) ? $arr_status[$auth_user['status']] : "active", 'class' => 'form-control input-sm input-small select2me', 'label' => FALSE, 'div' => FALSE));
$type = $this->Form->label('User.type', $auth_user['type'], array('class' => 'input-sm', 'label' => FALSE, 'div' => FALSE, 'data-validation' => 'required', 'disabled'));

$arrInput = array(
    array('label' => FALSE, 'input' => $id),
    array('label' => __('Email'), 'input' => $email, 'required' => TRUE),
    array('label' => __("Type"), 'input' => $type),
    array('label' => __('First name'), 'input' => $first_name),
    array('label' => __('Last name'), 'input' => $last_name),
    array('label' => __("Phone number"), 'input' => $phone_number),
    array('label' => __("Address"), 'input' => $address),
    array('label' => __("Status"), 'input' => $status),
);
?>

<?php echo $this->element('admins/default/header-body') ?>
<div class="row">
    <div class="alert-success">
        <?php echo $this->Session->flash(); ?>
    </div>    
    <div class="col-md-12">
        <?php echo $this->Form->create('User', array('inputDefaults' => array('label' => false), 'novalidate' => true, 'class' => 'form-horizontal form-row-seperated', 'type' => 'POST')); ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-user"></i>
                    <?php echo __('Edit profile'); ?>
                </div>
                <div class="action btn-set pull-right">
                    <?php echo $btn_back . $btn_save . $btn_change_password;?>
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