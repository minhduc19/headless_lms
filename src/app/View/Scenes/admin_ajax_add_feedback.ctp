<?php 
$id = uniqid();
$types = ['right' => 'Right', 'wrong' => 'Wrong', 'other' => 'Other'];
?>
<tr>
    <td width="20%">
        <?php echo $this->Form->input("Feedback.type.$id", array('options' => $types, 'empty' => '-- Select --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Feedback.answer.$id", array('type' => 'text', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'Answer', 'data-validation' => 'required'));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Feedback.feedback.$id", array('type' => 'text', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'Feedback', 'data-validation' => 'required'));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <a href="javascript:void(0)" id="add<?=$id?>" class="add-row-image-feedback btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
        <a href="javascript:void(0)" class="remove-row-image-feedback btn-custom btn purple"  id="remove<?php echo $id; ?>" title="<?php echo __('Remove'); ?>"><i class="fa fa-times"></i></a>
    </td>
</tr>
<script>
    $('#add<?php echo $id; ?>').click(function() {
        var element = $(this).parent().parent();
        $('.remove-row-image-feedback').css('display', 'block');
        $.ajax({
            type: "POST",
            url: "<?php echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_add_feedback')); ?>",
            dataType: 'Html',
            success: function(data) {
                element.after(data);
            }
        });
    });
    $('#remove<?php echo $id; ?>').click(function() {
        console.log('clicked');
        $(this).parent().parent().remove();
        var del = $('.remove-row-image-feedback');
        if (del.length == 1) {
            $('.remove-row-image-feedback').css('display', 'none');
        }
    });
</script>