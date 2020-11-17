<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_synologyapi {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "synologyapi";
    public static $name = "_name";
    public static $ip = "_server";

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
        
        for($i = 1; $i <= 3; $i++){
             if(!empty(config::byKey("Syno".$i.self::$ip, self::$plug))) {

                $return[self::$plug.$i]["plugin"] = self::$plug;
                $return[self::$plug.$i]["plugin_print"] = self::$plug;
                if(empty(config::byKey("Syno".$i.self::$ip, self::$plug))) {
                    $return[self::$plug.$i]["name"] = "Syno".$i;
                } else {
                    $return[self::$plug.$i]["name"] = config::byKey("Syno".$i.self::$name, self::$plug);
                }
                $return[self::$plug.$i]["id"] = "Syno".$i.self::$ip;
                
                $value = config::byKey("Syno".$i.self::$ip, self::$plug);
                if(preg_match(scan_ip_tools::getRegex("ip_v4"), $value, $match)){
                    $return[self::$plug.$i]["ip_v4"] = $match[0];
                } else {
                   $return[self::$plug.$i]["ip_v4"] = NULL;
                }
            }
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
       
        $old = config::byKey($_id, self::$plug);
        preg_match(scan_ip_tools::getRegex("ip_v4"), $old, $match);
        if($match[0] != $_ip) { 
            $change_ip = preg_replace(scan_ip_tools::getRegex("ip_v4"), $_ip, $old);
            config::save($_id, $change_ip, self::$plug);
            // Retourne le deamon à lancer
            return NULL;
        }
        
    }
    
}