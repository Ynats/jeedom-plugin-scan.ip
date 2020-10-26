<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_xiaomihome {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/subPlugs/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "idplugin";
    public static $ip = "ip";
    
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
            $return[$eqLogic->getId()]["plugin_print"] = self::$plug . " :: " . $eqLogic->getConfiguration('type');
            $return[$eqLogic->getId()]["name"] = $eqLogic->getName();
            $return[$eqLogic->getId()]["id"] = $eqLogic->getId();
            $return[$eqLogic->getId()]["ip_v4"] = $eqLogic->getConfiguration(self::$ip);
        }
        return $return;
    }
    
    
    /**
    * getIpElement sert à récupérer l'ip d'un élément du plugin par son id
    *
    * @param $_id de l'équipement
    * 
    * 
    * @return string ip de l'équipement au format v4
    */
    public function getIpElement($_id){
        
        $eqLogics = eqLogic::byType(self::$plug); 

        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getId() == $_id) {  
                return $eqLogic->getConfiguration(self::$ip);
                break;
            }
        }
        
    }
    
    /**
    * majIpElement sert à mettre à jour l'ip de l'élément si celui-ci est différent
    *
    * @param $_ip ip de l'adresse MAC à mettre à jour si différent
    * @param $_id identifiant de l'équipement associé au plugin
    * 
    */
    public function majIpElement($_ip ,$_id){
        
        $eqLogics = eqLogic::byType(self::$plug); 

        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getId() == $_id) { 
                if($eqLogic->getConfiguration(self::$ip) != $_ip){
                    $eqLogic->setConfiguration(self::$ip, $_ip);
                    $eqLogic->save(); 
                    break;
                }   
            }
        }
        
    }
    
}
