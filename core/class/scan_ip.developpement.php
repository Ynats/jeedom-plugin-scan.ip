<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_dev extends eqLogic {
    
    public static $_fileDev = __DIR__ . "/../../../../plugins/scan_ip/data/dev/modeDev";
    public static $_jsonDevEquipement = __DIR__ . "/../../../../plugins/scan_ip/data/dev/v1/equipements.json";
    public static $_jsonDevCommentairesEquipement = __DIR__ . "/../../../../plugins/scan_ip/data/dev/v1/commentMac.json";
    public static $_jsonDevMapping = __DIR__ . "/../../../../plugins/scan_ip/data/dev/v1/mapping.json";
    
    public static function modeDeveloppeur(){
        if(is_file(self::$_fileDev)){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function printDebug($_data){
        echo"<pre>";
        var_dump($_data);
        echo"</pre>";
    }
    
    public static function reset(){
        shell_exec("sudo chmod 777 -R /var/www/html");
        
        scan_ip_maj::activationCron(0);
        
        config::save('versionPlugin', 1, 'scan_ip');
        
        self::resetAllEqLogics();
        
        self::reset_json(scan_ip::$_jsonEquipement, self::$_jsonDevEquipement);
        self::reset_json(scan_ip::$_jsonCommentairesEquipement, self::$_jsonDevCommentairesEquipement);
        self::reset_json(scan_ip::$_jsonMapping, self::$_jsonDevMapping);
    }
    
    public static function reset_json($_file, $_fileDev){
        @unlink($_file.".json");
        @unlink($_file."_backup_v1.json");
        $fichier = fopen($_file.'.json', 'w');
        fputs($fichier, @file_get_contents($_fileDev));
        fclose($fichier);
    }
    
    public static function resetAllEqLogics(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if(scan_ip_widgets::getWidgetType($scan_ip) == "normal"){
                $scan_ip->setConfiguration('mac_id', NULL);
                $scan_ip->save();
            }
        } 
    }

}
