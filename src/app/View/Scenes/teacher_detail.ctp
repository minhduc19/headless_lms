<?php echo $this->element('admins/default/header-body') ?>
<?php
$types = Configure::read('scene_types');
$model_name = 'TeacherScene';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'teacher' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_edit = $this->Html->link("<i class='fa fa-edit'></i>  " . __('Edit'), array('action' => 'edit', 'teacher' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false));

$lesson = $this->Form->label("$model_name.lesson", isset($item['TeacherLesson']) ? $item['TeacherLesson']['title'] : '', array('class' => 'control-label', 'label' => false));
$title = $this->Form->label("$model_name.title", $item[$model_name]['title'], array('class' => 'control-label', 'label' => false));
$content = $this->Form->label("$model_name.content", $item[$model_name]['content'], array('class' => 'control-label', 'label' => false));
$type = $this->Form->label("$model_name.type", isset($types[$item[$model_name]['type']]) ? $types[$item[$model_name]['type']] : '', array('class' => 'control-label', 'label' => false));
$group = $this->Form->label("$model_name.group_code", $item[$model_name]['group_code'], array('class' => 'control-label', 'label' => false));

$thumb_url = $this->HtmlExtend->get_image_from_link($item[$model_name]['thumb'], array('id' => 'icon-image-preview', 'alt' => 'thumb image', 'width' => 100));
$sort_order = $this->Form->label("$model_name.sort_order", $item[$model_name]['sort_order'], array('class' => 'control-label', 'label' => false));
$arrInput = array(
    array('label' => __('Lesson'), 'input' => $lesson),
    array('label' => __('Type'), 'input' => $type),
    array('label' => __('Thumb'), 'input' => $thumb_url), 
    array('label' => __('Sort order'), 'input' => $sort_order), 
    array('label' => __('Title'), 'input' => $title),
    array('label' => __('Content'), 'input' => $content), 
    array('label' => __('Group Code'), 'input' => $group),
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
                            <li role="presentation" class="active"><a href="#info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Scene info') ?></a></li>
                            <li role="presentation"><a href="#media" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Medias') ?></a></li>
                            <li role="presentation"><a href="#feedback" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Feedbacks') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="media">
                                <?php echo $this->element('admins/default/scene-media-list', array()) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="feedback">
                                <?php echo $this->element('admins/default/scene-feedback-list', array()) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php echo $this->Form->end() ?>
    </div>
</div>