<?php
class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = include_once(ROOT . '/config/routes.php');
    }

    private function getUri()
    {
        if(!empty($_SERVER['REQUEST_URI']))
        {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    public function go()
    {
        $uri = $this->getUri();
        $isBadAdress = true;

        foreach( $this->routes as $map => $classMethod)
        {
            if(preg_match("~^$map~", $uri) == 1)
            {
                $isBadAdress = false;

                include_once(ROOT . "/controllers/{$classMethod[0]}.php");

                $controllerObject = new $classMethod[0]();
                $method = $classMethod[1];
                $controllerObject-> $method($uri);
            }
        }

        if($isBadAdress)
        {
            if(empty($_SESSION['userKosmos']))
            {
                include_once(ROOT . "/dist/auth/index.html");
            }else{
                switch ($_SESSION['userKosmos']['user_role']) {
                    case NULL:
                        include_once(ROOT . "/dist/auth/index.html");
                        break;
                    case 'Адміністратор':
                        include_once(ROOT . "/dist/admin/index.html");
                        break;
                    case "Користувач":
                        include_once(ROOT . "/dist/user/index.html");
                        break;
                    default:
                        include_once(ROOT . "/dist/auth/index.html");
                        break;
                }
            }
        }
    }
}