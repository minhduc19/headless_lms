<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Lesson';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'admin' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$course = $this->Form->input("$model_name.course_id", array('options' => $courses, 'id' => 'selectCourse', 'class' => 'form-control input-sm select2', 'empty' => '--Select--', 'label' => false, 'div' => false, 'required' => true));
$chapter = $this->Form->input("$model_name.chapter_id", array('options' => $chapters, 'id' => 'selectChapter', 'class' => 'form-control input-sm select2', 'label' => false, 'div' => false, 'required' => true));

$title = $this->Form->input("$model_name.title", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));

$thumb_url = $this->Form->input("Image.thumb", array('type' => 'file', 'default' => '', 'class' => 'form-control txtmedium image', 'id' => 'thumb-image', 'div' => FALSE, 'label' => FALSE, 'accept' => 'image/*'));
$thumb_preview = $this->HtmlExtend->get_image($item[$model_name]['thumb'], array('id' => 'thumb-image-preview', 'alt' => 'thumb image', 'width' => 84));

$short_desc = $this->Form->input("$model_name.short_description", array('type' => 'textarea', 'class' => 'form-control input-lg ckeditor', 'label' => false, 'div' => false));

$selectedTags = [];
if(!empty($item['LessonTag'])){
    foreach ($item['LessonTag'] as $tag) {
        $selectedTags[] = $tag['tag_id'];
    }
}
$tags = $this->Form->input("$model_name.tags", array('options' => $tags, 'value' => $selectedTags, 'empty' => '-- Select --', 'class' => 'form-control input-lg select2', 'multiple' => true,'label' => false, 'div' => false));
$sort_order = $this->Form->input("$model_name.sort_order", array('type' => 'number', 'class' => 'form-control input-small', 'label' => false, 'div' => false, 'min' => 0));

$arrInput = array(
    array('label' => __('Course'), 'input' => $course, 'required' => true),
    array('label' => __('Chapter'), 'input' => $chapter),
    array('label' => __('Title'), 'input' => $title, 'required' => true),
    array('label' => __('Thumb'), 'input' => $thumb_url . $thumb_preview),
    array('label' => __('Tags'), 'input' => $tags),
    array('label' => __('Sort order'), 'input' => $sort_order),
    array('label' => __('Short Description'), 'input' => $short_desc, 'class' => 'col-lg-9'),
);

?>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create($model_name, array('type' => 'file', 'class' => 'form-horizontal form-row-seperated', 'id' => 'editForm', 'url' => array('action' => 'edit', $item[$model_name]['id']))) ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-eye "></i>
                    <?php echo $small_title ?> :: <?php echo $item[$model_name]['title'] ?>
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
                            <li role="presentation" class="active"><a href="#info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('%s Info', $model_name) ?></a></li>
                            <li role="presentation"><a href="#units" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Units') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="units">
                                <?php echo $this->element('admins/default/lesson-units', array('input' => $item['Unit'])) ?>
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
    function previewImage(input, type) {
        if (input.files && input.files[0]) {
            var max_size = '<?php echo $this->HtmlExtend->parse_size(Configure::read('upload_max_size')); ?>';
            var size = input.files[0].size;
            if (size > max_size) {
                alert('<?php echo __("Maximum image size is %s, Please select other image", Configure::read('upload_max_size')); ?>');
                input.val('');
            }
            else {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + type + '-image-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    }
    $("#thumb-image").change(function() {
        previewImage(this, 'thumb');
    });
    
    $("#selectCourse").change(function(){
        var course_id = $(this).val();
        $.ajax({
            type: "GET",
            url: "<?php echo $this->Html->url(array('controller' => 'lessons', 'action' => 'admin_ajax_get_chapters')); ?>/" + course_id,
            dataType: 'Html',
            success: function(data) {
                $('#selectChapter').empty().trigger("change");
                var json = JSON.parse(data);
                $.each(json, function(index, value){
                    var newOption = new Option(value, index, false, false);
                    $('#selectChapter').append(newOption).trigger('change');
                });
            }
        });
    });
    
</script>