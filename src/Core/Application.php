<?php
namespace MobiCity\Core;

use Dotenv\Dotenv;

class Application 
{
    protected Router $router;
    protected Request $request;

    public function __construct()
    {
        $this->loadEnv();
        $this->request = new Request();
        $this->router = new Router();
    }

    public function loadEnv()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
        $dotenv->load();
    }

    protected function loadRoutes()
    {
        $router = $this->router;
        require_once __DIR__ . '/../routes.php';
    }

    public function run()
    {
        $this->loadRoutes();
        $response = $this->router->dispatch($this->request);
        $response->send();
    }
}