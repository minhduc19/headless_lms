<table class="tblForm" style="width: 80%">
    <?php
    if (!empty($input)) {
        $count = count($input);
        foreach ($input as $key => $value) {
            $id = $value['id'];
            ?>
                <tr>
                    <td width="60%">
                        <?php
                            echo $this->Form->hidden("Chapter.id.$id", ['value' => $id]);
                            echo $this->Form->input("Chapter.title.$id", array('type' => 'text', 'value' => $value['title'], 'class' => 'form-control input-sm', 'div' => false, 'label' => false));
                        ?>
                    </td>
                    <td width="5%">
                    <td width="15%">
                        <?php
                            echo $this->Form->input("Chapter.sort_order.$id", array('type' => 'number', 'value' => $value['sort_order'], 'class' => 'form-control input-small', 'div' => false, 'label' => false, 'min' => 0));
                        ?>
                    </td>
                    <td width="5%"></td>
                    <td>
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
            <td width="60%">
                <?php 
                echo $this->Form->input("Chapter.title.", array('type' => 'text', 'placeholder' => 'chapter name', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => false));
                ?>
                
            </td>
            <td width="5%"></td>
            <td width="15%">
                <?php 
                echo $this->Form->input("Chapter.sort_order.", array('type' => 'number', 'value' => 0, 'class' => 'form-control input-small', 'div' => false, 'label' => false, 'min' => 0));
                ?>
                
            </td>
            <td width="5%"></td>
            <td>
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
                url: "<?php echo $this->Html->url(array('controller' => 'courses', 'action' => 'admin_ajax_add_chapter')); ?>",
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