<?php 
$id = uniqid();
?>
<tr>
    <td width="10%">
        <?php echo $this->Form->input("TeacherSkill.name.$id", array('type' => 'text', 'placeholder' => 'Skill name', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => true));?>
    </td>
    <td width="1%"></td>
    <td width="8%">
        <?php echo $this->Form->input("TeacherSkill.sort_order.$id", array('type' => 'number', 'placeholder' => 'Sort order', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'value' => 0));?>
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
            url: "<?php echo $this->Html->url(array('controller' => 'teachers', 'action' => 'admin_ajax_add_skill')); ?>",
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