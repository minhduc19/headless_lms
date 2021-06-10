<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
Router::connect('/', array('controller' => 'public', 'action' => 'index'));
Router::connect('/admin',array('controller'=>'public','action'=>'login','admin'=>true));
Router::connect("/admin/login", array('controller' => 'public', 'action' => 'login', 'admin' => true));
Router::connect('/teacher',array('controller'=>'public','action'=>'login','teacher'=>true));
Router::connect("/teacher/login", array('controller' => 'public', 'action' => 'login', 'teacher' => true));

Router::connect('/t/:id', array('controller' => 'public', 'action' => 'info'));
Router::connect('/lesson/:id', array('controller' => 'public', 'action' => 'lesson_detail'));
Router::connect('/teacher_lesson/:id/:user_id', array('controller' => 'public', 'action' => 'lesson_detail', 'teacher' => true));
Router::connect('/scene/:id/:user_id', array('controller' => 'public', 'action' => 'scene_detail'));

// API
Router::connect("/api/v1/config", array('controller' => 'public', 'action' => 'config'));
// User
Router::connect("/api/v1/user/register", array('controller' => 'api_users', 'action' => 'register'));
Router::connect("/api/v1/user/login", array('controller' => 'api_users', 'action' => 'login'));
Router::connect("/api/v1/user/login_facebook", array('controller' => 'api_users', 'action' => 'login_facebook'));
Router::connect("/api/v1/user/login_google", array('controller' => 'api_users', 'action' => 'login_google'));
Router::connect("/api/v1/user/login_apple", array('controller' => 'api_users', 'action' => 'login_apple'));
Router::connect("/api/v1/user/profile", array('controller' => 'api_users', 'action' => 'profile'));
Router::connect("/api/v1/user/update_profile", array('controller' => 'api_users', 'action' => 'update_profile'));
Router::connect("/api/v1/favorites", array('controller' => 'api_users', 'action' => 'favorites'));
Router::connect("/api/v1/unfavorites", array('controller' => 'api_users', 'action' => 'unfavorites'));
Router::connect("/api/v1/user/mycourses", array('controller' => 'api_users', 'action' => 'mycourses'));

// Tags
Router::connect("/api/v1/tags", array('controller' => 'api_courses', 'action' => 'tags', 'api' => true));

// Search
Router::connect("/api/v1/search", array('controller' => 'api_search', 'action' => 'index', 'api' => true));
Router::connect("/api/v1/tracking", array('controller' => 'api_tracking', 'action' => 'index', 'api' => true));

// Courses
Router::connect("/api/v1/courses", array('controller' => 'api_courses', 'action' => 'index', 'api' => true));
Router::connect("/api/v1/courses/:id", array('controller' => 'api_courses', 'action' => 'detail', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/lessons/:id", array('controller' => 'api_lessons', 'action' => 'detail', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/lessons/recents", array('controller' => 'api_lessons', 'action' => 'recents', 'api' => true));
Router::connect("/api/v1/lessons/new_releases", array('controller' => 'api_lessons', 'action' => 'new_releases', 'api' => true));

// Teacher
Router::connect("/api/v1/teachers", array('controller' => 'api_teachers', 'action' => 'index', 'api' => true));
Router::connect("/api/v1/teachers/:id", array('controller' => 'api_teachers', 'action' => 'detail', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/teachers/follow", array('controller' => 'api_teachers', 'action' => 'follow', 'api' => true));
Router::connect("/api/v1/teachers/skills", array('controller' => 'api_teachers', 'action' => 'skills', 'api' => true));
Router::connect("/api/v1/teachers/lessons", array('controller' => 'api_teachers', 'action' => 'lessons', 'api' => true));
Router::connect("/api/v1/teachers/lessons/:id", array('controller' => 'api_teachers', 'action' => 'lesson_detail', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/teachers/lessons/reset/:id", array('controller' => 'api_teachers', 'action' => 'lesson_reset', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/teachers/scene/:id", array('controller' => 'api_teachers', 'action' => 'scene', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/teachers/submissions", array('controller' => 'api_teachers', 'action' => 'submissions', 'api' => true));

Router::connect("/api/v1/units/:id", array('controller' => 'api_units', 'action' => 'detail', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/units/reset/:id", array('controller' => 'api_units', 'action' => 'reset', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/scene/:id", array('controller' => 'api_units', 'action' => 'scene', 'api' => true), array('id' => '[0-9]+'));
Router::connect("/api/v1/scene/:id/comments", array('controller' => 'api_units', 'action' => 'comments', 'api' => true), array('id' => '[0-9]+'));

Router::connect("/api/v1/comments", array('controller' => 'api_comments', 'action' => 'index', 'api' => true));
Router::connect("/api/v1/submissions", array('controller' => 'api_submissions', 'action' => 'index', 'api' => true));

// Upload
Router::connect("/upload", array('controller' => 'public', 'action' => 'upload', 'api' => false));
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
