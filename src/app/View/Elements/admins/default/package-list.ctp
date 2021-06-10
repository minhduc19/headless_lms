<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th width="20%">
                    <?php echo __('Icon'); ?>
                </th>
                <th>
                    <?php echo __('Package ID'); ?>
                </th>
                <th>
                    <?php echo __('Package Name'); ?>
                </th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        $items = json_decode($item['Game']['buy_packages'], true);
        if (is_array($items) && !empty($items)) {
            foreach ($items as $item) {
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $this->HtmlExtend->get_image_from_link($item['icon'], array('id' => 'icon-image-preview', 'alt' => 'icon image', 'width' => 80, 'height' => 58)); ?></td>
                    <td><?php echo $item['package_id'] ?></td>
                    <td><?php echo $item['package_name'] ?></td>
                    <!-- END CHANGE HERE -->
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>