<div class="page-sidebar navbar-collapse collapse">
    <ul class="page-sidebar-menu" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
        <?php
        if($auth_user['type'] == 'admin'){
            echo $this->element('admins/default/sidebar_menu_item', array('config' => array(
                    'level1' => array(
                        'label' => __('Courses Management'),
                        'icon-class' => 'fa fa-leanpub',
                        'ofControllers' => array('courses', 'lessons', 'tags', 'scenes'),
                        'level2' => array(
                            array('label' => __('Courses'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'courses',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit', 'admin_detail', 'admin_add')),
                            array('label' => __('Lessons'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'lessons',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit', 'admin_detail', 'admin_add')),
                            array('label' => __('Scenes'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'scenes',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit', 'admin_detail', 'admin_add')),
                            array('label' => __('Tags'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'tags',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit', 'admin_detail', 'admin_add')),
                        )
                    )
            )));

            echo $this->element('admins/default/sidebar_menu_item', array('config' => array(
                    'level1' => array(
                        'label' => __('Users Management'),
                        'icon-class' => 'fa fa-user-plus',
                        'ofControllers' => array('admins', 'teachers', 'users'),
                        'level2' => array(
                            array('label' => __('Admins'),
                                'icon-class' => 'fa fa-users',
                                'url' => array('controller' => 'admins',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit', 'admin_add', 'admin_detail')),

                            array('label' => __('Teachers'),
                                'icon-class' => 'fa fa-users',
                                'url' => array('controller' => 'teachers',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit', 'admin_add', 'admin_detail')),

                            array('label' => __('Users'),
                                'icon-class' => 'fa fa-users',
                                'url' => array('controller' => 'users',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_add', 'admin_edit', 'admin_detail')),
                        )
                    )
            )));

            echo $this->element('admins/default/sidebar_menu_item', array('config' => array(
                    'level1' => array(
                        'label' => __('Profile'),
                        'icon-class' => 'fa fa-user',
                        'ofControllers' => array('profile'),
                        'level2' => array(
                            array('label' => __('My Profile'),
                                'icon-class' => 'fa fa-cogs',
                                'url' => array('controller' => 'profile',
                                    'action' => 'admin_index'),
                                'activeWhenInAction' => array('admin_index', 'admin_edit'))
                        )
                    )
            )));
        } else if($auth_user['type'] == 'teacher') {
            echo $this->element('admins/default/sidebar_menu_item_teacher', array('config' => array(
                    'level1' => array(
                        'label' => __('Lessons Management'),
                        'icon-class' => 'fa fa-leanpub',
                        'ofControllers' => array('lessons', 'scenes', 'skills'),
                        'level2' => array(
                            array('label' => __('Skills'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'skills',
                                    'action' => 'teacher_index'),
                                'activeWhenInAction' => array('teacher_index', 'teacher_edit', 'teacher_detail', 'teacher_add')),
                            array('label' => __('Lessons'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'lessons',
                                    'action' => 'teacher_index'),
                                'activeWhenInAction' => array('teacher_index', 'teacher_edit', 'teacher_detail', 'teacher_add')),
                            array('label' => __('Scenes'),
                                'icon-class' => 'fa fa-th-list',
                                'url' => array('controller' => 'scenes',
                                    'action' => 'teacher_index'),
                                'activeWhenInAction' => array('teacher_index', 'teacher_edit', 'teacher_detail', 'teacher_add')),
                        )
                    )
            )));
        }
        ?>
    </ul>
</div>