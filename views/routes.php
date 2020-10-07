<?php

function call($controller, $action)
{
    $controllerName = ucfirst($controller) . 'Controller';

    $controller = "App\\Controllers\\{$controllerName}";
    $model = "App\\Models\\{$controllerName}";

    $className = $controller;
    $template = new \App\Helpers\Template();
    $controller = new $className($template);
    $action = lcfirst(str_replace('-', '', ucwords($action . '-')));
    $controller->{$action}();
}

if (array_key_exists($app->controller, $app->controllers)) {
    if (in_array($app->action, $app->controllers[$app->controller])) {
        call($app->controller, $app->action);
    } else {
        echo $app->controller;
        echo $app->action;
        call('pages', 'error');
    }
} else {
    echo $app->controller;
    echo $app->action;
    call('pages', 'error');
}
