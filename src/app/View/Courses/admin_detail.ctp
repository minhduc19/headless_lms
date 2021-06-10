<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Course';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_edit = $this->Html->link("<i class='fa fa-edit'></i>  " . __('Edit'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false));

$title = $this->Form->label("$model_name.title", $item[$model_name]['title'], array('class' => 'control-label', 'label' => false));
$short_description = $this->Form->label("$model_name.short_description", $item[$model_name]['short_description'], array('class' => 'control-label', 'label' => false));
$description = $this->Form->label("$model_name.description", $item[$model_name]['description'], array('class' => 'control-label', 'label' => false));

$course_tags = $this->Form->label("$model_name.tags", $tags, array('class' => 'control-label', 'label' => false));

$thumb_url = $this->HtmlExtend->get_image_from_link($item[$model_name]['thumb'], array('id' => 'icon-image-preview', 'alt' => 'thumb image', 'width' => 100));
$featured = $this->Form->label("$model_name.featured", $item[$model_name]['featured'] ? 'On' : 'Off', array('class' => 'control-label', 'label' => false));
$style = $this->Form->label("$model_name.style", $item[$model_name]['style'], array('class' => 'control-label', 'label' => false));
$sort_order = $this->Form->label("$model_name.sort_order", $item[$model_name]['sort_order'], array('class' => 'control-label', 'label' => false));

$arrInput = array(
    array('label' => __('Title'), 'input' => $title),
    array('label' => __('Thumb'), 'input' => $thumb_url),
    array('label' => __('Featured'), 'input' => $featured),
    array('label' => __('Style'), 'input' => $style),
    array('label' => __('Tags'), 'input' => $course_tags),
    array('label' => __('Sort order'), 'input' => $sort_order),
    array('label' => __('Short description'), 'input' => $short_description),
    array('label' => __('Description'), 'input' => $description),
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
                            <li role="presentation"><a href="#chapters" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Chapters') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)); ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="chapters">
                                <?php echo $this->element('admins/default/course-chapter-list', array()) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php echo $this->Form->end() ?>
    </div>
</div>