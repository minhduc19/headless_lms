<?php
$media_types = ['image' => 'Image', 'video' => 'Video', 'audio' => 'Audio'];
?>
<table class="tblForm" style="width: 100%">
    <?php
    if (!empty($input)) {
        $count = count($input);
        foreach ($input as $key => $value) {
            $id = $value['id'];
            ?>
                <tr>
                    <td width="20%">
                        <?php echo $this->Form->input("Media.type.$id", array('options' => $media_types, 'value' => $value['type'], 'empty' => '-- Select --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'required' => false));?>
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
                        <?php echo $this->Form->input("Media.sort_order.$id", array('type' => 'number', 'value' => $value['sort_order'], 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'sort order'));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
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
            <td width="20%">
                <?php echo $this->Form->input("Media.type.", array('options' => $media_types, 'empty' => '-- Select --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'required' => false));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Media.content.", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => false));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Media.cover.", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => 'cover'));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Media.sort_order.", array('type' => 'number', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'sort order', 'value' => 0));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
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
                url: "<?php 
                    if($auth_user['type'] == 'teacher'){
                        echo $this->Html->url(array('controller' => 'scenes', 'action' => 'teacher_ajax_add_media')); 
                    } else {
                        echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_add_media')); 
                    }
                ?>",
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