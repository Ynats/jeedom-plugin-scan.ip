<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_networks {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "networks";
    public static $ip = "ip";
    public static $ipWoL = "broadcastIP";
    
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
            $return[$eqLogic->getId().self::$ip]["plugin"] = self::$plug;
            $return[$eqLogic->getId().self::$ip]["plugin_print"] = self::$plug . " :: ip";
            $return[$eqLogic->getId().self::$ip]["name"] = $eqLogic->getName();
            $return[$eqLogic->getId().self::$ip]["id"] = $eqLogic->getId();
            $return[$eqLogic->getId().self::$ip]["ip_v4"] = $eqLogic->getConfiguration(self::$ip);
            $return[$eqLogic->getId().self::$ip]["champ"] = self::$ip;
            
            $return[$eqLogic->getId().self::$ipWoL]["plugin"] = self::$plug;
            $return[$eqLogic->getId().self::$ipWoL]["plugin_print"] = self::$plug . " :: ip WoL";
            $return[$eqLogic->getId().self::$ipWoL]["name"] = $eqLogic->getName();
            $return[$eqLogic->getId().self::$ipWoL]["id"] = $eqLogic->getId();
            $return[$eqLogic->getId().self::$ipWoL]["ip_v4"] = $eqLogic->getConfiguration(self::$ipWoL);
            $return[$eqLogic->getId().self::$ipWoL]["champ"] = self::$ipWoL;
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
    public function majIpElement($_ip ,$_id, $_champ = NULL){

        $eqLogics = eqLogic::byType(self::$plug); 

        foreach ($eqLogics as $eqLogic) {
            if ($eqLogic->getId() == $_id) { 
                if($eqLogic->getConfiguration($_champ) != $_ip){
                    $eqLogic->setConfiguration($_champ, $_ip);
                    $eqLogic->save(); 
                    // Retourne le deamon à lancer
                    return NULL;
                }   
            }
        }
        
    }
    
}