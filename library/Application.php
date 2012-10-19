<?php

define('CONTROLLER_DIR', LIB_PATH . '/Controllers');
define('MODEL_DIR',      LIB_PATH . '/Models');
define('VIEW_DIR',       LIB_PATH . '/Views');

require LIB_PATH . '/Request.php';
require LIB_PATH . '/Response.php';

class Application
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request();
        }
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Response();
        }
        return $this->response;
    }

    /**
     * Determine which action to run based on the request
     */
    protected function route()
    {
        $request = $this->getRequest();

        // http://www.php.net/manual/en/function.parse-url.php
        $uri = parse_url($_SERVER['REQUEST_URI']);

        // Determine the controller and action from the path
        if (isset($uri['path'])) {
            $pathArr = explode('/', $uri['path']);

            if (isset($pathArr[1]) && ctype_alpha($pathArr[1])) {
                $request->setController($pathArr[1]);
            }

            if (isset($pathArr[2]) && ctype_alpha($pathArr[2])) {
                $request->setAction($pathArr[2]);
            }
        }
    }

    /**
     * Execute the action that was determined by routing
     *
     * @return mixed
     * @throws Exception
     */
    protected function dispatch()
    {
        $request        = $this->getRequest();
        $response       = $this->getResponse();
        $controllerName = $request->getController();
        $actionName     = $request->getAction();

        // Clean up the controller name
        $controllerName = sprintf('%sController', ucfirst($controllerName));
        $controllerFile = sprintf('%s/Controllers/%s.php', __DIR__, $controllerName);

        // Create a controller instance if possible.
        if (is_readable($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
            } else {
                throw new Exception(sprintf(
                    "The controller class (%s) could not be loaded.",
                    $controllerName
                ));
            }
        } else {
            throw new Exception(sprintf(
                "The specified controller (%s) could not be found.",
                $controllerName
            ));
        }

        // Clean up the action name
        $actionName = sprintf('%sAction', ucfirst($actionName));

        // Call the action
        if (method_exists($controller, $actionName)) {
            // Start output buffering and collect the contents
            // http://us3.php.net/manual/en/function.ob-start.php
            ob_start();
            $controller->$actionName();
            $body = ob_get_clean();
        } else {
            throw new Exception("The specified action does not exist.");
        }

        $response->setBody($body);
    }

    /**
     * Get the response, and display it within the layout
     */
    protected function render()
    {
        $response = $this->getResponse();
        include VIEW_DIR . '/layout.phtml';
    }

    /**
     * Just about any web app can be boiled down to
     * route, dispatch, and render.
     */
    public function run()
    {
        try {
            $this->route();
            $this->dispatch();
            $this->render();
        } catch (Exception $ex) {
            // Do something with the error..
            echo "<h2>An error occurred:</h2>" . $ex->getMessage();
        }
    }
}