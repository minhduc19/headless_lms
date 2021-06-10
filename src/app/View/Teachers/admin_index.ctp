<?php echo $this->element('admins/default/header-body'); ?>
<?php
$search_form_action = 'index';
$partial_filter_form_name = 'filter';
$model_name = 'Teacher';
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
                        <div class="row">
                            <div class="col-sm-12 col-md-5 margin-bottom-10">
                                <?php echo $this->element('admins/paginator/paginator1', array('id' => 'paginator')); ?>
                            </div>
                            <div class="col-sm-12 col-md-7 search-box text-right">
                                <?php echo $this->Form->create(false, array('name' => 'search-by-name', 'type' => 'get', 'class' => 'search-form', 'url' => array('action' => $search_form_action))) ?>
                                <!-- CHANGE HERE -->
                                <?php echo $this->Form->input('name', array('type' => 'text', 'value' => isset($this->request->query['name']) ? $this->request->query('name') : '', 'placeholder' => array('text' => __('name')), 'div' => false, 'label' => false, 'class' => 'form-control input-inline input-sm')) ?>

                                <!-- CHANGE HERE -->
                                <?php echo $this->Form->button('<i class="fa fa-search"></i>   ' . __('Search'), array('div' => false, 'label' => FALSE, 'escape' => false, 'type' => 'submit', 'class' => 'btn yellow btn-sm')); ?>
                                <?php echo $this->Form->end() ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($items)): ?>
                        <div>
                            <?php echo $this->Form->create(null, array('name' => $table_action_form, 'class' => 'search-form', 'type' => 'POST', 'style' => 'width: 100%')); ?>
                            <div class="table-scrollable">
                                <table class="table table-striped table-bordered table-hover" id="datatable_products">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <!-- CHANGE HERE -->
                                            <th width="5%">
                                                <?php echo $this->Filter->sort_link(__('ID'), "$model_name.id") ?>
                                            </th>
                                            <th>
                                                <?php echo __("Avatar"); ?>
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Name'), "$model_name.name") ?>
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Email'), "$model_name.email") ?>
                                            </th>
                                            <th>
                                                Skills
                                            </th>
                                            <th>
                                                <?php echo $this->Filter->sort_link(__('Status'), "$model_name.status") ?>
                                            </th>
                                            <th style="width:20%"><?php echo __('Action') ?></th>
                                            <!-- END CHANGE HERE -->
                                        </tr>
                                    </thead>
                                    <?php
                                    if (!empty($items)) {
                                        foreach ($items as $item) {
                                            $arr_skills = [];
                                            if(!empty($item['TeacherSkill'])){
                                                foreach ($item['TeacherSkill'] as $skill) {
                                                    $arr_skills[] = '#' . $skill['name'];
                                                }
                                            }

                                            ?>
                                            <tr role="row" class="filter">
                                                <!-- CHANGE HERE -->
                                                <td><?php echo $item[$model_name]['id'] ?></td>
                                                <td><?php echo $this->HtmlExtend->get_image_from_link($item[$model_name]['avatar'], array('alt' => $item[$model_name]['name'], 'width' => 84, 'height' => 58)); ?></td>
                                                <td><?php echo $item[$model_name]['name'] ?></td>
                                                <td><?php echo $item[$model_name]['email'] ?></td>
                                                <td><?php echo implode("<br>", $arr_skills) ?></td>
                                                <td><?php echo $item[$model_name]['status'] ?></td>
                                                <td class="action-control">
                                                    <?php
                                                    //echo $this->Html->link('<i class="fa fa-eye"></i>' . __('&nbspDetail'), array('action' => $btn_detail_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs blue', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Detail')));
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
    });
</script>
<?php echo $this->element('admins/default/a_check_all_and_partial_filter', array('partial_filter_form_name' => $partial_filter_form_name, 'table_action_form' => $table_action_form)) ?>
<?php echo $this->end() ?>
<!--//load a_.plugin-->
<?php echo $this->Html->script('init_ui_controls') ?>
<?php echo $this->Html->script('init_image_fancybox') ?>