<?php echo $this->element('admins/default/header-body'); ?>
<?php
$search_form_action = 'index';
$partial_filter_form_name = 'filter';
$model_name = 'Scene';
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
			<?php echo $this->element('admins/search/search_scene'); ?>
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
                                            <!-- CHANGE HERE -->
                                            <th style="width:2%">
                                                <?php echo $this->Filter->sort_link(__('ID'), "$model_name.id") ?>
                                            </th>
                                            <th style="width:10%">
                                                <?php echo __("Thumb"); ?>
                                            </th>
                                            <th style="width:15%">
                                                <?php echo $this->Filter->sort_link(__('Unit'), "$model_name.unit_id") ?>
                                            </th>
                                            <th style="width:20%">
                                                <?php echo $this->Filter->sort_link(__('Title'), "$model_name.title") ?>
                                            </th>
                                            <th style="width:23%">
                                                <?php echo $this->Filter->sort_link(__('Content'), "$model_name.content") ?>
                                            </th>
                                            <th style="width:5%">
                                                <?php echo $this->Filter->sort_link(__('Sort order'), "$model_name.sort_order") ?>
                                            </th>
                                            
                                            <th style="width:25%"><?php echo __('Action') ?></th>
                                            <!-- END CHANGE HERE -->
                                        </tr>
                                    </thead>
                                    <?php
                                    if (!empty($items)) {
                                        foreach ($items as $item) {
                                            ?>
                                            <tr role="row" class="filter">
                                                </td>
                                                <!-- CHANGE HERE -->
                                                <td><?php echo $item[$model_name]['id'] ?></td>
                                                <td><?php echo $this->HtmlExtend->get_image_from_link($item[$model_name]['thumb'], array('alt' => $item[$model_name]['title'], 'height' => 80)); ?></td>
                                                <td>
                                                    <?php echo !empty($item['Unit']) ? $item['Unit']['title'] : ''; ?>
                                                </td>
                                                <td><?php echo $item[$model_name]['title'] ?></td>
                                                <td class="short-content"><?php echo $item[$model_name]['content']; ?></td>
                                                <td><?php 
                                                //echo $item[$model_name]['sort_order'] 
                                                  echo $this->Form->input("$model_name.sort_order.{$item[$model_name]['id']}", array('type' => 'number', 'value' => $item[$model_name]['sort_order'], 'class' => 'form-control input-small-index sort-order', 'data-sceneid' => $item[$model_name]['id'], 'label' => false, 'div' => false, 'min' => 0));      
                                                ?></td>
                                                <td class="action-control">
                                                    <?php
                                                    echo $this->Html->link('<i class="fa fa-eye"></i>' . __('&nbspDetail'), array('action' => $btn_detail_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs blue', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Detail')));
                                                    echo $this->Html->link('<i class="fa fa-edit"></i>' . __('&nbspEdit'), array('action' => $btn_edit_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs red', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Edit')));
                                                    echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('&nbspDelete'), array('action' => 'admin_delete', $item[$model_name]['id']), array('class' => 'btn default btn-xs btn-calling-ajax', 'data-confirm' => __('Are you sure?'), 'escape' => false, 'data-container' => "#{$arrParam['action']}-ajax-message"));
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
                url: "<?php echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_get_lessons')); ?>/" + course_id,
                dataType: 'Html',
                success: function(data) {
                    $('#selectLesson').empty().trigger("change");
                    var json = JSON.parse(data);
                    $.each(json, function(index, value){
                        var newOption = new Option(value, index, false, false);
                        $('#selectLesson').append(newOption).trigger('change');
                    });
                }
            });
        });
        
        $("#selectLesson").change(function(){
            var lesson_id = $(this).val();
            $.ajax({
                type: "GET",
                url: "<?php echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_get_units')); ?>/" + lesson_id,
                dataType: 'Html',
                success: function(data) {
                    $('#selectUnit').empty().trigger("change");
                    var json = JSON.parse(data);
                    $.each(json, function(index, value){
                        var newOption = new Option(value, index, false, false);
                        $('#selectUnit').append(newOption).trigger('change');
                    });
                }
            });
        });
        
        $(".sort-order").change(function(){
            var order = $(this).val();
            var scene_id = $(this).data('sceneid');
            $.ajax({
                type: "POST",
                url: "<?php echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_update_order')); ?>",
                data: {
                    sort_order: order,
                    id: scene_id
                },
                success: function(data) {
                    console.log(data);
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