<?php

abstract class Application
{
    protected bool $debug = false;
    protected Request $request;
    protected Response $response;
    protected Session $session;
    protected DbManager $dbManager;
    protected Router $router;

    protected $loginAction = [];

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->dbManager = new DbManager();
        $this->router = new Router($this->registerRoutes());
    }

    protected function configure()
    {
    }

    abstract public function getRootDir();

    abstract protected function registerRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDbManager()
    {
        return $this->dbManager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    /**
     * @return void
     */
    public function run()
    {
        try {
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            [$controller, $action] = $this->loginAction;
            $this->runAction($controller, $action);
        }

        $this->response->send();
    }

    /**
     * @param class-string $controllerName
     * @param string $action
     * @param array $params
     * @return void
     * @throws HttpNotFoundException
     */
    public function runAction($controllerName, $action, $params = [])
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';

        $controller = $this->findController($controllerClass);
        if ($controller === false) {
            throw new HttpNotFoundException($controllerClass . ' controller is not found');
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    /**
     * @param $controllerClass
     * @return false|Controller
     */
    public function findController($controllerClass)
    {
        if (!class_exists($controllerClass)) {
            $controllerFile = $this->getControllerDir() . '/' . $controllerClass . '.php';
        }
        if (!is_readable($controllerFile)) {
            return false;
        } else {
            require_once $controllerFile;

            if (!class_exists($controllerClass)) {
                return false;
            }
        }

        return new $controllerClass($this);
    }

    /**
     * @param HttpNotFoundException $e
     * @return void
     */
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOC
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html lang="ja">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>404</title>
        </head>
        <body>
            {$message}
        </body>
        </html>
        EOC);
    }
}
