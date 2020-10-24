<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of xiaomihome
 *
 * @author stany.ferer
 */
class scan_ip_xiaomihome {
    
    public function getElements(){
        
        $eqLogics = eqLogic::byType('xiaomihome', true); 
        var_dump($eqLogics);
        
        $i = 0;
        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getConfiguration('type') == 'yeelight') {
                
                $return[$i]["name"] = $eqLogic->getName();
                $return[$i]["id"] = $eqLogic->getId();
                $return[$i]["ip_v4"] = $eqLogic->getConfiguration('ipwifi');
                $return[$i]["gateway"] = $eqLogic->getConfiguration('gateway');
                
                $eqLogic->setConfiguration('ipwifi', "192.168.0.0");
                $eqLogic->setConfiguration('gateway', "192.168.0.0");
                
            }
        }
        
        var_dump($return);
       
        return $return;
    }
    
}
