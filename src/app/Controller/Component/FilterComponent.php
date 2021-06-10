<?php

App::uses('Component', 'Controller');

class FilterComponent extends Component {

    private $controller;
    private $filters = array();

    public function __construct(\ComponentCollection $collection, $settings = array()) {
	$this->controller = $collection->getController();
	parent::__construct($collection, $settings);
    }

    public function build($default = array()) {
	$this->filters = array_merge($default, $this->controller->request->query);
	if (isset($this->controller->paginate) && isset($this->controller->request->query['limit_page'])) {
	    if (!in_array($this->controller->request->query['limit_page'], array(5, 10, 20, 50, 100, 200, 500, -1))) {
		$this->controller->request->query['limit_page'] = 10;
	    }
	    $this->controller->paginate['limit'] = $this->controller->request->query['limit_page'];
	}
	if (isset($this->controller->paginate) && isset($this->controller->request->query['order'])) {
	    if (!isset($this->controller->request->query['order_by'])) {
		$order_by = 'asc';
	    } else {
		$order_by = $this->controller->request->query['order_by'] == 'desc' ? 'desc' : 'asc';
	    }
	    $this->controller->paginate['order'] = array($this->controller->request->query['order'] => $order_by);
	}
//        $this->update_filters();
    }

    public function get_filters() {
	return $this->filters;
    }

    public function get_filters_saved($type = 'filter') {
	$user_property['user_id'] = $this->controller->viewVars['auth_user']['id'];
	$user_property['controller'] = $this->controller->request->params['controller'];
	$user_property['action'] = $this->controller->request->params['action'];

	$conditions = array(
	    'UserProperty.user_id' => $user_property['user_id'],
	    'UserProperty.controller' => $user_property['controller'],
	    'UserProperty.action' => $user_property['action'],
	);
	if ($type == 'search') {
	    $conditions += array('UserProperty.filter_value != ' => null);
	}
	$items = $this->controller->UserProperty->find('all', array(
	    'contain' => FALSE,
	    'conditions' => $conditions,
	    'order' => array('sort_level' => 'ASC')
	));
	return $items;
    }

    public function get_conditions() {
	$conditions = array();
	$search_data = $this->get_filters_saved('search');
	if (!empty($search_data)) {
	    foreach ($search_data as $key => $value) {
		$field_name = $value['UserProperty']['model_name'] . '.' . $value['UserProperty']['field_name'];
		$conditions += array("$field_name LIKE " => '%' . $value['UserProperty']['filter_value'] . '%');
	    }
	}
	return $conditions;
    }

    public function update_conditions($options = array()) {
	$controller = $this->controller;
	$user_property = array();
	$user_property['user_id'] = $this->controller->viewVars['auth_user']['id'];
	$user_property['controller'] = $this->controller->request->params['controller'];
	$user_property['action'] = $this->controller->request->params['action'];

	$conditions = array(
	    'UserProperty.user_id' => $user_property['user_id'],
	    'UserProperty.controller' => $user_property['controller'],
	    'UserProperty.action' => $user_property['action'],
	);
	$delete_conditions = $conditions;
	$delete_conditions += array('UserProperty.filter_value != ' => null);
	$controller->UserProperty->deleteAll($delete_conditions);
	if (!empty($options)) {
	    $time = time();
	    $arr_data_save = array();
	    foreach ($options as $field => $filter_value) {
		$arr_field = explode(".", $field);
		$user_property['model_name'] = $arr_field[0];
		$user_property['field_name'] = $arr_field[1];
		$user_property['filter_value'] = $filter_value;
		$user_property['created_at'] = $time;
		$user_property['modified_at'] = $time;
		$arr_data_save[] = array('UserProperty' => $user_property);
	    }
	    if (!empty($arr_data_save)) {
		if (!$controller->UserProperty->saveAll($arr_data_save)) {
		    $controller->Session->setFlash(__("Invalid data. Please try again."));
		}
	    }
	}
    }

