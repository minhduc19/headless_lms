<?php
if($auth_user['type'] == 'admin'){
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
} else {
    $btn_save = $this->Form->button("<i class='fa fa-check'></i>" . '&nbsp;' . __('Save'), array('type' => 'submit', 'class' => 'btn btn-sm green pull', 'div' => false, 'label' => false, 'escape' => false));
    $btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>" . '&nbsp;' . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn btn-sm default', 'escape' => false));
    $name = $this->Form->input('User.name', array('type' => 'text', 'value' => $auth_user['name'], 'placeholder' => array('text' => 'Input first name'), 'class' => 'form-control input-sm', 'label' => false, 'div' => FALSE));
    $email = $this->Form->label('User.email', $auth_user['email'], array('class' => 'input-sm', 'label' => FALSE, 'div' => FALSE, 'data-validation' => 'required', 'disabled'));
    $avatar = $this->Form->input("Image.avatar", array('type' => 'file', 'default' => '', 'class' => 'form-control txtmedium image', 'id' => 'thumb-image', 'div' => FALSE, 'label' => FALSE, 'accept' => 'image/*'));
    $avatar_preview = $this->HtmlExtend->get_image($auth_user['avatar'], array('id' => 'thumb-image-preview', 'alt' => 'thumb image', 'width' => 84));

    //$user_skills = array_keys(json_decode($auth_user['skills'], true));
    //$skills = $this->Form->input('User.skills', array('type' => 'text', 'value' => implode(",", $user_skills), 'class' => 'form-control input-sm input-small select2tag', 'label' => FALSE, 'div' => FALSE));

    $arrInput = array(
        array('label' => __('Email'), 'input' => $email),
        array('label' => __("Name"), 'input' => $name),
        //array('label' => __("Skills"), 'input' => $skills),
        array('label' => __("Avatar"), 'input' => $avatar . $avatar_preview),
    );
}
?>

<?php echo $this->element('admins/default/header-body') ?>
<div class="row">
    <div class="alert-success">
        <?php echo $this->Session->flash(); ?>
    </div>    
    <div class="col-md-12">
        <?php echo $this->Form->create('User', array('type' => 'file', 'class' => 'form-horizontal form-row-seperated', 'id' => 'editForm')) ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-user"></i>
                    <?php echo __('Edit profile'); ?>
                </div>
                <div class="action btn-set pull-right">
                    <?php if(isset($btn_back)) echo $btn_back . $btn_save?>
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
<?php echo $this->Html->script('init_validation') ?>
<?php echo $this->Html->script('init_ui_controls') ?>
<script>
    var myLanguage = {
        requiredFields: '<?php echo __('Fields is required'); ?>',
    };

    $(function() {
        $('.close').click(function() {
            $('#error-validator').addClass('hidden');
        });
    });

</script>