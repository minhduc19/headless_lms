<?php
$controller = 'users';
$search_form_action = 'index';
$partial_filter_form_name = 'filter';
$model_name = 'User';
$table_action_form = 'table-action'; //just dummy form but need
$btn_detail_action = 'admin_detail';
$btn_edit_action = 'admin_edit';
$btn_delete_checked_action = 'admin_delete_checked';
?>
<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th width="5%">
                    <?php echo __('ID') ?>
                </th>
                <th>
                    <?php echo __("Avatar"); ?>
                </th>
                <th>
                    <?php echo __('Type'); ?>
                </th>
                <th>
                    <?php echo __('Username'); ?>
                </th>
                <th>
                    <?php echo __('Email'); ?>
                </th>
                <th>
                    <?php echo __('Phone number'); ?>
                </th>
                <th>
                    <?php echo __('Status'); ?>
                </th>

                <th>
                    <?php echo __('Gcoi'); ?>
                </th>
                <th style="width:20%"><?php echo __('Action') ?></th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($input)) {
            foreach ($input as $item) {
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $item[$model_name]['id'] ?></td>
                    <td><?php echo $this->HtmlExtend->get_image_from_link($item[$model_name]['avatar'], array('alt' => $item[$model_name]['username'], 'width' => 84, 'height' => 58)); ?></td>
                    <td><?php echo $item[$model_name]['type'] ?></td>
                    <td><?php echo $item[$model_name]['username'] ?></td>
                    <td><?php echo $item[$model_name]['email'] ?></td>
                    <td><?php echo $item[$model_name]['phone_number'] ?></td>
                    <td><?php echo $item[$model_name]['status'] ?></td>
                    <td align="right"><?php echo $item[$model_name]['gcoin'] ?></td>
                    <td class="action-control">
                        <?php
                        echo $this->Html->link('<i class="fa fa-eye"></i>' . __('&nbspDetail'), array('action' => $btn_detail_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs blue', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Detail')));
                        echo $this->Html->link('<i class="fa fa-edit"></i>' . __('&nbspEdit'), array('action' => $btn_edit_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs red', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Edit')));
                        //echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('&nbspDelete'), array('action' => 'admin_delete', $item[$model_name]['id']), array('class' => 'btn default btn-xs btn-calling-ajax', 'data-confirm' => __('Are you sure?'), 'escape' => false, 'data-container' => "#{$arrParam['action']}-ajax-message"));
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