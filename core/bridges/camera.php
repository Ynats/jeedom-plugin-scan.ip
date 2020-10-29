<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_camera {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "camera";
    public static $ip = "ip";
    public static $ipStream = "cameraStreamAccessUrl";
    
    /**
    * getAllElements sert à récupérer les infos des éléments liés au plugin
    *
    * @return array 
    * -> $return[idEquipement]["plugin"] = l'id du Plugin
    * -> $return[idEquipement]["plugin_print"] = Comment afficher l'id du plugin (ex. pour préciser le sous élément d'un plugin)
    * -> $return[idEquipement]["name"] = Nom de l'équipement
    * -> $return[idEquipement]["id"] = Id de l'équipement
    * -> $return[idEquipement]["ip_v4"] = l'ip enregistré au format v4
    */
    public function getAllElements(){

        $eqLogics = eqLogic::byType(self::$plug); 
        
        foreach ($eqLogics as $eqLogic) {    
            $return[$eqLogic->getId()]["plugin"] = self::$plug;
            
            if(!empty($eqLogic->getConfiguration('applyDevice'))){
                $return[$eqLogic->getId()]["plugin_print"] = self::$plug . " :: " . $eqLogic->getConfiguration('applyDevice');
            } else {
                $return[$eqLogic->getId()]["plugin_print"] = self::$plug;
            }
            
            $return[$eqLogic->getId()]["name"] = $eqLogic->getName();
            $return[$eqLogic->getId()]["id"] = $eqLogic->getId();
            $return[$eqLogic->getId()]["ip_v4"] = $eqLogic->getConfiguration(self::$ip);
        }
        return $return;
    }
    
    
    /**
    * majIpElement sert à mettre à jour l'ip de l'élément si celui-ci est différent
    *
    * @param $_ip ip de l'adresse MAC à mettre à jour si différent
    * @param $_id identifiant de l'équipement associé au plugin
    * 
    */
    public function majIpElement($_ip ,$_id){
        
        $record = array(self::$ip => 0, self::$ipStream => 0);
        $eqLogics = eqLogic::byType(self::$plug); 

        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getId() == $_id) { 
                
                if($eqLogic->getConfiguration(self::$ip) != $_ip){
                    $record[self::$ip] = 1;
                    $old_ipStream = $eqLogic->getConfiguration(self::$ipStream); 
                    if(preg_match(scan_ip::getRegex("ip_v4"), $old_ipStream, $match)){ 
                        if(!empty($match[0]) AND $match[0] != $eqLogic->getConfiguration(self::$ip)){
                            log::add('scan_ip', 'debug', "Bridge camera :. L'ip associée à \"URL de snaphot\" est différente de l'ip associée à la caméra \"" . $eqLogic->getName() . "\" et donc ignorée.");
                        } 
                        elseif(!empty($match[0]) AND $match[0] == $eqLogic->getConfiguration(self::$ip)) {
                            $record[self::$ipStream] = 1;
                        }
                    }
                }
                
                if($record[self::$ip] == 1) { 
                    $eqLogic->setConfiguration(self::$ip, $_ip); 
                }
                if($record[self::$ipStream] == 1) {
                    $change_ipStream = preg_replace(scan_ip::getRegex("ip_v4"), $_ip, $old_ipStream);
                    $eqLogic->setConfiguration(self::$ipStream, $change_ipStream);
                }
                if($record[self::$ip] == 1){
                    $eqLogic->save(); 
                    break;
                }
                  
            }
        }
        
    }
    
}
