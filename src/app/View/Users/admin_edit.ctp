<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'User';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));
$arrStatus = Configure::read('user_status');
$arrUserTypes = Configure::read('user_types');
$type = $this->Form->input("$model_name.type", array('options' => $arrUserTypes, 'default' => isset($item[$model_name]['type']) ? $item[$model_name]['type'] : 'normal', 'class' => 'input-sm', 'label' => false, 'div' => false));
$username = $this->Form->label("$model_name.username", $item[$model_name]['username'], array('class' => 'control-label', 'label' => false, 'div' => false));
$name = $this->Form->label("$model_name.name", $item[$model_name]['name'], array('class' => 'control-label', 'label' => false, 'div' => false));
$email = $this->Form->label("$model_name.email", $item[$model_name]['email'], array('class' => 'control-label', 'label' => false, 'div' => false));
$status = $this->Form->input("$model_name.status", array('options' => $arrStatus, 'default' => $item[$model_name]['status'], 'class' => 'input-sm', 'label' => false, 'div' => false));

$phone_number = $this->Form->label("$model_name.phone_number", $item[$model_name]['phone_number'], array('class' => 'control-label', 'label' => false, 'div' => false));
$avatar = $this->HtmlExtend->get_image($item[$model_name]['avatar'], array('alt' => $item[$model_name]['username'], 'width' => 84, 'height' => 58));
$birth_day = $this->Form->label("$model_name.birthday", $item[$model_name]['birthday'], array('class' => 'control-label', 'label' => false, 'div' => false));
//$gxu = $this->Form->label("$model_name.gxu", $item[$model_name]['gxu'], array('class' => 'control-label', 'label' => false, 'div' => false));
$gxu = $this->Form->input("$model_name.gxu", array('type' => 'number', 'default' => $item[$model_name]['gxu'], 'class' => 'form-control input-sm', 'label' => false, 'div' => false));
$arrInput = array(
    array('label' => __('Type'), 'input' => $type),
    array('label' => __('Name'), 'input' => $name),
    array('label' => __('Username'), 'input' => $username),
    array('label' => __('Gxu'), 'input' => $gxu),
    array('label' => __('Email'), 'input' => $email),
    array('label' => __('Phone number'), 'input' => $phone_number),
    array('label' => __('Birthday'), 'input' => $birth_day),
    array('label' => __('Avatar'), 'input' => $avatar),
    array('label' => __('Status'), 'input' => $status),  
);
?>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create($model_name, array('class' => 'form-horizontal form-row-seperated', 'id' => 'editForm', 'url' => array('action' => 'edit', $item[$model_name]['id']))) ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-eye "></i>
                    <?php echo $small_title ?> :: <?php echo $item[$model_name]['username'] ?>
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
                            <li role="presentation" class="active"><a href="#new-info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('User information') ?></a></li>
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

<script>
    $('#save-btn').on('click', function(e) {
        e.preventDefault();
        $('#editForm').submit();
    });
</script>