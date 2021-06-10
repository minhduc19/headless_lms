<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Tag';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$name = $this->Form->input("$model_name.name", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$popular = $this->Form->input("$model_name.popular", array('type' => 'number', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true, 'min' => 0));

$arrInput = array(
    array('label' => __('Name'), 'input' => $name, 'required' => true),
    array('label' => __('Short order'), 'input' => $popular, 'required' => true)
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