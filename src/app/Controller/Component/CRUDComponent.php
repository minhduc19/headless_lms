<?php

class CRUDComponent extends Component
{

    public $components = array('ExtendControl');
    private $controller;

    public function initialize(\Controller $controller)
    {
        $this->controller = &$controller;
        $this->Paginator = $controller->Paginator;
        $this->Session = $controller->Session;
        $this->request = $controller->request;
    }

    public function do_with_selected_ids(callable $callback, $actionRedirect = 'index')
    {
        if (!$this->request->is('post')) {
            $this->Session->setFlash(__('Please use GUI for this action'), 'flash-error');
            $this->controller->redirect(array('action' => $actionRedirect));
        }
        if (!isset($this->request->data['ids']) || empty($this->request->data['ids'])) {
            $this->Session->setFlash(__('You have to select items to execute this function'), 'flash-error');
            $this->controller->redirect(array('action' => $actionRedirect));
        }
        $ids = array_keys($this->request->data['ids']);
        $ids = array_filter($ids, function($id) {
            return is_numeric($id);
        });
        $callback($ids);
    }
    
    public function do_with_selected_ids_mongo(callable $callback, $actionRedirect = 'index')
    {
        if (!$this->request->is('post')) {
            $this->Session->setFlash(__('Please use GUI for this action'), 'flash-error');
            $this->controller->redirect(array('action' => $actionRedirect));
        }
        if (!isset($this->request->data['ids']) || empty($this->request->data['ids'])) {
            $this->Session->setFlash(__('You have to select items to execute this function'), 'flash-error');
            $this->controller->redirect(array('action' => $actionRedirect));
        }
        $ids = array_keys($this->request->data['ids']);
        $ids = array_filter($ids, function($id) {
            return !is_null($id);
        });
        $callback($ids);
    }

    /**
     * Return basic paginated model, all data in $this->request->query['cond_<some cond>'] is used to filter
     * 	value that is equals to empty string is ignored(for example: $this->request->query['a'] = '')
     * @param type $model_name 
     * @param type $search_column_name
     * 
     */
    public function basic_paginated_model($model_name, $search_column_name, $otherSettings = null, $likeColumns = null, $relate_model_name = null, $relate_search_colum_name = null)
    {
        if (!isset($this->request->query['limit'])) {
            $this->request->query['limit'] = 10;
        }
        $this->Paginator->settings = array(
            'paramType' => 'querystring',
//			'order' => array("{$model_name}.id" => 'desc', "{$model_name}.status" => 'asc')
        );
        $showAll = false;
        if ($this->request->query['limit'] == 'all') {
            $showAll = true;
            unset($this->request->query['limit']);
            $numRow = $this->controller->$model_name->find('count');
            $this->Paginator->settings['limit'] = $numRow;
        }
        if (isset($this->request->query['order_by']) && isset($this->request->query['order'])) {
            $this->Paginator->settings['order'] = array(
                $this->request->query['order'] => $this->request->query['order_by']
            );
        }
        if (isset($otherSettings['conditions'])) {
            unset($otherSettings['conditions']['order']);
            unset($otherSettings['conditions']['order_by']);
            unset($otherSettings['conditions']['limit']);
            unset($otherSettings['conditions']['page']);
            unset($otherSettings['conditions'][$search_column_name]);
        }
        if (!empty($likeColumns)) {
            foreach ($likeColumns as $likeColumn) {
                if (!isset($otherSettings['conditions'][$likeColumn]))
                    continue;
                $otherSettings['conditions'][$likeColumn . ' LIKE'] = '%' . $otherSettings['conditions'][$likeColumn] . '%';
                unset($otherSettings['conditions'][$likeColumn]);
            }
        }
        if (isset($this->request->query[$search_column_name]) &&
                !empty($this->request->query[$search_column_name])) {
            $this->Paginator->settings['conditions'] = array(
                "{$model_name}.{$search_column_name} LIKE" => '%' . $this->request->query[$search_column_name] . '%'
            );
            //TODO: support multiple conditions
        }
        //Search for relate Model
        if (!is_null($relate_model_name) && isset($this->request->query[$relate_search_colum_name]) &&
                !empty($this->request->query[$relate_search_colum_name])) {
            $this->Paginator->settings['conditions'] = array(
                "{$relate_model_name}.{$relate_search_colum_name} LIKE" => '%' . $this->request->query[$relate_search_colum_name] . '%'
            );
            //TODO: support multiple conditions
        }

        if (!empty($otherSettings)) {
            $this->Paginator->settings = array_merge_recursive($this->Paginator->settings, $otherSettings);
        }
        try {
            $retval = $this->Paginator->paginate($model_name);
            if ($showAll) {
                $this->request->query['limit'] = 'all';
            }
            return $retval;
        } catch (NotFoundException $e) {
            $this->Session->setFlash(__('Oops, this page doesn\'t exist.'), 'flash-error');
            $this->controller->redirect(array('action' => 'index'));
        }
    }

    public function ajax_delete_individual_by_id($id, AppModel $model, $cascade = false, $withConditions = null)
    {
        $this->controller->layout = 'ajax';
        if (!empty($withConditions)) {
            $ret = $model->deleteAll(array_merge($withConditions, array(
                'id' => $id
            )));
        } else {
            $ret = $model->delete($id, $cascade);
            $this->Session->setFlash(__('Done delete record id %s', $ret));
        }
        if ($this->request->is('post') && $ret) {
            $this->Session->setFlash(__('Done delete record id %s in %s', $id, Inflector::humanize($model->name)), 'flash-success');
            $this->controller->set('success', true);
        } else {
            $this->controller->set('success', false);
        }
    }
   
