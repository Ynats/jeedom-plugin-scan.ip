<?php

/**
* le nom de la class doit commencer par "scan_ip_" et se poursuivre par le nom du plugin
*/
class scan_ip_Abeille {
    
    /**
    * Nom du Plugin correspondant au nom du fichier présent dans core/bridges/*****.php
    * Nom de la variable ip à modifier
    */
    public static $plug = "Abeille";
    public static $ip = "IpWifiZigate";
    
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
        $zigateNb = config::byKey('zigateNb', self::$plug);

        for($i = 1; $i <= $zigateNb; $i++){
            if(config::byKey('AbeilleType'.$i, self::$plug) == "WIFI" AND !empty(config::byKey(self::$ip.$i, self::$plug))){
                $return[self::$plug.$i]["plugin"] = self::$plug;
                $return[self::$plug.$i]["plugin_print"] = self::$plug;
                $return[self::$plug.$i]["name"] =  "Zigate".$i;
                $return[self::$plug.$i]["id"] = self::$ip.$i;
                
                $value = config::byKey(self::$ip.$i, self::$plug);
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
    public function majIpElement($_ip ,$_id, $_champ = NULL){
        
        $old = config::byKey($_id, self::$plug);
        preg_match(scan_ip_tools::getRegex("ip_v4"), $old, $match);
        if($match[0] != $_ip) { 
            $change_ip = preg_replace(scan_ip_tools::getRegex("ip_v4"), $_ip, $old);
            config::save($_id, $change_ip, self::$plug);
            // Retourne le deamon à lancer
            return self::$plug;
        }
        
    }
    
}
