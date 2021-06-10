<div class="top-menu">
    <ul class="nav navbar-nav pull-right">
        <li class="dropdown dropdown-user">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                <!--<img alt="" class="img-circle" src="../../assets/admin/layout/img/avatar3_small.jpg"/>-->
                <span class="username username-hide-on-mobile">
                    <?php echo __('Hello %s', $auth_user['email']); ?> </span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-default">
                <li>
                    <a href="<?php echo $this->Html->url(array('controller' => 'profile', 'action' => 'index', 'admin' => TRUE)); ?> ">
                        <i class="icon-user"></i> My Profile </a>
                </li>
             
                <li>
                    <?php if($auth_user['type'] == 'admin'){?>
                        <a href="<?php echo $this->Html->url(array('controller' => 'public', 'action' => 'logout', 'admin' => TRUE)); ?> ">
                            <i class="icon-key"></i> <?php echo __('Logout'); ?></a>
                    <?php } else { ?>
                        <a href="<?php echo $this->Html->url(array('controller' => 'public', 'action' => 'logout', 'teacher' => TRUE)); ?> ">
                            <i class="icon-key"></i> <?php echo __('Logout'); ?></a>
                    <?php } ?>
                </li>
            </ul>
        </li>
    </ul>
</div>
<!--end nofication-->