    public function ajax_soft_delete_individual_by_id($id, AppModel $model, $withConditions = null)
    {
        $this->controller->layout = 'ajax';
        $deleted_date = date('Y-m-d H:i:s', time());
        $model_name = $model->name;
        if (!empty($withConditions)) {
            $ret = $model->updateAll(array('deleted' => "'$deleted_date'"), array_merge($withConditions, array(
                "$model_name.id" => $id
            )));
        } else {
            $ret = $model->updateAll(array('deleted' => "'$deleted_date'"), array(
                "$model_name.id" => $id
            ));
        }
        if ($this->request->is('post') && $ret) {
            $this->Session->setFlash(__('Done delete record id %s in %s', $id, Inflector::humanize($model->name)), 'flash-success');
            $this->controller->set('success', true);
        } else {
            $this->controller->set('success', false);
        }
    }

    public function delete_checked(AppModel $model, $cascade = false, $then_redirect_to_action = 'index', $withConditions = null)
    {
        $this->do_with_selected_ids(function($ids) use ($model, $cascade, $then_redirect_to_action, $withConditions) {
            $conditions = array("{$model->name}.id" => $ids);
            if (!empty($withConditions)) {
                $conditions = array_merge($conditions, $withConditions);
            }
            if ($model->deleteAll($conditions, $cascade)) {
                $this->Session->setFlash(__('Done selected record in %s', Inflector::humanize($model->name)), 'flash-success');
            } else {
                $this->Session->setFlash(__('There is something wrong happened while deleting'), 'flash-error');
            }
            $this->controller->redirect(array('action' => $then_redirect_to_action));
        });
    }
    
    public function delete_checked_mongo(AppModel $model, $cascade = false, $then_redirect_to_action = 'index', $withConditions = null)
    {
        $this->do_with_selected_ids_mongo(function($ids) use ($model, $cascade, $then_redirect_to_action, $withConditions) {
            $conditions = array("{$model->name}.id" => array('$in' => $ids));
            if (!empty($withConditions)) {
                $conditions = array_merge($conditions, $withConditions);
            }
            if ($model->deleteAll($conditions, $cascade)) {
                $this->Session->setFlash(__('Done selected record in %s', Inflector::humanize($model->name)), 'flash-success');
            } else {
                $this->Session->setFlash(__('There is something wrong happened while deleting'), 'flash-error');
            }
            $this->controller->redirect(array('action' => $then_redirect_to_action));
        });
    }

    public function update_checked(AppModel $model, $update, $then_redirect_to_action = 'index', $withConditions = null)
    {
        $this->do_with_selected_ids(function($ids) use ($model, $update, $then_redirect_to_action, $withConditions) {
            $conditions = array("{$model->name}.id" => $ids);
            if (!empty($withConditions)) {
                $conditions = array_merge($conditions, $withConditions);
            }
            if ($model->updateAll($update, $conditions)) {
                $this->Session->setFlash(__('Done updated record in %s', Inflector::humanize($model->name)), 'flash-success');
            } else {
                $this->Session->setFlash(__('There is something wrong happened while updating'), 'flash-error');
            }

            $this->controller->redirect(array('action' => $then_redirect_to_action));
        });
    }

    public function update_all(AppModel $model, $update, $then_redirect_to_action = 'index')
    {
        if ($model->updateAll($update)) {
            $this->Session->setFlash(__('Done updated record in %s', Inflector::humanize($model->name)), 'flash-success');
        } else {
            $this->Session->setFlash(__('There is something wrong happened while updating'), 'flash-error');
        }
        $this->controller->redirect(array('action' => $then_redirect_to_action));
    }

    public function hide_checked(AppModel $model, $then_redirect_to_action = 'index', $withConditions = null)
    {
        $this->do_with_selected_ids(function($ids) use ($model, $then_redirect_to_action, $withConditions) {
//			$ids = "(" . implode(',', $ids) . ")";
            $conditions = array("{$model->name}.id" => $ids);
            if (!empty($withConditions)) {
                $conditions = array_merge($conditions, $withConditions);
            }
            $date = date('Y-m-d H:i:s', time());
//			pr($conditions);die();
            if ($model->updateAll(array('deleted' => "'$date'"), $conditions)) {
                $this->Session->setFlash(__('Done selected record in %s', Inflector::humanize($model->name)), 'flash-success');
            } else {
                $this->Session->setFlash(__('There is something wrong happened while deleting'), 'flash-error');
            }
            $this->controller->redirect(array('action' => $then_redirect_to_action));
        });
    }

    public function detail_of($model, $id, callable $then)
    {
        $this->ExtendControl->if_exists_id_then(function($id) use($model, $then) {
            $this->ExtendControl->if_found_then(function($item) use($then) {
                if (isset($then)) {
                    $then($item);
                }
            }, $model->findById($id));
        }, $id);
    }

    public function basic_transaction(AppModel $model, callable $callback, callable $onSuccess = null, callable $onFailure = null)
    {
        $model->begin();
        try {
            $callback();
            $model->commit();
        } catch (Exception $ex) {
            $model->rollback();
            $this->Session->setFlash($ex->getMessage(), 'flash-error');
            //$this->Session->setFlash(__('Transaction fail'), 'flash-error');
            if ($onFailure) {
                $onFailure($ex);
            } else {
                throw $ex;
            }
        }
        if ($onSuccess) {
            $onSuccess();
        }
    }

}
