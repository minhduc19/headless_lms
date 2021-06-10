<?php
?>
<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th>
                    <?php echo __('STT'); ?>
                </th>
                <th>
                    <?php echo __('Type'); ?>
                </th>
                <th>
                    <?php echo __('Answer'); ?>
                </th>
                <th>
                    <?php echo __('Feedback'); ?>
                </th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($item['Feedback'])) {
            foreach ($item['Feedback'] as $key => $media) {
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $media['type'] ?></td>
                    <td><?php echo $media['answer'] ?></td>
                    <td><?php echo $media['feedback'] ?></td>
                    <!-- END CHANGE HERE -->
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>