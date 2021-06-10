<?php 
$id = uniqid();
?>
<tr>
    <td width="60%">
        <?php echo $this->Form->input("Chapter.title.", array('type' => 'text', 'placeholder' => 'chapter name', 'class' => 'form-control input-sm', 'div' => false, 'label' => false));?>
    </td>
    <td width="5%"></td>
    <td width="15%">
        <?php echo $this->Form->input("Chapter.sort_order.", array('type' => 'number', 'placeholder' => 'short order', 'class' => 'form-control input-small', 'div' => false, 'label' => false, 'min' => 0, 'value' => 0));?>
    </td>
    <td width="5%"></td>
    <td>
        <a href="javascript:void(0)" class="add-row-image btn-custom btn green" id="add<?php echo $id; ?>" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
        <a href="javascript:void(0)" class="remove-row-image btn-custom btn purple"  id="remove<?php echo $id; ?>" title="<?php echo __('Remove'); ?>"><i class="fa fa-times"></i></a>
    </td>
</tr>
<script>
    $('#add<?php echo $id; ?>').click(function() {
        var element = $(this).parent().parent();
        $('.remove-row-image').css('display', 'block');
        $.ajax({
            type: "POST",
            url: "<?php echo $this->Html->url(array('controller' => 'courses', 'action' => 'admin_ajax_add_chapter')); ?>",
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