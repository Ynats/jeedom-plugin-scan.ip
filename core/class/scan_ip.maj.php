<?php

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_maj extends eqLogic {
    
    public static $_versionPlugin = 1.1;
    
    public static function pluginAJour(){
        if(self::getVersionPlugin() != self::$_versionPlugin){
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public static function getVersionPlugin(){
        if (config::byKey('versionPlugin', 'scan_ip') == '') {
            config::save('versionPlugin', 1, 'scan_ip');
        }
        return config::byKey('versionPlugin', 'scan_ip');
    }
    
    public static function setVersionPlugin(){
        config::save('versionPlugin', self::$_versionPlugin, 'scan_ip');
    }
    
    public static function printVersionPlugin(){
        if(self::pluginAJour() == FALSE){
            return '<a class="btn btn-warning btn-sm" id="reloadMajPlugin" style="position:relative;top:-5px;"><i class="fas fa-bicycle"></i> Pas à jour encore en v'.self::getVersionPlugin().' (Relancer)</a>';
        } else {
            return "<span style='color:green;'>A jour (".self::getVersionPlugin().")</span>";
        }
    }
    
    public static function printCheckPlugin($_statut = FALSE){
        if($_statut == FALSE){
            return "<span style='color:red;'>Error</span>";
        } else {
            return "<span style='color:green;'>Ok</span>";
        }
    }
    
    public static function majAllEquipement(){
        foreach (scan_ip::byType('scan_ip') as $scan_ip) {
            try {
                $scan_ip->save();
            } catch (Exception $e) { }
        } 
    }
    
    public static function setConfigBase(){
        if (config::byKey('cron_pass', 'scan_ip') == '') {
            config::save('cron_pass', 1, 'scan_ip');
        }
        if (config::byKey('offline_time', 'scan_ip') == '') {
            config::save('offline_time', 4, 'scan_ip');
        }
    }
    
///////////////////////////////////////////////////////////////////////////////
// Release 1.1
    
    public static function majJsonCommentaires_v1_1(){
        
        $commentMac = NULL;
        $arrayCommentMac = scan_ip_json::getJson(scan_ip::$_jsonCommentairesEquipement);
        
        if(empty($arrayCommentMac[0][0]["id"]) and !$arrayCommentMac($_array[0][0]["mac"])){
            foreach ($arrayCommentMac[0] as $tempCommentMac) {
                $commentMac[] = array("id" => scan_ip_tools::getLastMac($value["mac"]), "val" => $value["val"]);
            }
            scan_ip_json::majNetworkCommentaires($commentMac);
        }
 
    }
    
    public static function checkJsonCommentaires_v1_1(){
        $array = scan_ip_json::getJson(scan_ip::$_jsonCommentairesEquipement);
        if(empty($array[0]["mac"])){ return TRUE; }
        elseif(!empty($array[0]["if"]) OR empty($array)){ return TRUE; }
        else{ return FALSE; }
    }
    
    public static function checkJsonEquipements_v1_1(){
        $arrayCommentMac = scan_ip_json::getJson(scan_ip::$_jsonEquipement);
        if($arrayCommentMac == NULL){ return TRUE; }
        elseif(empty($arrayCommentMac[0]["id"])){ return TRUE; }
        else{ return FALSE; }
    }
    
    public static function majMacToMacEnd($_array){ // Pour la 1.1
        $return = array();
        foreach ($_array as $key => $value) {
            if(strlen($key) == 17){
                $return[scan_ip_tools::getLastMac($key)] = $value;
                $return[scan_ip_tools::getLastMac($key)]["mac"] = $key;
            } else {
                $return[$key] = $value;
            }
        }
        return $return;
    }
    
    public static function majMacComToMacEndCom($_array){ // Pour la 1.1

        if(empty($_array[0]["id"]) and !empty($_array[0]["mac"])){
            $return = array();
            foreach ($_array as $value) {
                if(strlen($value["mac"]) == 17){
                    $return[0][] = array("id" => scan_ip_tools::getLastMac($value["mac"]), "val" => $value["val"]);
                } else {
                    $return[0][] = array("id" => $value["mac"], "val" => $value["val"]);
                }
            }
        } else {
            $return = $_array;
        }
        
        return $return;
    }
    
///////////////////////////////////////////////////////////////////////////////
// Release Beta
    
    public static function cleanAfterUpdate($_path = NULL){   
        if($_path == NULL){
             $resources = __DIR__ . "/../../../../plugins/scan_ip/resources/";
        } else {
            $resources = $_path."plugins/scan_ip/resources/";
        }
       
        if(@file_exists($resources . "ieee-oui.txt") == TRUE){
            unlink($resources . "ieee-oui.txt");
        }
        if(@file_exists($resources . "ieee-oui.txt") == TRUE){
            unlink($resources . "ieee-oui.txt.bak");
        }
        if(@file_exists($resources . "ieee-iab.txt") == TRUE){
            unlink($resources . "ieee-iab.txt");
        }
        if(@file_exists($resources . "ieee-iab.txt.bak") == TRUE){
            unlink($resources . "ieee-iab.txt.bak");
        }
        if(@file_exists($resources . "install_scan_ip.sh") == TRUE){
            unlink($resources . "install_scan_ip.sh");
        }
        if(@file_exists($resources . "upload.oui.sh") == TRUE){
            unlink($resources . "upload.oui.sh");
        }
        if(@file_exists($resources . "Autre") == TRUE){
            unlink($resources . "Autre");
        }
        
        if($_path == NULL){
             $bridge = __DIR__ . "/../../../../plugins/scan_ip/core/bridges/";
        } else {
            $bridge = $_path."plugins/scan_ip/core/bridges/";
        }
        if(@file_exists($bridge . "template.php") == TRUE){
            unlink($bridge . "template.php");
        }
        
        if($_path == NULL){
             $json = __DIR__ . "/../../../../plugins/scan_ip/core/json/";
        } else {
            $json = $_path."plugins/scan_ip/core/json/";
        }
        if(@file_exists($json . "macaddress.temp") == TRUE){
            unlink($json . "macaddress.temp");
        }
        if(@file_exists($json . "serialize.temp") == TRUE){
            unlink($json . "serialize.temp");
        }
        if(@file_exists($json . "bridges.json") == TRUE){
            unlink($json . "bridges.json");
        }
        
        if($_path == NULL){
             $subPlugs = __DIR__ . "/../../../../plugins/scan_ip/core/subPlugs";
        } else {
            $subPlugs = $_path."plugins/scan_ip/core/subPlugs/";
        }
        if(@is_dir($subPlugs)){
            if(@file_exists($subPlugs . "/broadlink.php") == TRUE){
                unlink($subPlugs . "/broadlink.php");
            }
            if(@file_exists($subPlugs . "/googlecast.php") == TRUE){
                unlink($subPlugs . "/googlecast.php");
            }
            if(@file_exists($subPlugs . "/template.php") == TRUE){
                unlink($subPlugs . "/template.php");
            }
            if(@file_exists($subPlugs . "/xiaomihome.php") == TRUE){
                unlink($subPlugs . "/xiaomihome.php");
            }
        
            rmdir($subPlugs);
        }
        
    }
    
}
