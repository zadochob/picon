<?php

namespace Picon\Lib;

class Controller{

    protected   $route;
    protected   $layout;
    protected   $view;
    protected   $viewVars;

    public function __construct($route){
        $this->route        =   $route;
        $this->security     =   new Security();
        $this->viewVars     =   array();
        $this->setView();
        $this->setLayout();
    }

    public function pre_action(){
        $this->security->check();
    }

    public function post_action(){

    }

    public function set($vars){
        $this->viewVars     =   array_merge($this->viewVars, $vars);
    }

    public function sendViewError($error_message, $level = 1, $duration = 1){
        if($duration <= 0)
            return false;
        $_SESSION["voice"]["errors"][$level][]   =   array(
                                                            "msg"       =>  $error_message, 
                                                            "timeleft"  =>  $duration
                                                        );
        return true;
    }

    public function sendViewMessage($message, $duration = 1){
        if($duration <= 0)
            return false;
        $_SESSION["voice"]["messages"][]   =   array(
                                                        "msg"       =>  $message, 
                                                        "timeleft"  =>  $duration
                                                    );
        return true;
    }

    public function setView($viewName   =   null, $viewDir = ""){
        !isset($viewName)   && $viewName    =   $this->route["action"]; 
        !$viewDir           && $viewDir     =   $this->route["controller"];
        $this->view =   !$viewName  ? "" : strtolower($viewDir . "/" . $viewName); 
    
    }

    public function setLayout($layout = null){ 
        !isset($layout) &&  $layout =   "default";
        !$this->view    &&  $layout =   "";
        $this->layout   =   $layout;
    }


    public function getViewVars(){
        return $this->viewVars;
    }


    public function getViewInfos(){
        return array(
            "view"      =>  $this->view,
            "layout"    =>  $this->layout
        );
    }

    public function _call_action($action){
        call_user_func(array($this, "pre_action"));
        call_user_func_array(array($this, $action), $this->route["params"]);
        call_user_func(array($this, "post_action"));
    }

    public function redirect($uri){
        http_response_code(302);
        header("Location: " . $uri);
    }

}
