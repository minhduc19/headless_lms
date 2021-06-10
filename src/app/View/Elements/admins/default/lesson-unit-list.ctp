<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th>
                    <?php echo __('ID'); ?>
                </th>
                <th>
                    <?php echo __('Thumb'); ?>
                </th>
                <th>
                    <?php echo __('Type'); ?>
                </th>
                <th>
                    <?php echo __('Sort order'); ?>
                </th>
                <th>
                    <?php echo __('Title'); ?>
                </th>
                <th>
                    <?php echo __('Description'); ?>
                </th>
                <th style="width: 30%;">
                    <?php echo __('Action'); ?>
                </th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($item['Unit'])) {
            foreach ($item['Unit'] as $unit) {
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $unit['id'] ?></td>
                    <td><?php echo $this->HtmlExtend->get_image_from_link($unit['thumb'], array('alt' => $unit['title'], 'width' => 84)); ?></td>
                    <td><?php echo $unit['type'] ?></td>
                    <td><?php echo $unit['sort_order'] ?></td>
                    <td><?php echo $unit['title'] ?></td>
                    <td><?php echo $unit['short_description'] ?></td>
                    <td>
                        <a href="<?= $unit['url']?>" target="_blank" class="btn default btn-xs blue" title="View"><i class="fa fa-eye"></i>&nbsp;View</a>
                        <a href="<?= $unit['zip_url']?>" target="_blank" class="btn default btn-xs green" title="Download"><i class="fa fa-download"></i>&nbsp;Download</a>
                        <?php echo $this->Html->link('<i class="fa fa-list-ul"></i>' . __('&nbspScenes'), array('controller' => 'scenes', 'action' => 'index', '?' => ['unit_id' => $unit['id']]), array('class' => 'btn default btn-xs blue', 'target' => '_blank', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Detail')));?>
                    </td>
                    <!-- END CHANGE HERE -->
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>