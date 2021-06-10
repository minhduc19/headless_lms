<?php 
$id = uniqid();
?>
<tr>
    <td width="9%">
        <?php echo $this->Form->input("Unit.type.$id", array('options' => ['html' => 'HTML', 'native' => 'Native'], 'empty' => '-- Type --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'required' => true));?>
    </td>
    <td width="1%"></td>
    <td width="10%">
        <?php echo $this->Form->input("Unit.title.$id", array('type' => 'text', 'placeholder' => 'Unit title', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => true));?>
    </td>
    <td width="1%"></td>
    <td width="8%">
        <?php echo $this->Form->input("Unit.sort_order.$id", array('type' => 'number', 'placeholder' => 'Sort order', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'value' => 0));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Unit.short_description.$id", array('type' => 'text', 'placeholder' => 'Description', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => false));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Unit.thumb.$id", array('type' => 'file', 'placeholder' => 'Unit title', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'thumb', 'required' => false, 'accept' => 'image/*'));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Unit.html_file.$id", array('type' => 'file', 'placeholder' => 'Unit title', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'zip', 'required' => false, 'accept' => '.zip'));?>
    </td>
    <td width="1%"></td>
    <td width="16%">
        <a href="javascript:void(0)" id="add<?=$id?>" class="add-row-image btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
        <a href="javascript:void(0)" class="remove-row-image btn-custom btn purple"  id="remove<?php echo $id; ?>" title="<?php echo __('Remove'); ?>"><i class="fa fa-times"></i></a>
    </td>
</tr>
<script>
    $('#add<?php echo $id; ?>').click(function() {
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
    $('#remove<?php echo $id; ?>').click(function() {
        console.log('clicked');
        $(this).parent().parent().remove();
        var del = $('.remove-row-image');
        if (del.length == 1) {
            $('.remove-row-image').css('display', 'none');
        }
    });
</script>