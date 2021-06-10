<?php echo $this->element('admins/default/header-body') ?>
<?php

$model_name = 'TeacherLesson';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'teacher' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_edit = $this->Html->link("<i class='fa fa-edit'></i>  " . __('Edit'), array('action' => 'edit', 'teacher' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false));

$title = $this->Form->label("$model_name.title", $item[$model_name]['title'], array('class' => 'control-label', 'label' => false));

$arr_skills = [];
if(!empty($item['TeacherLessonSkill'])){
    foreach($item['TeacherLessonSkill'] as $val){
        if(isset($skills[$val['teacher_skill_id']])){
            $arr_skills[] = $skills[$val['teacher_skill_id']];
        }
    }
}
$skills = $this->Form->label("$model_name.skills", implode(", ", $arr_skills), array('class' => 'control-label', 'label' => false));
$thumb_url = $this->HtmlExtend->get_image_from_link($item[$model_name]['thumb'], array('id' => 'icon-image-preview', 'alt' => 'thumb image', 'width' => 100));
//$sort_order = $this->Form->label("$model_name.sort_order", $item[$model_name]['sort_order'], array('class' => 'control-label', 'label' => false));
$arrInput = array(
    array('label' => __('Title'), 'input' => $title),
    array('label' => __('Skills'), 'input' => $skills), 
    //array('label' => __('Sort order'), 'input' => $sort_order), 
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
                            <li role="presentation" class="<?= $active == 'info' ? 'active' : ''?>"><a href="#info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Lesson Info') ?></a></li>
                            <li role="presentation" class="<?= $active == 'scenes' ? 'active' : ''?>"><a href="#scenes" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Scenes') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?= $active == 'info' ? 'active' : ''?>" id="info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane <?= $active == 'scenes' ? 'active' : ''?>" id="scenes">
                                <?php echo $this->element('admins/default/teacher-lesson-scenes', array()) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php echo $this->Form->end() ?>
    </div>
</div>