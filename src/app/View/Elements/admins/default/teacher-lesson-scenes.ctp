<div class="pull-right">
    <?php echo $this->Html->link("<i class='fa  fa-plus-circle'></i>" . '  Add Scene', array('controller' => 'scenes', 'action' => 'teacher_add', '?' => ['lesson_id' => $item['TeacherLesson']['id']]), array('div' => false, 'label' => false, 'class' => 'btn btn-sm green margin-bottom-5 pull', 'escape' => false)); ?>
</div>
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
                <th style="width: 30%;">
                    <?php echo __('Action'); ?>
                </th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($item['TeacherScene'])) {
            foreach ($item['TeacherScene'] as $scene) {
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $scene['id'] ?></td>
                    <td><?php echo $this->HtmlExtend->get_image_from_link($scene['thumb'], array('alt' => $scene['title'], 'width' => 84)); ?></td>
                    <td><?php echo $scene['type'] ?></td>
                    <td><?php echo $scene['sort_order'] ?></td>
                    <td><?php echo $scene['title'] ?></td>
                    <td>
                        <a href="/teacher/scenes/edit/<?= $scene['id']?>" target="_blank" class="btn default btn-xs blue" title="View"><i class="fa fa-eye"></i>&nbsp;Edit</a>
                        <?php
                        echo $this->Html->link('<i class="fa fa-trash-o"></i>' . __('&nbspDelete'), array('controller' => 'scenes', 'action' => 'teacher_delete', $scene['id'], '?' => ['redirect' => "/teacher/lessons/detail/{$item['TeacherLesson']['id']}?active=scenes"]), array('class' => 'btn default btn-xs btn-calling-ajax', 'data-confirm' => __('Are you sure?'), 'escape' => false, 'data-container' => "#{$arrParam['action']}-ajax-message"));
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
<?php echo $this->append('script') ?>
<?php echo $this->HtmlExtend->script('plugins/a_plugins') ?>
<?php echo $this->end() ?>
