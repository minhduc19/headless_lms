<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th>
                    <?php echo __('ID'); ?>
                </th>
                <th>
                    <?php echo __('Title'); ?>
                </th>
                <th>
                    <?php echo __('Sort order'); ?>
                </th>
                <th style="width:30%"><?php echo __('Action') ?></th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($item['Chapter'])) {
            foreach ($item['Chapter'] as $chapter) {
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $chapter['id'] ?></td>
                    <td><?php echo $chapter['title'] ?></td>
                    <td><?php echo $chapter['sort_order'] ?></td>
                    <td class="action-control">
                        <?php
                        echo $this->Html->link('<i class="fa fa-leanpub"></i>' . __('&nbspLessons'), array('controller' => 'lessons', 'action' => 'index', '?' => ['chapter_id' => $chapter['id']]), array('class' => 'btn default btn-xs blue', 'div' => false, 'label' => false, 'escape' => false, 'title' => __('Lessons')));
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