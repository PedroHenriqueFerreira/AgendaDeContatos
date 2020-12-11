<?php
require(__DIR__ . '/config/connection.php');
require(__DIR__ . '/utils/render.php');

class Routes
{
  public $url;
  public $routes;

  function __construct()
  {

    $this->url = parse_url(
      $_SERVER['REQUEST_URI'],
      PHP_URL_PATH
    );

    require(__DIR__ . '/routes.php');
    $this->routes = $myRoutes;

    session_start();

    $this->controllers();
  }

  function controllers()
  {

    if (filter_var($this->url, FILTER_SANITIZE_NUMBER_INT)) {
      $_POST['id'] = filter_var($this->url, FILTER_SANITIZE_NUMBER_INT);

      $this->url = implode(
        ':id',
        explode(
          filter_var(
            $this->url,
            FILTER_SANITIZE_NUMBER_INT
          ),
          $this->url
        )
      );
    }
    $found = false;

    foreach ($this->routes as $i => $v) {
      if ($i == $this->url) {
        $found = true;
      }
    }


    if (!$found) {
      $this->control(
        $this->routes['*'][0],
        $this->routes['*'][1]
      );
    } else {
      $this->control(
        $this->routes[$this->url][0],
        $this->routes[$this->url][1]
      );
    }
  }

  function control($page, $action)
  {
    require(__DIR__ . '/controllers' . '/' . ucfirst($page) . 'Controller.php');

    $controller = ucfirst($page) . 'Controller';
    (new $controller)->$action();
  }
}

$routes = new Routes();
