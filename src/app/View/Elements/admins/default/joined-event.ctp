<?php
$controller = 'events';
$search_form_action = 'index';
$partial_filter_form_name = 'filter';
$model_name = 'Event';
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
                    <?php __('ID') ?>
                </th>
                <th>
                    <?php echo __("Thumb image"); ?>
                </th>
                <th>
                    <?php echo __('Name') ?>
                </th>
                <th>
                    <?php echo __('Title') ?>
                </th>
                <th>
                    <?php echo __('Game') ?>
                </th>
                <th>
                    <?php echo __('Total joined') ?>
                </th>
                <th>
                    <?php echo __('Event from') ?>
                </th>
                <th>
                    <?php echo __('Event to') ?>
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
                    <td align="right"><?php echo $item[$model_name]['id'] ?></td>
                    <td><?php echo $this->HtmlExtend->get_image($item[$model_name]['thumb_url'], array('alt' => $item[$model_name]['title'], 'width' => 84, 'height' => 58)); ?></td>
                    <td><?php echo $item[$model_name]['name'] ?></td>
                    <td><?php echo $item[$model_name]['title'] ?></td>
                    <td><?php echo isset($item['Game']['name']) ? $item['Game']['name'] : ''; ?></td>
                    <td align="right"><?php echo $item[$model_name]['total_joined']; ?></td>
                    <td><?php echo $item[$model_name]['event_from'] ?></td>
                    <td><?php echo $item[$model_name]['event_to'] ?></td>
                    <td class="action-control">
                        <?php
                        echo $this->Html->link('<i class="fa fa-eye"></i>' . __('&nbspDetail'), array('controller' => $controller, 'action' => $btn_detail_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs blue', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Detail')));
                        echo $this->Html->link('<i class="fa fa-edit"></i>' . __('&nbspEdit'), array('controller' => $controller, 'action' => $btn_edit_action, $item[$model_name]['id']), array('class' => 'btn default btn-xs red', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Edit')));
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