    private function update_filters() {
	$filter = $this->get_filters();
	$user_property['user_id'] = $this->controller->viewVars['auth_user']['id'];
	$user_property['controller'] = $this->controller->request->params['controller'];
	$user_property['action'] = $this->controller->request->params['action'];

	$conditions = array(
	    'UserProperty.user_id' => $user_property['user_id'],
	    'UserProperty.controller' => $user_property['controller'],
	    'UserProperty.action' => $user_property['action'],
	);
	if (!empty($filter)) {
	    $arr_filter = explode(".", $filter['order']);
	    $user_property['model_name'] = $arr_filter[0];
	    $user_property['field_name'] = $arr_filter[1];
	    $user_property['sort_order'] = $filter['order_by'];
	    $user_property['sort_level'] = 1;
	    //Set other sort_level = 2
	    $items = $item = $this->controller->UserProperty->find('all', array(
		'conditions' => $conditions,
	    ));
	    if (!empty($items)) {
		$arr_data = array();
		foreach ($items as $key => $value) {
		    $arr_data[] = array('UserProperty' => array('id' => $value['UserProperty']['id'], 'sort_level' => 2));
		}
		$this->controller->UserProperty->saveAll($arr_data);
	    }

	    $conditions += array(
		'UserProperty.model_name' => $user_property['model_name'],
		'UserProperty.field_name' => $user_property['field_name'],
	    );
	    $item = $this->controller->UserProperty->find('first', array(
		'conditions' => $conditions,
	    ));
	    $time = time();
	    if (!empty($item)) {//UPDATE
		$user_property['modified_at'] = $time;
		$this->controller->UserProperty->id = $item['UserProperty']['id'];
		if (!$this->controller->UserProperty->save($user_property)) {
		    //TODO: ERROR
		}
	    } else {//INSERT
		$user_property['created_at'] = $time;
		$user_property['modified_at'] = $time;
		$this->controller->UserProperty->create();
		if (!$this->controller->UserProperty->save($user_property)) {
		    //TODO: ERROR
		}
	    }
	} else {//CLEAR Filter
	    $this->controller->UserProperty->deleteAll($conditions);
	}
    }

    public function handle($action) {
	$controller = $this->controller;
	$url = array_merge($controller->request->params['pass'], $controller->request->params['named']);
	if (!empty($controller->request->query)) {
	    $url['?'] = $controller->request->query;
	} else {
	    $url['?'] = array();
	}
	if (isset($controller->request->data['redirect'])) {
	    $url['action'] = $controller->request->data['redirect'];
	} else {
	    $url['action'] = $action;
	}
	unset($url['page']);
	$url['?'] = array_merge($url['?'], $controller->request->data);
	unset($url['?']['redirect']);
	$this->controller->redirect($url);
    }

    public function clear($action) {
	$controller = $this->controller;
	$user_property['user_id'] = $this->controller->viewVars['auth_user']['id'];
	$user_property['controller'] = $this->controller->request->params['controller'];

	$conditions = array(
	    'UserProperty.user_id' => $user_property['user_id'],
	    'UserProperty.controller' => $user_property['controller'],
	    'UserProperty.action' => $action,
	);
	if ($user_property['controller'] == 'client_master_suppliers' && $action == 'index') {
	    $conditions = array(
		'UserProperty.user_id' => $user_property['user_id'],
		'UserProperty.controller' => $user_property['controller'],
		'UserProperty.action' => $action,
		'UserProperty.model_name' => 'MasterSupplier',
		'UserProperty.field_name != ' => 'country_id',
	    );
	}
	$controller->UserProperty->deleteAll($conditions);
	$url = array_merge($controller->request->params['pass'], $controller->request->params['named']);
	unset($url['page']);
	if (isset($controller->request->query['redirect'])) {
	    $url['action'] = $controller->request->query['redirect'];
	} else {
	    $url['action'] = $action;
	}
	unset($url['?']['redirect']);
	$this->controller->redirect($url);
    }

}
