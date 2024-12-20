<?php

class Router {
    protected $routes = array();
    protected $params = array();
     
    public function add($route, $params){
        $this -> routes[$route] = $params;
    }
    public function getRoutes() {
        return $this -> routes;
    }



    public function matchRoutes($url) {
        
        foreach($this -> routes as $route => $params) {
            $url = rtrim($url, '/');
            $pattern = str_replace(['{id}', '/'], ['([0-9]{1,3})', '\/'], $route);
            
            $pattern = '/^\/' . $pattern . '$/';
            //echo $pattern ;
            //echo "Patrón generado: $pattern\n";
            //echo "URL analizada: $url\n";
            //echo $url;
            //if(preg_match($pattern, $url)) {
            //    $this -> params = $params;
            //    return true;
            //}
            if (preg_match($pattern, $url, $matches)) {
                $this->params = $params;
                
                // Si encontramos una coincidencia, guardamos el ID en los parámetros
                if (isset($matches[1])) {
                    $this->params['id'] = $matches[1]; // Guarda el ID capturado
                    //var_dump($this->params);
                }
                return true;
            }
        
        }
        return false;
    }

    public function getParams() {
        return $this -> params;
    }



}





?>