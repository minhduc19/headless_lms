<table class="tblForm" style="width: 100%">
    <?php
    if (!empty($input)) {
        $count = count($input);
        foreach ($input as $key => $value) {
            $id = $value['id'];
            ?>
                <tr>
                    <td width="9%">
                        <?php echo $this->Form->input("Unit.type.$id", array('options' => ['html' => 'HTML', 'native' => 'Native'], 'empty' => '-- Type --', 'value' => $value['type'], 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'required' => true));?>
                    </td>
                    <td width="1%"></td>
                    <td width="10%">
                        <?php echo $this->Form->input("Unit.title.$id", array('type' => 'text', 'value' => $value['title'], 'placeholder' => 'Unit title', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => true));?>
                    </td>
                    <td width="1%"></td>
                    <td width="8%">
                        <?php echo $this->Form->input("Unit.sort_order.$id", array('type' => 'number', 'value' => $value['sort_order'], 'placeholder' => 'Sort order', 'class' => 'form-control input-sm', 'div' => false, 'label' => false));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
                        <?php echo $this->Form->input("Unit.short_description.$id", array('type' => 'text', 'value' => $value['short_description'], 'placeholder' => 'Description', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => false));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
                        <?php echo $this->Form->input("Unit.thumb.$id", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'thumb', 'accept' => 'image/*'));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
                        <?php echo $this->Form->input("Unit.html_file.$id", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'zip', 'accept' => '.zip'));?>
                    </td>
                    <td width="1%"></td>
                    <td width="16%">
                        <a href="javascript:void(0)" class="add-row-image btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
                        <a href="javascript:void(0)" class="remove-row-image btn-custom btn purple" title="<?php echo __('Remove'); ?>" style="<?php
                        if ($count < 2) {
                            echo 'display: none;';
                        }
                        ?>"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td width="9%">
                <?php echo $this->Form->input("Unit.type.", array('options' => ['html' => 'HTML', 'native' => 'Native'], 'empty' => '-- Type --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'required' => true));?>
            </td>
            <td width="1%"></td>
            <td width="10%">
                <?php echo $this->Form->input("Unit.title.", array('type' => 'text', 'placeholder' => 'Unit title', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => true));?>
            </td>
            <td width="1%"></td>
            <td width="8%">
                <?php echo $this->Form->input("Unit.sort_order.", array('type' => 'number', 'value' => 0, 'placeholder' => 'Sort order', 'class' => 'form-control input-sm', 'div' => false, 'label' => false));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Unit.short_description.", array('type' => 'text', 'placeholder' => 'Description', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => false));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Unit.thumb.", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'thumb', 'required' => false, 'accept' => 'image/*'));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Unit.html_file.", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'zip', 'required' => false, 'accept' => '.zip'));?>
            </td>
            <td width="1%"></td>
            <td width="16%">
                <a href="javascript:void(0)" class="add-row-image btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
                <a href="javascript:void(0)" class="remove-row-image btn-custom btn purple" title="<?php echo __('Remove'); ?>" style="display: none;"><i class="fa fa-times"></i></a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<script type="text/javascript">
    $(document).ready(function() {
        $('.add-row-image').click(function() {
            var element = $(this).parent().parent();
            $('.remove-row-image').css('display', 'block');
            $.ajax({
                type: "POST",
                url: "<?php echo $this->Html->url(array('controller' => 'lessons', 'action' => 'admin_ajax_add_unit')); ?>",
                dataType: 'Html',
                success: function(data) {
                    element.after(data);
                }
            });
        });
        $('.remove-row-image').click(function() {
            $(this).parent().parent().remove();
            var del = $('.remove-row-image');
            if (del.length == 1) {
                $('.remove-row-image').css('display', 'none');
            }
        });
    });
   
</script>