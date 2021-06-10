<?php

class ExtendControlComponent extends Component
{

    private $controller;

    public function initialize(\Controller $controller)
    {
        $this->controller = &$controller;
        $this->Session = $controller->Session;
        $this->request = $controller->request;
    }

    public function if_exists_data_then(callable $callback, $data = null, callable $else = null)
    {
        if (!isset($data)) {
            $this->Session->setFlash(__('%s/%s, data not found', $this->request->params['controller'], $this->request->params['action']), 'flash-error');
            if (isset($else)) {
                $else();
            } else {
                $this->requestAction(array('action' => 'index'));
            }
        } else {
            $callback($data);
        }
    }

    public function if_exists_id_then(callable $callback, $id = null, callable $else = null)
    {
        if (!isset($id)) {
            $this->Session->setFlash(__('%s/%s require you to fill id, please use GUI rather than input manually', $this->request->params['controller'], $this->request->params['action']), 'flash-error');
            if (isset($else)) {
                $else();
            } else {
                $this->requestAction(array('action' => 'index'));
            }
        } else {
            $callback($id);
        }
    }

    public function if_is_post_then(callable $callback, callable $else = null)
    {
        if (!$this->request->is('post')) {
            $this->Session->setFlash(__('%s/%s require you to use POST', $this->request->params['controller'], $this->request->params['action']), 'flash-error');
            if (isset($else)) {
                $else();
            } else {
                $this->requestAction(array('action' => 'index'));
            }
        } else {
            $callback();
        }
    }

    public function if_crud_complete_then(callable $callback = null, $crud_result = null, $show_error = false)
    {
        if ($crud_result) {
            $this->Session->setFlash(__('Done'), 'flash-success');
            $this->controller->set('success', true);
            if (!empty($callback)) {
                $callback($crud_result);
            }
        } else {
            $this->controller->set('success', false);
            if ($show_error) {
                $this->Session->setFlash(__('Error'), 'flash-error');
            }
        }
    }

    public function if_found_then(callable $callback = null, $result = null, callable $else = null, $show_error = true)
    {
        if ($result) {
            if ($callback) {
                $callback($result);
            }
        } else {
            if ($show_error) {
                $this->Session->setFlash(__('Id is not exists'), 'flash-error');
                $this->controller->redirect(array('action' => 'index'));
            }
            if ($else) {
                $else();
            }
        }
    }

    public function if_is_validate_then(callable $callback, $data, AppModel $model)
    {
        $model->set($data);
        if ($model->validates()) {
            $callback($data);
        } else {
            $this->Session->setFlash(__('Your data you input for %s is not valid, please checking again :)', Inflector::humanize($model->name)), 'flash-error');
        }
    }

    //TODO: if exists record
    //TODO: if create success/fail
    //TODO: if update success/fail
    //TODO: if delete success/fail
    //TODO: if some variable fail
}
