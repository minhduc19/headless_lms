<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Teacher';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$name = $this->Form->input("$model_name.name", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$email = $this->Form->input("$model_name.email", array('type' => 'email', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$desc = $this->Form->input("$model_name.description", array('type' => 'textarea', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => false));
$status = $this->Form->input("$model_name.status", array('options' => ['active' => 'Active', 'block' => 'Block'], 'class' => 'form-control input-sm select2', 'label' => false, 'div' => false, 'required' => true));
$password = $this->Form->input("$model_name.password", array('type' => 'password', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));

$arrInput = array(
    array('label' => __('Name'), 'input' => $name, 'required' => true),
    array('label' => __('Email'), 'input' => $email, 'required' => true),
    array('label' => __('Password'), 'input' => $password, 'required' => true),
    array('label' => __('Status'), 'input' => $status, 'required' => true),
    //array('label' => __('Skills'), 'input' => $skills, 'required' => true),
    array('label' => __('Description'), 'input' => $desc),
);
?>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create($model_name, array('type' => 'file', 'class' => 'form-horizontal form-row-seperated', 'id' => 'appForm', 'url' => array('controller' => 'teachers', 'action' => 'add'))) ?>
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
                            <li role="presentation" class="active"><a href="#new-info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Teacher Info') ?></a></li>
                            <li role="presentation"><a href="#skills" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Skills') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="new-info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="skills">
                                <?php echo $this->element('admins/default/teacher-skills', array()) ?>
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