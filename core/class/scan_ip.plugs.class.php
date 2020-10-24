<?php


class scan_ip_plugs {
    
    public function allPlugs(){
        $plugs = array(
            "xiaomihome",
            );
        return $plugs;
    }
    
    public function getPlugsElements($plugs){
        require(__DIR__ . "/../../../../plugins/scan_ip/core/subPlugs/".$plugs.".php");
        $class = "scan_ip_".$plugs;
        ${$plugs} = new $class;
        return ${$plugs}->getElements();
    }
    
    public function getElements(){
        $return = array();
        foreach ($this->allPlugs() as $plugs) {
            $return = array_merge($return, $this->getPlugsElements($plugs));
        }
        return $return;
    }
}
