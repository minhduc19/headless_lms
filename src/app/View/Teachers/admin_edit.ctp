<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Teacher';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$email = $this->Form->input("$model_name.email", array('class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$name = $this->Form->input("$model_name.name", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false));
$password = $this->Form->input("$model_name.password", array('class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$status = $this->Form->input("$model_name.status", array('options' => ['active' => 'Active', 'block' => 'Block'], 'class' => 'form-control input-sm select2', 'label' => false, 'div' => false));
$desc = $this->Form->input("$model_name.description", array('type' => 'textarea', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => false));


$arrInput = array(
    array('label' => __('Name'), 'input' => $name, 'required' => true),
    array('label' => __('Email'), 'input' => $email, 'required' => true),
    array('label' => __('Password'), 'input' => $password, 'required' => true),
    array('label' => __('Status'), 'input' => $status, 'required' => true),
    array('label' => __('Description'), 'input' => $desc),
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
                    <?php echo $small_title ?> :: <?php echo $item[$model_name]['name'] ?>
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
                            <li role="presentation" class="active"><a href="#new-info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('%s information', $model_name) ?></a></li>
                            <li role="presentation"><a href="#skills" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Skills') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="new-info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="skills">
                                <?php echo $this->element('admins/default/teacher-skills', array('input' => $item['TeacherSkill'])) ?>
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