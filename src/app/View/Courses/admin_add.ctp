<?php echo $this->element('admins/default/header-body') ?>
<?php
$model_name = 'Course';
$btn_back = $this->Html->link("<i class='fa fa-angle-left'></i>  " . __('Back'), array('action' => 'index', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn default margin-bottom-5 btn-sm', 'escape' => false));
$btn_save = $this->Html->link("<i class='fa fa-check'></i>  " . __('Save'), array('action' => 'add', 'admin' => true), array('div' => false, 'label' => false, 'class' => 'btn green margin-bottom-5 btn-sm', 'escape' => false, 'id' => 'save-btn'));

$title = $this->Form->input("$model_name.title", array('type' => 'text', 'class' => 'form-control input-sm', 'label' => false, 'div' => false, 'required' => true));

$thumb_url = $this->Form->input("Image.thumb", array('type' => 'file', 'default' => '', 'class' => 'form-control txtmedium image', 'id' => 'thumb-image', 'div' => FALSE, 'label' => FALSE, 'accept' => 'image/*'));
$thumb_preview = $this->HtmlExtend->get_image('', array('id' => 'thumb-image-preview', 'alt' => 'thumb image', 'width' => 84));

$short_desc = $this->Form->input("$model_name.short_description", array('type' => 'textarea', 'class' => 'form-control input-lg ckeditor', 'label' => false, 'div' => false));
$desc = $this->Form->input("$model_name.description", array('type' => 'textarea', 'class' => 'form-control input-lg ckeditor', 'label' => false, 'div' => false));
$tags = $this->Form->input("$model_name.tags", array('options' => $tags, 'empty' => '-- Select --', 'class' => 'form-control input-lg select2', 'multiple' => true,'label' => false, 'div' => false));

$featured = $this->Form->input("$model_name.featured", array('type' => 'checkbox', 'class' => 'form-control input-sm', 'label' => false, 'div' => false));
$style = $this->Form->input("$model_name.style", array('options' => ['vertical' => 'Vertical', 'horizontal' => 'Horizontal'], 'class' => 'form-control input-sm select2', 'label' => false, 'div' => false));
$sort_order = $this->Form->input("$model_name.sort_order", array('type' => 'number', 'class' => 'form-control input-small', 'label' => false, 'div' => false, 'min' => 0, 'value' => 0));

$arrInput = array(
    array('label' => __('Title'), 'input' => $title, 'required' => true),
    array('label' => __('Thumb'), 'input' => $thumb_url . $thumb_preview),
    array('label' => __('Featured'), 'input' => $featured),
    array('label' => __('Style'), 'input' => $style),
    array('label' => __('Tags'), 'input' => $tags),
    array('label' => __('Sort order'), 'input' => $sort_order),
    array('label' => __('Short Description'), 'input' => $short_desc, 'class' => 'col-lg-9'),
    array('label' => __('Description'), 'input' => $desc, 'class' => 'col-lg-9'),
);
?>

<div class="row">
    <div class="col-md-12">
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->Form->create($model_name, array('type' => 'file', 'class' => 'form-horizontal form-row-seperated', 'id' => 'appForm', 'url' => array('action' => 'add'))) ?>
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
                            <li role="presentation" class="active"><a href="#info" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Info') ?></a></li>
                            <li role="presentation"><a href="#chapters" aria-controls="home" role="tab" data-toggle="tab"><?php echo __('Chapters') ?></a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="info">
                                <?php echo $this->element('admins/default/form-input', array('input' => $arrInput)) ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="chapters">
                                <?php echo $this->element('admins/default/course-chapters', array()) ?>
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
    
</script>