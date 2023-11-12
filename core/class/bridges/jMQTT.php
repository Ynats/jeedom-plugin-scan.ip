<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_jmqtt {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "jMQTT";
    public static $ip = "mqttAddress";
    
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

        $return = NULL;
        $eqLogics = eqLogic::byType(self::$plug); 
        
        foreach ($eqLogics as $eqLogic) {  
           if ($eqLogic->getConfiguration(self::$ip) != '')
           {
            $return[$eqLogic->getId()]["plugin"] = self::$plug;
            $return[$eqLogic->getId()]["plugin_print"] = self::$plug;
            $return[$eqLogic->getId()]["name"] = $eqLogic->getName();
            $return[$eqLogic->getId()]["id"] = $eqLogic->getId();
            $return[$eqLogic->getId()]["ip_v4"] = $eqLogic->getConfiguration(self::$ip);
           }
        }
        return $return;
    }
    
    
    /**
    * majIpElement sert à mettre à jour l'ip de l'élément si celui-ci est différent
    *
    * @param $_array["ip"] ip de l'adresse MAC à mettre à jour si différent
    * @param $_array["id"] identifiant de l'équipement associé au plugin
    * 
    */
    public function majIpElement($_array){
        
        $eqLogics = eqLogic::byType(self::$plug); 

        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getId() == $_array["id"]) { 
                if($eqLogic->getConfiguration(self::$ip) != $_array["ip"]){
                    $eqLogic->setConfiguration(self::$ip, $_array["ip"]);
                    $eqLogic->save(); 
                    // Retourne le deamon à lancer
                    return NULL;
                }   
            }
        }
        
    }
    
}