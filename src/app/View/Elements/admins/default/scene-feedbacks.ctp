<?php
$types = ['right' => 'Right', 'wrong' => 'Wrong', 'other' => 'Other'];
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
                        <?php echo $this->Form->input("Feedback.type.$id", array('options' => $types, 'value' => $value['type'], 'empty' => '-- Select --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
                        <?php echo $this->Form->input("Feedback.answer.$id", array('type' => 'text', 'value' => $value['answer'], 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'Answer', 'data-validation' => 'required'));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
                        <?php echo $this->Form->input("Feedback.feedback.$id", array('type' => 'text', 'value' => $value['feedback'], 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'Feedback', 'data-validation' => 'required'));?>
                    </td>
                    <td width="1%"></td>
                    <td width="20%">
                        <a href="javascript:void(0)" class="add-row-image-feedback btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
                        <a href="javascript:void(0)" class="remove-row-image-feedback btn-custom btn purple" title="<?php echo __('Remove'); ?>" style="<?php
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
                <?php echo $this->Form->input("Feedback.type.", array('options' => $types, 'empty' => '-- Select answer type --', 'class' => 'form-control input-sm select2', 'div' => false, 'label' => false));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Feedback.answer.", array('type' => 'text', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'Answer'));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <?php echo $this->Form->input("Feedback.feedback.", array('type' => 'text', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'placeholder' => 'Feedback'));?>
            </td>
            <td width="1%"></td>
            <td width="20%">
                <a href="javascript:void(0)" class="add-row-image-feedback btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
                <a href="javascript:void(0)" class="remove-row-image-feedback btn-custom btn purple" title="<?php echo __('Remove'); ?>" style="display: none;"><i class="fa fa-times"></i></a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<script type="text/javascript">
    $(document).ready(function() {
        $('.add-row-image-feedback').click(function() {
            var element = $(this).parent().parent();
            $('.remove-row-image-feedback').css('display', 'block');
            $.ajax({
                type: "POST",
                url: "<?php 
                if($auth_user['type'] == 'teacher'){
                    echo $this->Html->url(array('controller' => 'scenes', 'action' => 'teacher_ajax_add_feedback')); 
                } else {
                    echo $this->Html->url(array('controller' => 'scenes', 'action' => 'admin_ajax_add_feedback')); 
                }
                ?>",
                dataType: 'Html',
                success: function(data) {
                    element.after(data);
                }
            });
        });
        $('.remove-row-image-feedback').click(function() {
            $(this).parent().parent().remove();
            var del = $('.remove-row-image-feedback');
            if (del.length == 1) {
                $('.remove-row-image-feedback').css('display', 'none');
            }
        });
    });
   
</script>