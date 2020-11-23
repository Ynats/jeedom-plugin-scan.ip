<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_core {
  
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "core";
    public static $coreIp = array('Samba Backup IP' => 'updatetab|samba::backup::ip',
                             'Samba Backup Partage' => 'updatetab|samba::backup::share',
                                    'Accès interne' => 'networktab|internalAddr',
                                             'Push' => 'commandtab|cmdPushUrl');
    
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


        $i = 1;
        foreach (self::$coreIp as $name => $ip) {
            $split = explode("|", $ip);
            if(!empty(config::byKey($split[1], self::$plug))){

                $return[self::$plug.$i]["plugin"] = self::$plug;
                $return[self::$plug.$i]["plugin_print"] = self::$plug;
                $return[self::$plug.$i]["name"] = $name;
                $return[self::$plug.$i]["id"] = $ip;
        
                $value = config::byKey($split[1], self::$plug);
                if(preg_match(scan_ip_tools::getRegex("ip_v4"), $value, $match)){
                    $return[self::$plug.$i]["ip_v4"] = $match[0];
                } else {
                    $return[self::$plug.$i]["ip_v4"] =  NULL; 
                }
                $i++;
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

            $old = config::byKey($_array["champ"], self::$plug);
            preg_match(scan_ip_tools::getRegex("ip_v4"), $old, $match);
            if($match[0] != $_array["ip"]) { 
                $change_ip = preg_replace(scan_ip_tools::getRegex("ip_v4"), $_array["ip"], $old);
                config::save($_array["champ"], $change_ip, self::$plug);
                // Retourne le deamon à lancer
                return NULL;
            }
    }
    
}
