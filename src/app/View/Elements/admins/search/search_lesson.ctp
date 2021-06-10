<?php
$keyword = $this->Form->input('keyword', array('type' => 'text', 'value' => isset($keyword) ? $keyword : '', 'placeholder' => array('text' => __('title')), 'div' => false, 'label' => false, 'class' => 'form-control input-sm'));
$tag = $this->Form->input('tag_id', array('options' => $tags, 'value' => isset($tag_id) ? $tag_id : '', 'empty' => '--Select--', 'div' => false, 'label' => false, 'class' => 'form-control input-sm select2'));
$course = $this->Form->input('course_id', array('options' => $courses, 'id' => 'selectCourse', 'value' => isset($course_id) ? $course_id : '', 'empty' => '--Select--', 'div' => false, 'label' => false, 'class' => 'form-control input-sm select2'));
$chapter = $this->Form->input('chapter_id', array('options' => $chapters, 'id' => 'selectChapter', 'value' => isset($chapter_id) ? $chapter_id : '', 'empty' => '--Select--', 'div' => false, 'label' => false, 'class' => 'form-control input-sm select2'));
$btn_search = $this->Form->button('<i class="fa fa-search"></i>   ' . __('Search'), array('type' => 'submit', 'div' => false, 'label' => FALSE, 'escape' => false, 'class' => 'btn btn-sm yellow filter-submit'));
$btn_reset = $this->Html->link('<i class="fa fa-times"></i>   ' . __('Reset'), array('action' => 'index', 'admin' => true), array('class' => 'btn btn-sm red filter-cancel', 'div' => false, 'label' => FALSE, 'escape' => FALSE));
?>
<div class="portlet box green">
    <div class="portlet-title search_" style="cursor:pointer">
        <div class="caption ">
            <span class="search-title">
                <i class="fa fa-search-plus"></i>
                <?php echo __('Search'); ?>
            </span>
        </div>
        <div class="tools">
            <a href="javascript:;" id="control_" class="<?php
            if (!$flag) {
                echo 'expand';
            } else {
                echo 'collapse';
            }
            ?>"></a>
        </div>
    </div>
    <div class="portlet-body form" id="search-body" style="<?php
    if (!$flag) {
        echo 'display:none;';
    } else {
        echo 'display:block;';
    }
    ?>">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                            <?php echo __('Title') ?>
                        </label>
                        <div class="col-md-9">
                            <?php echo $keyword; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                            <?php echo __('Tag') ?>
                        </label>
                        <div class="col-md-9">
                            <?php echo $tag; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                            <?php echo __('Course') ?>
                        </label>
                        <div class="col-md-9">
                            <?php echo $course; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-md-3">
                            <?php echo __('Chapter') ?>
                        </label>
                        <div class="col-md-9">
                            <?php echo $chapter; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <?php echo $btn_search . $btn_reset; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    $('#control_').click(function() {
        if ($(this).attr('class') == 'expand') {
            $('#control_').removeClass('expand');
            $('#control_').addClass('collapse');
        } else if ($(this).attr('class') == 'collapse') {
            $('#control_').removeClass('collapse');
            $('#control_').addClass('expand');
        }
    });
    $('.search_').click(function() {
        $('#search-body').slideToggle();
        var className = $('#control_').attr('class');
        if (className == 'expand') {
            $('#control_').removeClass('expand');
            $('#control_').addClass('collapse');
        } else if (className = 'collapse') {
            $('#control_').removeClass('collapse');
            $('#control_').addClass('expand');
        }
    });
    //Kiem soat tu ngay-toi ngay
    $('#from-date-input').change(function() {
        var from_date = $('#from-date-input').val();
        var to_date = $('#to-date-input').val();
        var from_date_cv = moment.utc(from_date, 'DD-MM-YYYY').format('MM/DD/YYYY');
        var to_date_cv = moment.utc(to_date, 'DD-MM-YYYY').format('MM/DD/YYYY');
        from = new Date(from_date_cv).getTime();
        to = new Date(to_date_cv).getTime();
        if (from > to) {
            $('#to-date-input').val(from_date);
            $('.date-picker').datepicker("update");
        }
    });
    $('#to-date-input').change(function() {
        var from_date = $('#from-date-input').val();
        var to_date = $('#to-date-input').val();
        var from_date_cv = moment.utc(from_date, 'DD-MM-YYYY').format('MM/DD/YYYY');
        var to_date_cv = moment.utc(to_date, 'DD-MM-YYYY').format('MM/DD/YYYY');
        from = new Date(from_date_cv).getTime();
        to = new Date(to_date_cv).getTime();
        if (from > to) {
            $('#from-date-input').val(to_date);
            $('.date-picker').datepicker("update");
        }
    });
</script>