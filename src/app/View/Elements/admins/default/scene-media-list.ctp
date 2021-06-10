<?php
?>
<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th>
                    <?php echo __('ID'); ?>
                </th>
                <th>
                    <?php echo __('Sort order'); ?>
                </th>
                <th>
                    <?php echo __('Type'); ?>
                </th>
                <th>
                    <?php echo __('Cover'); ?>
                </th>
                <th>
                    <?php echo __('Content'); ?>
                </th>
                <th>
                    <?php echo __('Action'); ?>
                </th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($item['Media'])) {
            foreach ($item['Media'] as $media) {
                $content = '';
                switch ($media['type']){
                    case 'image':
                        $content = "<img src='{$media['url']}' width='100px'/>";
                        break;
                    case 'video':
                        $content = "<video controls width='300px'><source src='{$media['url']}' /></video>";
                        break;
                    case 'audio':
                        $content = "<audio controls width='300px'><source src='{$media['url']}'></audio>";
                        //$content = $media['url'];
                        break;
                    default:
                        break;
                }
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $media['id'] ?></td>
                    <td><?php echo $media['sort_order'] ?></td>
                    <td><?php echo $media['type'] ?></td>
                    <td><?php echo $this->HtmlExtend->get_image_from_link($media['cover'], array('alt' => '', 'width' => 100)); ?></td>
                    <td><?php echo $content; ?></td>
                    <td>
                        <a href="<?= $media['url']?>" target="_blank" class="btn default btn-xs blue" title="View"><i class="fa fa-eye"></i>&nbsp;View</a>
                    </td>
                    <!-- END CHANGE HERE -->
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>