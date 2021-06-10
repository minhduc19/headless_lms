<?php
$email = $this->Form->label('User.email', $auth_user['email'], array('class' => 'input-sm', 'label' => FALSE, 'div' => FALSE, 'data-validation' => 'required', 'disabled'));
$type = $this->Form->label('User.type', $auth_user['type'], array('class' => 'input-sm', 'label' => FALSE, 'div' => FALSE, 'data-validation' => 'required', 'disabled'));
$status = $this->Form->label('User.status', $auth_user['status'], array('class' => 'input-sm input-small', 'label' => FALSE, 'div' => FALSE));
$name = $this->Form->label('User.name', $auth_user['name'], array('class' => 'input-sm', 'label' => false, 'div' => FALSE));


$arrInput = array(
    array('label' => __('Name'), 'input' => $name),
    array('label' => __('Email'), 'input' => $email),
    array('label' => __('Type'), 'input' => $type),
    array('label' => __('Status'), 'input' => $status)
);
?>
<?php echo $this->element('admins/default/header-body') ?>
<div class="row">
    <?php echo $this->Session->flash(); ?>
    <div class="col-md-12">
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-user"></i>
                    <?php echo __('Your profile'); ?>
                </div>
                <div class="action btn-set pull-right">
                </div>
            </div>
            <div class="portlet-body">
                <div class="tab-content no-space">
                    <div class="tab-pane active" id="tab_general">
                        <div class="form-body">
                            <?php echo $this->element("admins/default/detail-info", array('input' => $arrInput)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>