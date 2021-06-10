<?php $is_open_level1 = in_array($arrParam['controller'], $config['level1']['ofControllers']); ?>
<li class="<?php echo $is_open_level1 ? 'start active open' : '' ?>">
    <a href="javascript:;">
        <i class="<?php echo $config['level1']['icon-class'] ?>"></i>
        <span class="title"><?php echo $config['level1']['label'] ?></span>
        <span class="arrow <?php echo $is_open_level1 ? 'open' : '' ?>"></span>
    </a>
    <?php if (isset($config['level1']['level2'])): ?>
        <ul class="sub-menu">
            <?php foreach ($config['level1']['level2'] as $level2): ?>
                <?php
                $is_active_level2 = $arrParam['controller'] == $level2['url']['controller'];
                if (isset($level2['activeWhenInAction'])) {
                    $is_active_level2 = $is_active_level2 && in_array($arrParam['action'], $level2['activeWhenInAction']);
                }
                ?>
                <li class="<?php echo $is_active_level2 ? 'active' : '' ?>">
                    <?php echo
                    $this->Html->link("<i class='{$level2['icon-class']}'></i>" . '  ' . $level2['label'], array('controller' => $level2['url']['controller'],
                        'action' => $level2['url']['action'],
                        'teacher' => true), array('div' => false,
                        'label' => false,
                        'escape' => false))
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</li>

