<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Lesson';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_edit = $this->Html->link("<i class='fa fa-edit'></i>  " . __('Edit'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false));

$course_text = !empty($item['Course']) ? $item['Course']['title'] : '';
if(!empty($item['Chapter'])){
    $course_text .= '  (<i>' . $item['Chapter']['title'] . '</i>)';
}
$course = $this->Form->label("$model_name.course", $course_text, array('class' => 'control-label', 'label' => false));

$title = $this->Form->label("$model_name.title", $item[$model_name]['title'], array('class' => 'control-label', 'label' => false));
$short_description = $this->Form->label("$model_name.short_description", $item[$model_name]['short_description'], array('class' => 'control-label', 'label' => false));

$lesson_tags = $this->Form->label("$model_name.tags", $tags, array('class' => 'control-label', 'label' => false));
$thumb_url = $this->HtmlExtend->get_image_from_link($item[$model_name]['thumb'], array('id' => 'icon-image-preview', 'alt' => 'thumb image', 'width' => 100));
$sort_order = $this->Form->label("$model_name.sort_order", $item[$model_name]['sort_order'], array('class' => 'control-label', 'label' => false));
$arrInput = array(
    array('label' => __('Course'), 'input' => $course),
    array('label' => __('Title'), 'input' => $title),
    array('label' => __('Short description'), 'input' => $short_description),
    array('label' => __('Tags'), 'input' => $lesson_tags), 
    array('label' => __('Sort order'), 'input' => $sort_order), 
    array('label' => __('Thumb'), 'input' => $thumb_url), 
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
                    <?php echo $small_title ?> :: <?php echo $item[$model_name]['title'] ?>
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
                            <li role="presentation" class="active"><a href="#info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Course') ?></a></li>
                            <li role="presentation"><a href="#units" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Units') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="units">
                                <?php echo $this->element('admins/default/lesson-unit-list', array()) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php echo $this->Form->end() ?>
    </div>
</div>