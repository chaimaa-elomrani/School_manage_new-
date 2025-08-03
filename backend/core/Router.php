<?php

// explation of this file with details : 
// this file is the core of the application it is the router of the application it is the one that is responsible for routing the requests to the appropriate controller and action

namespace Core ;

class Router{

    private array $routes = [
        'GET'=>[],
        'POST'=>[], 
        'PUT'=>[],
        'DELETE'=>[],
    ];


    // enregistrement des routes 

    public function get(string $uri, $action){
        $this->add('GET', $uri, $action);
    }

    public function post(string $uri , string $action){
        $this->add('POST', $uri, $action);
    }

    public function put(string $uri , string $action){
        $this->add('PUT', $uri, $action);
    }

    public function delete(string $uri , string $action){
        $this->add('DELETE', $uri, $action);
    }

    public function add(string $method, string $uri, string $action){
        $pattern = '#^' . preg_replace('#\{[^/]+\}#', '([^/]+)' , rtrim($uri , '/')) . '$#'; 
        $this->routes[$method][$pattern] = $action;
    }

    // lancement (dispatcher ) dispatcher est une fonction qui permet de lancer les routes

    public function dispatch(string $requestUri , string $requestMethod){
        $requestPath = parse_url($requestUri , PHP_URL_PATH);
        $routes = $this->routes[$requestMethod] ?? []; 

        foreach($routes as $pattern => $action){
            if(preg_match($pattern, $requestPath , $params)){
                array_shift($params); 
                return $this->callAction($action, $params); 

            }
        }

        http_response_code(404); 
        echo 'Route non trouvée'; 
    }

    
    // appel du controlleur@methode fonction qui permet d'appeler le controlleur et la methode

    private function callAction($action, array $params = []){
        // Handle closure/function actions
        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }
        
        // Handle Controller@method actions
        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            $controllerClass = "App\\Controllers\\{$controller}";
            
            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} not found");
            }
            
            $controllerInstance = new $controllerClass();
            
            if (!method_exists($controllerInstance, $method)) {
                throw new \Exception("Method {$method} not found in {$controllerClass}");
            }
            
            return call_user_func_array([$controllerInstance, $method], $params);
        }
        
        throw new \Exception("Invalid action type");
    }
}
