<?php

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_maj extends eqLogic {
    
    public static $_version = 1.1;
    
    public static function MajMacToMacEnd($_array){ // Pour la 1.1
        $return = array();
        foreach ($_array as $key => $value) {
            if(strlen($key) == 17){
                $return[self::getLastMac($key)] = $value;
                $return[self::getLastMac($key)]["mac"] = $key;
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }
    
    public static function MajMacComToMacEndCom($_array){ // Pour la 1.1
        
        $array = $_array[0];
        
        if(empty($array[0]["id"])){
            $return = array();
            foreach ($array as $value) {
                if(strlen($value["mac"]) == 17){
                    $return[0][] = array("id" => self::getLastMac($value["mac"]), "val" => $value["val"]);
                } else {
                    $return[0][] = array("id" => $value["mac"], "val" => $value["val"]);
                }
            }
        } else {
            $return = $_array;
        }
        
        return $return;
    }
    
}
