<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'TeacherLesson';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'teacher' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'edit', 'teacher' => true, $item[$model_name]['id']), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$title = $this->Form->input("$model_name.title", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));
$thumb_url = $this->Form->input("Image.thumb", array('type' => 'file', 'default' => '', 'class' => 'form-control txtmedium image', 'id' => 'thumb-image', 'div' => FALSE, 'label' => FALSE, 'accept' => 'image/*'));
$thumb_preview = $this->HtmlExtend->get_image($item[$model_name]['thumb'], array('id' => 'thumb-image-preview', 'alt' => 'thumb image', 'width' => 84));

$selectedSkills = [];
if(!empty($item['TeacherLessonSkill'])){
    foreach ($item['TeacherLessonSkill'] as $val) {
        $selectedSkills[] = $val['teacher_skill_id'];
    }
}

$teacher_skills = $this->Form->input("$model_name.skills", array('options' => $skills, 'value' => $selectedSkills, 'empty' => '-- Select --', 'class' => 'form-control input-lg select2', 'multiple' => true,'label' => false, 'div' => false));

$arrInput = array(
    array('label' => __('Title'), 'input' => $title, 'required' => true),
    array('label' => __('Thumb'), 'input' => $thumb_url . $thumb_preview),
    array('label' => __('Skills'), 'input' => $teacher_skills),
);

?>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create($model_name, array('type' => 'file', 'class' => 'form-horizontal form-row-seperated', 'id' => 'editForm', 'url' => array('controller' => 'lessons', 'action' => 'edit', 'teacher' => true, $item[$model_name]['id']))) ?>
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
                            <li role="presentation" class="active"><a href="#info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Lesson Info') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
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