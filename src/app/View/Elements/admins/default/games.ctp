<table class="tblForm" style="width: 45%">
    <?php
    if (!empty($input)) {
            ?>
            <tr>
                <td width="70%"> 
                    <select name="data[Game][]" class="form-control input-sm input-medium select2image" multiple="true" data-validation="required" required="required">
                        <?php
                            foreach ($arrGames as $id => $value){
                                if(in_array($value['id'], $input)){
                                    echo '<option selected value="' . $id . '" data-icon="' . $value['icon'] . '">' . $value['name'] . '</option>';
                                }else{
                                    echo '<option value="' . $id . '" data-icon="' . $value['icon'] . '">' . $value['name'] . '</option>';
                                }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
    } else {
        ?>
        <tr>
            <td width="70%">
                <select name="data[Game][]" class="form-control input-sm input-medium select2image" multiple="true" data-validation="required" required="required">
                    <?php
                        foreach ($arrGames as $id => $value){
                            echo '<option value="' . $id . '" data-icon="' . $value['icon'] . '">' . $value['name'] . '</option>';
                        }
                    ?>
                </select>
            </td>
        </tr>
        <?php
    }
    ?>
</table>