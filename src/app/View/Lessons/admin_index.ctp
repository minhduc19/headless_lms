<?php echo $this->element('admins/default/header-body'); ?>
<?php
$search_form_action = 'index';
$partial_filter_form_name = 'filter';
$model_name = 'Lesson';
$table_action_form = 'table-action'; //just dummy form but need
$btn_detail_action = 'admin_detail';
$btn_edit_action = 'admin_edit';
$btn_delete_checked_action = 'admin_delete_checked';
?>

<div class="row" id='<?php echo $arrParam['action'] ?>'>
    <div class="col-md-12" id='<?php echo $arrParam['action'] ?>-ajax-message'>
    </div>
    <div class="col-md-12">
        <?php echo $this->Session->flash() ?>
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption pull-left">
                    <i class="fa fa-newspaper-o"></i>
                    <?php echo $small_title ?>
                </div>
                <div class="action btn-set pull-right margin-top-5">
                    
                    <div class="pull-right">
                        <?php echo $this->Html->link("<i class='fa  fa-plus-circle'></i>" . '  Add', array('action' => 'add'), array('div' => false, 'label' => false, 'class' => 'btn btn-sm green margin-bottom-5 pull', 'escape' => false)); ?>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="datatable_products_wrapper" class="dataTables_wrapper dataTables_extended_wrapper no-footer">
                        <?php echo $this->Form->create(FALSE, array('url' => ['action' => 'admin_index'], 'inputDefaults' => array('label' => false), 'novalidate' => true, 'class' => 'form-horizontal form-row-seperated', 'type' => 'GET')); ?>
			<?php echo $this->element('admins/search/search_lesson'); ?>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
				<?php echo $this->element('admins/paginator/paginator_header'); ?>
                            </div>
                            <div class="col-sm-12 col-md-7">

                            </div>
                        </div>
			<?php echo $this->Form->end(); ?>
                    </div>
                    <?php if (!empty($items)): ?>
                        <div>
                            <?php echo $this->Form->create(null, array('name' => $table_action_form, 'class' => 'search-form', 'type' => 'POST', 'style' => 'width: 100%')); ?>
                            <div class="table-scrollable">
                                <table class="table table-striped table-bordered table-hover" id="datatable_products">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th width="1%">
                                                <input type="checkbox" class="group-checkable" id='<?php echo $arrParam['action'] ?>-delete-check'>
                                            </th>
                                            <!-- CHANGE HERE -->
                                            <th width="5%">
                                                <?php echo $this->Filter->sort_link(__('ID'), "$model_name.id") ?>
                                            </th>
                                            <th>
                                                <?php echo __("Thumb"); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Title'), "$model_name.title") ?>
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Course'), "$model_name.course_id") ?>
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Sort order'), "$model_name.sort_order") ?>
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Short description'), "$model_name.short_description") ?>
                                            </th>
                                            <th style="width:30%"><?php echo __('Action') ?></th>
                                            <!-- END CHANGE HERE -->
                                        </tr>
                                    </thead>
                                    <?php
                                    if (!empty($items)) {
                                        foreach ($items as $item) {
                                            ?>
                                            <tr role="row" class="filter">
                                                <td><input type="checkbox" class="check  <?php echo $arrParam['action'] ?>-delete-check" name='ids[<?php echo $item[$model_name]['id'] ?>]'>
                                                </td>
                                                <!-- CHANGE HERE -->
                                                <td><?php echo $item[$model_name]['id'] ?></td>
                                                <td><?php echo $this->HtmlExtend->get_image_from_link($item[$model_name]['thumb'], array('alt' => $item[$model_name]['title'], 'width' => 84)); ?></td>
                                                <td><?php echo $item[$model_name]['title'] ?></td>
                                                <td>
                                                    <?php echo !empty($item['Course']) ? $item['Course']['title'] : ''; ?>
                                                    <?php echo !empty($item['Chapter']) ? ('<br><i>(' . $item['Chapter']['title'] . ')</i>') : ''; ?>
                                                </td>
                                                <td><?php echo $item[$model_name]['sort_order'] ?></td>
                                                <td><?php echo $item[$model_name]['short_description']; ?></td>
                                                <td class="action-control">
                                                    <?php
                                                    echo $this->Html->link('<i class="fa fa-eye"></i>' . __('&nbspDetail'), array('action' => $btn_detail_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs blue', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Detail')));
                                                    echo $this->Html->link('<i class="fa fa-edit"></i>' . __('&nbspEdit'), array('action' => $btn_edit_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs red', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Edit')));
                                                    echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('&nbspDelete'), array('action' => 'admin_delete', $item[$model_name]['id']), array('class' => 'btn default btn-xs btn-calling-ajax', 'data-confirm' => __('All related units will be deleted! Are you sure want to continue?'), 'escape' => false, 'data-container' => "#{$arrParam['action']}-ajax-message"));
                                                    ?>
                                                </td>
                                                <!-- END CHANGE HERE -->
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                            <?php echo $this->Form->end() ?>
                        </div>
                    <?php else: ?>
                        <div class='note note-info'>
                            <?php echo __('Oh. There is no data') ?>
                        </div>
                    <?php endif; ?>

                    <?php echo $this->element('admins/paginator/paginator1', array('id' => 'paginator-bottom', 'is_bottom' => true)); ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php echo $this->append('css') ?>
<?php echo $this->HtmlExtend->css('admin/a_search_form_custom_pagination') ?>
<style>
    #action-bar .btn{
        width: 100%;
    }
</style>
<?php echo $this->end() ?>

<?php echo $this->append('script') ?>
<?php echo $this->HtmlExtend->script('plugins/a_plugins') ?>
<script>
    $(function() {
        //data-form, data-action + (link | button) -> remote submit form
        $('#delete-seen').asSubmitButton();
        
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
    });
</script>
<?php echo $this->element('admins/default/a_check_all_and_partial_filter', array('partial_filter_form_name' => $partial_filter_form_name, 'table_action_form' => $table_action_form)) ?>
<?php echo $this->end() ?>
<!--//load a_.plugin-->
<?php echo $this->Html->script('init_ui_controls') ?>
<?php echo $this->Html->script('init_image_fancybox') ?>