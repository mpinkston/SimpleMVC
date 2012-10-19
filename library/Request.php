<?php

class Request
{
    const DEFAULT_CONTROLLER = 'default';
    const DEFAULT_ACTION     = 'index';

    /**
     * @var string
     */
    protected $controller = self::DEFAULT_CONTROLLER;

    /**
     * @var string
     */
    protected $action = self::DEFAULT_ACTION;

    /**
     * @var string
     */
    protected $params;

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }
}