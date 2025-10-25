<?php

namespace MobiCity\Core;

class Router
{
    protected $routes = [];

    public function addRoute(string $route, mixed $handler, string $httpMethod, string $name)
    {
        $this->routes[] = [
            'route' => $route,
            'handler' => $handler,
            'httpMethod' => $httpMethod,
            'name' => $name
        ];
    }

    public function get(string $route, mixed $handler, $name = "")
    {
        $this->addRoute($route, $handler, 'GET', $name);
    }

    public function post(string $route, mixed $handler, $name = "")
    {
        $this->addRoute($route, $handler, 'POST', $name);
    }

    public function dispatch(Request $request): Response
    {
        $uri = $request->getUri();
        $requestMethod = $request->getMethod();

        foreach ($this->routes as $route) {
            [$pattern, $paramNames] = $this->getPattern($route['route']);

            if (preg_match($pattern, $uri, $matches) && $route['httpMethod'] == $requestMethod) {
                array_shift($matches);
                $handler = $route['handler'];

                $routeParams = array_combine($paramNames, $matches);

                $bodyParams = $request->getBodyParams();
                $queryParams = $request->getQueryParams();
                $params = (object)array_merge($queryParams, $bodyParams, $routeParams);
                
                if (is_callable($handler)) {
                    $responseContent = !empty($params)
                        ? call_user_func($handler, $params)
                        : call_user_func($handler);

                    return new Response($responseContent);
                }

                // if (is_string($handler) && strpos($handler, '::') !== false) {
                //     [$controllerClass, $method] = explode('::', $handler);
                //     $controller = "App\\Controllers\\$controllerClass.php";

                //     if (!class_exists($controller)) {
                //         return new Response("500 Controller [$controller] not found", 500);
                //     }

                //     $responseContent = !empty($params) 
                //         ? $controller->$method($params) 
                //         : $controller->$method();

                //     return new Response($responseContent);
                // }

                if (is_array($handler) && count($handler) == 2) {
                    [$controllerClass, $method] = $handler;

                    if (!class_exists($controllerClass)) {
                        return new Response("500 Controller [$controllerClass] not found", 500);
                    }

                    $controller = new $controllerClass();

                    if (!method_exists($controller, $method)) {
                        return new Response("500 Method [$method] not found in Controller [$controllerClass]",500);
                    }

                    $responseContent = !empty($params) 
                        ? $controller->$method($params) 
                        : $controller->$method();

                    return new Response($responseContent);
                }

                return new Response("500 Invalid action [$handler] for this route.", 500);
            }
        }

        return new Response('404 Not Found', 404);
    }

    private function getPattern(string $route): array
    {
        $paramNames = [];
        $regex = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([a-zA-Z0-9_]+))?\}/', function ($matches) use (&$paramNames) {
            $name = $matches[1];
            $type = $matches[2] ?? 'any';

            $paramNames[] = $name;

            return '('.$this->getRegexForType($type).')';
        }, $route);

        return ['#^' . $regex . '$#', $paramNames];
        // $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
        //     return '([a-zA-Z0-9_\-]+)';
        // }, $route);

        // return "#^$pattern$#";
    }

    private function getRegexForType(string $type): string
    {
        return match ($type) {
            'int' => '\d+',
            'slug' => '[a-z0-9\-]+',
            'uuid' => '[0-9a-fA-F\-]{36}',
            'alpha' => '[a-zA-Z]+',
            default => '[^/]+', // "any"
        };
    }
}