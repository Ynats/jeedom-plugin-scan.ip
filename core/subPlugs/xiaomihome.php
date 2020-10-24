<?php

class scan_ip_xiaomihome {
    
    public static $plug = "xiaomihome";
    
    public function getElements(){

        $eqLogics = eqLogic::byType(self::$plug); 
        
        foreach ($eqLogics as $eqLogic) { 

            if ($eqLogic->getConfiguration('type') == 'yeelight' OR $eqLogic->getConfiguration('type') == 'wifi') {
                
                $return[$eqLogic->getId()]["plugin"] = "[" . self::$plug . "] " . $eqLogic->getConfiguration('type');
                $return[$eqLogic->getId()]["name"] = $eqLogic->getName();
                $return[$eqLogic->getId()]["id"] = $eqLogic->getId();
                $return[$eqLogic->getId()]["ip_v4"] = $eqLogic->getConfiguration('ipwifi');
                
            }
        }
        return $return;
    }
    
    public function changeIpElement(){
        
        $eqLogics = eqLogic::byType('xiaomihome'); 
        var_dump($eqLogics);
        
        $i = 0;
        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getConfiguration('type') == 'yeelight') {
                
                $return[$i]["name"] = $eqLogic->getName();
                $return[$i]["id"] = $eqLogic->getId();
                $return[$i]["ip_v4"] = $eqLogic->getConfiguration('ipwifi');
                $return[$i]["gateway"] = $eqLogic->getConfiguration('gateway');
                
                $eqLogic->setConfiguration('ipwifi', "192.168.0.1");
                
                $eqLogic->save();
                
            }
        }
        
        var_dump($return);
       
        return $return;
    }
    
}
