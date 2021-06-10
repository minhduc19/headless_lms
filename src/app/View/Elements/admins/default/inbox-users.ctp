<?php
$model_name = 'User';
$table_action_form = 'table-action';
$partial_filter_form_name = 'filter';
?>
<table class="table table-striped table-bordered table-hover" id="datatable_products">
    <thead>
        <tr role="row" class="heading">
            <th width="1%">
                <input type="checkbox" class="group-checkable" id='check-all'>
            </th>
            <th width="5%">
                <?php echo $this->Filter->sort_link(__('ID'), "$model_name.id") ?>
            </th>
            <th>
                <?php echo __("Avatar"); ?>
            </th>
            <th>
                <?php echo __("Type"); ?>
            </th>
            <th>
                <?php echo __("Full name"); ?>
            </th>
            <th>
                <?php echo __("Email"); ?>
            </th>
            <th>
                <?php echo __("Phone number"); ?>
            </th>
            <th>
                <?php echo __("Status"); ?>
            </th>

            <th>
                <?php echo __("Gxu"); ?>
            </th>
        </tr>
    </thead>
    <?php
    if (!empty($input)) {
        foreach ($input as $item) {
            ?>
            <tr role="row" class="">
                <td class="check-receiver"><input type="checkbox" class="check" name='ids[<?php echo $item[$model_name]['id']; ?>]'>
                <td><?php echo $item[$model_name]['id'] ?></td>
                <td><?php echo $this->HtmlExtend->get_image_from_link($item[$model_name]['avatar'], array('alt' => $item[$model_name]['name'], 'width' => 84, 'height' => 58)); ?></td>
                <td><?php echo $item[$model_name]['type'] ?></td>
                <td><?php echo $item[$model_name]['name'] ?></td>
                <td><?php echo $item[$model_name]['email'] ?></td>
                <td><?php echo $item[$model_name]['phone_number'] ?></td>
                <td><?php echo $item[$model_name]['status'] ?></td>
                <td align="right"><?php echo $item[$model_name]['gxu'] ?></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<script>
    $(function() {
        $("#check-all").click(function(event) {
            var checkboxes = document.getElementsByClassName('check');
            var n = checkboxes.length;
            if(this.checked){
                $(".check-receiver .checker > span").addClass("checked");
            }else{
                $(".check-receiver .checker > span").removeClass("checked");
            }
            for (var i = 0; i < n; i++) {
                checkboxes[i].checked = this.checked;
            }
        });
        $(".check").click(function(event) {
            if(!this.checked && document.getElementById('check-all').checked){
                $(".heading .checker > span").removeClass("checked");
                document.getElementById('check-all').checked = false;
            }
        });
    });
</script> 
