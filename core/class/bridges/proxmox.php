<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_proxmox {
  
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "proxmox";
    public static $ip = "IP";
    
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
  
        $return[self::$plug]["plugin"] = self::$plug;
        $return[self::$plug]["plugin_print"] = self::$plug;
        $return[self::$plug]["name"] = "Proxmox";
        $return[self::$plug]["id"] = "config";
        
        $value = config::byKey(self::$ip, self::$plug);
        if(preg_match(scan_ip_tools::getRegex("ip_v4"), $value, $match)){
            $return[self::$plug]["ip_v4"] = $match[0];
            return $return;
        } else {
           return NULL; 
        }
    }
    
    
    /**
    * majIpElement sert à mettre à jour l'ip de l'élément si celui-ci est différent
    *
    * @param $_array["ip"] ip de l'adresse MAC à mettre à jour si différent
    * @param $_array["id"] identifiant de l'équipement associé au plugin
    * 
    */
    public function majIpElement($_array){
        
        $old = config::byKey(self::$ip, self::$plug);
        preg_match(scan_ip_tools::getRegex("ip_v4"), $old, $match);
        if($match[0] != $_array["ip"]) { 
            $change_ip = preg_replace(scan_ip_tools::getRegex("ip_v4"), $_array["ip"], $old);
            config::save(self::$ip, $change_ip, self::$plug);
            // Retourne le deamon à lancer
            return NULL;
        }
    }
    
}

