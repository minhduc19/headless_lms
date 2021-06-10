<?php ?>
<div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover" id="datatable_products">
        <thead>
            <tr role="row" class="heading">
                <!-- CHANGE HERE -->
                <th width="10%">
                    <?php echo __('ID'); ?>
                </th>
                <th>
                    <?php echo __('Icon'); ?>
                </th>
                <th>
                    <?php echo __('Name'); ?>
                </th>
                <th>
                    <?php echo __('Items'); ?>
                </th>
                <!-- END CHANGE HERE -->
            </tr>
        </thead>
        <?php
        if (!empty($games)) {
            foreach ($games as $game) {
                $game_items = '';
                if(!empty($items)){
                    foreach($items as $item){
                        if($item['Item']['game_id'] == $game['Game']['id']){
                            $game_items .= $item['Item']['item_id'] . ' | ' . $item['Item']['amount'] . "<br>";
                        }
                    }
                }
                ?>
                <tr role="row" class="filter">
                    <!-- CHANGE HERE -->
                    <td><?php echo $game['Game']['id'] ?></td>
                    <td><?php echo $this->HtmlExtend->get_image_from_link($game['Game']['icon'], array('id' => 'icon-image-preview', 'alt' => 'icon image', 'width' => 60, 'height' => 58)); ?></td>
                    <td><?php echo $game['Game']['name'] ?></td>
                    <td><?php echo $game_items ?></td>
                    <!-- END CHANGE HERE -->
                </tr>
                <?php
            }
        }
        ?>
    </table>
</div>