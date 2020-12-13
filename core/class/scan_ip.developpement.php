<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_dev extends eqLogic {
    
    public static $_modeDeveloppeur = 1;
    public static $_jsonDevEquipement = __DIR__ . "/../../../../plugins/scan_ip/data/json_dev/equipements.json";
    public static $_jsonDevCommentairesEquipement = __DIR__ . "/../../../../plugins/scan_ip/data/json_dev/commentMac.json";
    
    public static function printDebug($_data){
        echo"<pre>";
        var_dump($_data);
        echo"</pre>";
    }
    
    public static function reset(){
        scan_ip_maj::activationCron(0);
        
        config::save('versionPlugin', 1, 'scan_ip');
        
        @unlink(scan_ip::$_jsonEquipement.".json");
        
        $fichier = fopen(scan_ip::$_jsonEquipement.'.json', 'w');
        fputs($fichier, @file_get_contents(self::$_jsonDevEquipement));
        fclose($fichier);

        @unlink(scan_ip::$_jsonCommentairesEquipement.".json");
        
        $fichier = fopen(scan_ip::$_jsonCommentairesEquipement.'.json', 'w');
        fputs($fichier, @file_get_contents(self::$_jsonDevCommentairesEquipement));
        fclose($fichier);
        
        @unlink(scan_ip::$_jsonMapping.".json");
    }

}
