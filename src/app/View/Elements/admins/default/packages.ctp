<table class="tblForm" style="width: 80%">
    <?php
    if (!empty($input)) {
        $count = count($input);
        foreach ($input as $key => $value) {
            ?>
                <tr>
                    <td width="20%">
                        <?php echo $this->Form->input("Package.icon.", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'required' => false));?>
                    </td>
                    <td style="width: 10px;"></td>
                    <td width="30%">
                        <?php echo $this->Form->input("Package.package_id.", array('type' => 'text', 'value' => $value['package_id'], 'placeholder' => 'package_id', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
                    </td>
                    <td style="width: 10px;"></td>
                    <td width="30%">
                        <?php echo $this->Form->input("Package.package_name.", array('type' => 'text', 'value' => $value['package_name'], 'placeholder' => 'package_name', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
                    </td>
                    <td width="10%">
                    </td>
                    <td>
                        <a href="javascript:void(0)" class="add-row-image-package btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
                        <a href="javascript:void(0)" class="remove-row-image-package btn-custom btn purple" title="<?php echo __('Remove'); ?>" style="<?php
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
                <?php echo $this->Form->input("Package.icon.", array('type' => 'file', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
            </td>
            <td style="width: 10px;"></td>
            <td width="30%">
                <?php echo $this->Form->input("Package.package_id.", array('type' => 'text', 'placeholder' => 'package_id', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
            </td>
            <td style="width: 10px;"></td>
            <td width="30%">
                <?php echo $this->Form->input("Package.package_name.", array('type' => 'text', 'placeholder' => 'package_name', 'class' => 'form-control input-sm', 'div' => false, 'label' => false, 'data-validation' => 'required'));?>
            </td>
            <td width="10%"></td>
            <td>
                <a href="javascript:void(0)" class="add-row-image-package btn-custom green btn" title="<?php echo __('Add'); ?>"><i class="fa fa-plus"></i></a>
                <a href="javascript:void(0)" class="remove-row-image-package btn-custom btn purple" title="<?php echo __('Remove'); ?>" style="display: none;"><i class="fa fa-times"></i></a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
<script type="text/javascript">
    $(document).ready(function() {
        $('.add-row-image-package').click(function() {
            var element = $(this).parent().parent();
            $('.remove-row-image-package').css('display', 'block');
            $.ajax({
                type: "POST",
                url: "<?php echo $this->Html->url(array('controller' => 'games', 'action' => 'admin_ajax_add_package')); ?>",
                dataType: 'Html',
                success: function(data) {
                    element.after(data);
                }
            });
        });
        $('.remove-row-image-package').click(function() {
            $(this).parent().parent().remove();
            var del = $('.remove-row-image-package');
            if (del.length == 1) {
                $('.remove-row-image-package').css('display', 'none');
            }
        });
    });
   
</script>