<?php
if (!empty($input)) {
    foreach ($input as $val) {
        ?>  
        <div class="form-group <?php echo isset($val['super_class']) ? $val['super_class'] : '';?>">
            <label class="col-md-2 control-label" ><?php echo $val['label']; ?>
                <?php
                if (isset($val['required']) && $val['required'] == TRUE) {
                    echo '<span class="red"> * </span>';
                }
                if ($val['label'])
                    echo ': ';
                ?>
            </label>
            <div class="<?php
            if (isset($val['description']) && !isset($val['class'])) {
                echo "col-md-9 col-sm-12";
            } else if (!isset($val['class'])) {
                echo "col-md-5 col-sm-12";
            } else {
                echo $val['class'];
            }
            ?>"><?php
                     if (is_array($val['input'])) {
                         
                     } else {
                         echo $val['input'];
                     }
                     ?>
            </div>
        </div>
        <?php
    }
}
?>