<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'User';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_edit = $this->Html->link("<i class='fa fa-edit'></i>  " . __('Edit'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false));

$arrStatus = Configure::read('user_status');
$type = $this->Form->label("$model_name.type", $item[$model_name]['type'], array('class' => 'control-label', 'label' => false, 'div' => false));
$username = $this->Form->label("$model_name.username", $item[$model_name]['username'], array('class' => 'control-label', 'label' => false, 'div' => false));
$name = $this->Form->label("$model_name.name", $item[$model_name]['name'], array('class' => 'control-label', 'label' => false, 'div' => false));
$email = $this->Form->label("$model_name.email", $item[$model_name]['email'], array('class' => 'control-label', 'label' => false, 'div' => false));
$status = $this->Form->label("$model_name.status", isset($arrStatus[$item[$model_name]['status']]) ? $arrStatus[$item[$model_name]['status']] : '', array('class' => 'control-label', 'label' => false, 'div' => false));

$phone_number = $this->Form->label("$model_name.phone_number", $item[$model_name]['phone_number'], array('class' => 'control-label', 'label' => false, 'div' => false));
$avatar = $this->HtmlExtend->get_image($item[$model_name]['avatar'], array('alt' => $item[$model_name]['username'], 'width' => 84, 'height' => 58));
$birth_day = $this->Form->label("$model_name.birthday", $item[$model_name]['birthday'], array('class' => 'control-label', 'label' => false, 'div' => false));
$gxu = $this->Form->label("$model_name.gxu", $item[$model_name]['gxu'] . ' Gxu', array('class' => 'control-label', 'label' => false, 'div' => false));

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

        <?php echo $this->Form->create(null, array('class' => 'form-horizontal form-row-seperated')) ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-eye "></i>
                    <?php echo $small_title ?> :: <?php echo $item[$model_name]['name'] ?>
                </div>
                <div class="action btn-set pull-right">
                    <?php echo $btn_back ?>	
                    <?php echo $btn_edit ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="form-body">
                    <div>
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#register-info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Information') ?></a></li>
                            <li role="presentation"><a href="#game-played-info" aria-controls="profile" role="tab" data-toggle="tab"><?php echo __('Game played') ?></a></li>
                            <li role="presentation"><a href="#joined-event" aria-controls="profile" role="tab" data-toggle="tab"><?php echo __('Event joined') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="register-info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="game-played-info">
                                <?php echo $this->element('admins/default/game-played', array('input' => $played)) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="joined-event">
                                <?php echo $this->element('admins/default/joined-event', array('input' => $joined_events)) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php echo $this->Form->end() ?>
    </div>
</div>