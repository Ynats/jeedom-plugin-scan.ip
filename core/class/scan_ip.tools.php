<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_tools extends eqLogic {
    
    public static function arrayCompose($_arrayOld = NULL, $_arrayNew){
        if(is_array($_arrayNew)){
            if($_arrayOld == NULL){ 
                return $_arrayNew; 
            } 
            else { 
                return array_merge($_arrayOld, $_arrayNew); 
            }
        } else {
            if(($_arrayOld == NULL OR empty($_arrayOld[0])) AND $_arrayNew != NULL){ 
                $array = array();
                array_push($array, $_arrayNew); 
                return $array;
            } 
            elseif($_arrayNew != NULL){  
                array_push($_arrayOld, $_arrayNew);  
                return array_unique($_arrayOld);
            }
            else {   
                return $_arrayOld;
            }
        } 
    }
    
    public static function lockProcess(){
        if(file_exists(scan_ip::$_file_lock)) {
            if((time() - filemtime (scan_ip::$_file_lock)) > 180){
                self::unlockProcess();
            } else {
               return FALSE; 
            }
        } else {
            fopen(scan_ip::$_file_lock, "w");
            chmod(scan_ip::$_file_lock, 0777);
            return TRUE;
        } 
        return TRUE;
    }
    
    public static function unlockProcess(){
        if(file_exists(scan_ip::$_file_lock)) {
            unlink(scan_ip::$_file_lock);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function getDateFile($_filename){
        if (@file_exists($_filename)) {
            return date("d/m/Y", filemtime($_filename));
        } else {
            return NULL;
        }
        
    }
    
    public static function getRegex($_type){
        if($_type == "ip_v4") { return "/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/"; }
        elseif($_type == "mac") { return "/([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/"; }
        elseif($_type == "iproute2") { return "/\d{1,3}\:\s(.+)\:\s/"; }
        elseif($_type == "net-tools") { return "/(.+)\:\s(flags)/"; }
        elseif($_type == "()") { return "/(\((.*?)\))/"; }
        else { return NULL; }
    }
    
    public static function getPlageIp($_ip){
        list($a, $b, $c) = explode('.', $_ip);
        return $a . "." . $b . "." . $c;
    }
    
    public static function getLastMac($_mac){
        list($a, $b, $c, $d, $e, $f) = explode(':', $_mac);
        return $d . ":" . $e . ":" . $f;
    }
    
    public static function isOffline($_expire = NULL, $_time){
        if($_expire == NULL){ $_expire = 4; }
        $expire = time() - (60 * $_expire);
        if($expire <= $_time){ return 0; } 
        else { return 1; }
    }
    
    public static function excludeSubReseau($_string){
        if($_string == ""){ return FALSE; }
        if($_string == "lo"){ return FALSE; }
        elseif(preg_match('/(tun)[0-9]*/', $_string)){ return FALSE; }
        else { return TRUE; }
    }
    
    public static function vueSubTitle($_titre, $_from = "devices"){
        
        if($_from == "devices"){ $col1 = "col-sm-3"; $col2 = "col-sm-5"; $margin = "20px 0"; } 
        elseif ($_from == "config"){ $col1 = "col-sm-4"; $col2 = "col-sm-5"; $margin = "10px 0"; } 
        
        echo '  <div class="form-group">
                    <div class="'.$col1.'"></div>
                    <div class="'.$col2.'">
                           <div style="background-color: #039be5; padding: 2px 5px; color: white; margin: '.$margin.'; font-weight: bold;">'. $_titre .'</div>
                    </div>
                </div>';
    }
    
    public static function getCycle($_width, $_color){
        $return = '<div style="margin: 0;">';
        $return .= '<div style="width: '.$_width.'; height: '.$_width.'; border-radius: '.$_width.'; background: '.$_color.';"></div>';
        $return .= '</div>';
        return $return;
    }
    
    public static function printArray($_array){
        echo "<pre style='background-color: #1b2426 !important; color: white !important;'>".print_r($_array, true)."</pre>";
    }
    
    public static function printFileOuiExist(){
        if(@file_exists(scan_ip::$_file_oui) == TRUE){
            return "<span style='color:green'>oui.txt (Installé)</span>";
        } else {
            return '<a class="btn btn-danger btn-sm" onclick= "recordBtMac()" style="position:relative;top:-5px;"><i class="fas fa-paperclip"></i> oui.txt (Manquant)</a>';
        }
    }
    
    public static function printFileIatExist(){
        if(@file_exists(scan_ip::$_file_iab) == TRUE){
            return "<span style='color:green'>iab.txt (Installé)</span>";
        } else {
            return '<a class="btn btn-danger btn-sm" onclick= "recordBtMac()" style="position:relative;top:-5px;"><i class="fas fa-paperclip"></i> iab.txt (Manquant)</a>';
        }
    }
    
    public static function getCleanForSortTable($_string, $_type = NULL){
        if (preg_match(self::getRegex("ip_v4"), $_string)) { 
            return str_replace(".", "", $_string);
        } 
        if($_string == "..."){
            if($_type == "int"){
                return 9999999999999;
            } elseif($_type == "string"){
                return "ZZZZZZZZZZZZZZZ";
            } elseif($_type == "date"){
                return 0;
            } else{
                return 9999999999999;
            }
        }
        else {
            return strtolower($_string);
        }
    }
    
    public static function cleanArrayEquipement($_array){
        $return = NULL;
        foreach ($_array as $mac => $scanLine) {
            if(!empty($scanLine["equipement"]) AND !empty($mac)){
                $return[$mac] = $scanLine;
            }
        }
        return $return;
    }
    
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
    
    public static function showEquCadence() {
        if (scan_ip::getConfigMode() == "normal") {
            echo " style='display:none;'";
        } else {
            echo "";
        }
    }
    
    public static function printInputSubConfig(){
        log::add('scan_ip', 'debug', 'printInputSubConfig :. Lancement');
        $return = "";
        
        $allReseau = scan_ip_shell::scanSubReseau();
        unset($allReseau["name_plage_route"]);
        
        foreach ($allReseau as $sub) {
            if(self::excludeSubReseau($sub["name"]) == TRUE) {
                $return .= '<div class="form-group"">';
                $return .= '<label class="col-sm-4 control-label">{{Scanner le sous-réseau ['.$sub["name"].']}} </label>';
                $return .= '<div class="col-sm-2">';
                $return .= '<input type="checkbox" class="configKey" data-l1key="sub_enable_'.md5($sub["name"]).'" style="border: 1px solid var(--link-color) !important;"><span style="font-weight: bold;">'.$sub["ip_v4"].'</span>';
                $return .= '</div>';
                $return .= '</div>';
            }
        }
        return $return;
    }
    
    public static function printDate($_time = NULL){
        if($_time == NULL OR $_time == "..."){
            return "...";
        } else {
             return date("d/m/Y H:i:s", $_time);
        } 
    }
    
    public static function getInstallJeedom(){
        
        $test1 = __DIR__ . "/../../../../robots.txt";
        $test2 = __DIR__ . "/../../../../apple-touch-icon.png";
        $test3 = __DIR__ . "/../../../../COPYRING";
        
        if (file_exists($test1)) { $return[] = filemtime($test1); }
        if (file_exists($test2)) { $return[] = filemtime($test2); }
        if (file_exists($test3)) { $return[] = filemtime($test3); }
        
        asort($return);
        return $return[0];
    }

}
