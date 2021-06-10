<?php 
$id = uniqid();
$media_types = ['image' => 'Image', 'video' => 'Video', 'audio' => 'Audio'];
?>
<tr>
    <td width="20%">
        <?php echo $this->Form->input("Media.type.$id", array('options' => $media_types, 'empty' => '-- Select --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'required' => false));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Media.content.$id", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => false));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Media.cover.$id", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'cover'));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
        <?php echo $this->Form->input("Media.sort_order.$id", array('type' => 'number', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'sort order', 'value' => 0));?>
    </td>
    <td width="1%"></td>
    <td width="20%">
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
            url: "<?php echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_add_media')); ?>",
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