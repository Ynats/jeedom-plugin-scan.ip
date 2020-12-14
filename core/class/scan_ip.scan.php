<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_scan extends eqLogic {

    public static function syncScanIp(){
        log::add('scan_ip', 'debug', '////////////////////////////////////////////////////////////////////');
        log::add('scan_ip', 'debug', 'syncScanIp :. Lancement du scan du réseau');
        
        if(scan_ip_maj::checkPluginVersionAJour() == FALSE AND scan_ip_dev::$_modeDeveloppeur == 0){
            $texteError = "Incident de mise à jour : Synchronisation annulée car les données ne sont pas au format de la version installée. Allez dans la configuration du plugin et activez le mode debug pour corriger le problème.";
            log::add('scan_ip', 'info', $texteError);
            scan_ip_tools::unlockProcess();
            ajax::error($texteError);
            return "error";
        }
        
        // Si json pas au bon endroit
        if(@is_file(__DIR__ . "/../../../../plugins/scan_ip/core/json/mapping.json")){
            shell_exec("sudo mv " . __DIR__ . "/../../../../plugins/scan_ip/core/json/*.json " . __DIR__ . "/../../../../plugins/scan_ip/data/json");
        }
        
        self::scanReseau();
        
        $jsonMapping = scan_ip_json::getJson(scan_ip::$_jsonMapping);
        
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                log::add('scan_ip', 'debug', 'syncScanIp :. cmdRefresh('.$scan_ip->getId().')');
                scan_ip_cmd::cmdRefresh($scan_ip, $jsonMapping);
            }
        }  
        
        log::add('scan_ip', 'debug', 'syncScanIp :. Fin du scan du réseau');
        log::add('scan_ip', 'debug', '////////////////////////////////////////////////////////////////////');
    }
       
    public static function scanReseau(){
        log::add('scan_ip', 'debug', "////////////////////////////////////////////////////////////////////");
        log::add('scan_ip', 'debug', 'scanReseau :. Lancement');
        
        $ipRoute = scan_ip_shell::getIpRoute();  
        $subReseau = scan_ip_shell::getSubReseauEnable($ipRoute);  
        $infoJeedom = scan_ip_shell::getInfoJeedom($ipRoute);
        
        if($subReseau["subReseauEnable"] == 0) {
            config::save("sub_enable_".md5($subReseau["name_plage_route"]), 1, 'scan_ip');
            $subReseau = scan_ip_shell::getSubReseauEnable($ipRoute);
        }

        $new = array();
        foreach ($subReseau["subReseau"] as $sub) { 
            if($sub["enable"] == 1){
                $scanResult = scan_ip_shell::arpScanShell($sub["name"]);
                $new = scan_ip_tools::arrayCompose($new, $scanResult);
            }
        }
        
        if(count($new) == 0){            
            log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
            event::add('jeedom::alert', array(
                'level' => 'warning',
                'page' => 'scan_ip',
                'message' => "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations."
            )); 
        } 
        else {
            
            if(config::byKey('add_network_jeedom', 'scan_ip', 0) == 1){
                $new = scan_ip_tools::arrayCompose($new, self::createEquipementJeedom($infoJeedom));
            } 
            
            $equipement = self::createArchiveEquipement($new);
            $now = scan_ip_tools::arrayCompose($equipement, $new);       
            $now = scan_ip_tools::cleanArrayEquipement($now); 
            $nowMapping = self::createJsonMapping($now, $equipement, $ipRoute, $infoJeedom); 
        }
        
        $nowMapping["jeedom"] = $infoJeedom; 
        $nowMapping["infos"]["version_arp"] = scan_ip_shell::arpVersion();
        $nowMapping["infos"]["time"] = time();
        $nowMapping["infos"]["date"] = date("d/m/Y H:i:s", $nowMapping["infos"]["time"]);

        scan_ip_json::recordInJson(scan_ip::$_jsonMapping, $nowMapping);
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $nowMapping["infos"]["version_arp"] . ']');
        log::add('scan_ip', 'debug', "////////////////////////////////////////////////////////////////////");
        
    }
    
    public static function createEquipementJeedom($_infoJeedom){
        $return[scan_ip_tools::getLastMac($_infoJeedom["mac"])] = array(
            "mac" => $_infoJeedom["mac"],
            "equipement" =>$_infoJeedom["name"],
            "ip_v4" => $_infoJeedom["ip_v4"],
            "time" => time(),
        );
        
        return $return;
    }
    
    public static function createArchiveEquipement($_new){
        
        $old = scan_ip_json::getJson(scan_ip::$_jsonEquipement);

        if($old != NULL){
            
            foreach (array_merge($_new, $old) as $macId => $scanLine) {
                
                if(empty($scanLine["record"]) OR $scanLine["record"] == ""){ $return[$macId]["record"] = time(); } 
                else { $return[$macId]["record"] = $old[$macId]["record"]; }
                
                if(empty($old[$macId]["equipement"]) AND empty($_new[$macId]["equipement"]) OR ((!empty($old[$macId]["equipement"]) AND $old[$macId]["equipement"] == "...") AND (!empty($_new[$macId]["equipement"]) AND $_new[$macId]["equipement"] == "..."))){
                    $return[$macId]["equipement"] = scan_ip_api_mac_vendor::get_MacVendor($macId);
                }
                elseif(!empty($_new[$macId]["equipement"]) and $_new[$macId]["equipement"] != "..."){
                    $return[$macId]["equipement"] = $_new[$macId]["equipement"];
                }
                else {
                    $return[$macId]["equipement"] = $old[$macId]["equipement"]; 
                }   
                
                if(!empty($_new[$macId]["ip_v4"])){ $return[$macId]["ip_v4"] = $_new[$macId]["ip_v4"]; } 
                elseif(!empty($old[$macId]["ip_v4"])){ $return[$macId]["ip_v4"] = $old[$macId]["ip_v4"]; }
                else { $return[$macId]["ip_v4"] = "..."; }

                if(!empty($_new[$macId]["time"])){ $return[$macId]["time"] = $_new[$macId]["time"]; } 
                elseif(!empty($old[$macId]["time"])){ $return[$macId]["time"] = $old[$macId]["time"]; }
                else { $return[$macId]["time"] = "..."; }
                
                if(!empty($_new[$macId]["mac"])){ $return[$macId]["mac"] = $_new[$macId]["mac"]; } 
                elseif(!empty($old[$macId]["mac"])){ $return[$macId]["mac"] = $old[$macId]["mac"]; }
                else { $return[$macId]["mac"] = "..."; }

            }
        } else {
            foreach ($_new as $macId => $scanLine) {
                $return[$macId]["record"] = time();
                $return[$macId]["equipement"] = $scanLine["equipement"];
            }
        }
        
        scan_ip_json::createJsonFile(scan_ip::$_jsonEquipement, $return);
        
        return $return;
    }
    
    public static function createJsonMapping($_now, $_equipement, $_ipRoute, $_infoJeedom){
        
        $timeNow = time();
        $_return = array();
        $add_network_routeur = config::byKey('add_network_routeur', 'scan_ip', 0);
          
        foreach ($_now as $mac_id => $scanLine) {

            if($scanLine["ip_v4"] == $_ipRoute){
                $_return["route"]["ip_v4"] = $scanLine["ip_v4"];
                $_return["route"]["mac"] = $scanLine["mac"];
                $_return["route"]["equipement"] = $scanLine["equipement"];
            } 
                
            if($add_network_routeur == 1 OR $scanLine["ip_v4"] != $_ipRoute){
                
                $macEnd = scan_ip_tools::getLastMac($scanLine["mac"]);

                if(!empty($_equipement[$mac_id]["record"])){
                    $record = $_equipement[$mac_id]["record"];
                } else {
                    $record = $_infoJeedom["record"];
                }

                $_return["sort"][] = array(
                    "record" => $record,
                    "ip_v4" => $scanLine["ip_v4"], 
                    "mac" => $scanLine["mac"], 
                    "mac_id" => $mac_id,
                    "time" => $scanLine["time"], 
                    "equipement" => $scanLine["equipement"]
                );
                
                $_return["byIpv4"][$scanLine["ip_v4"]] = array(
                    "mac" => $scanLine["mac"], 
                    "mac_id" => $mac_id,
                    "equipement" => $scanLine["equipement"], 
                    "time" => $scanLine["time"], 
                    "record" => $record
                    );
                
                $_return["byId"][$mac_id] = array(
                    "mac" => $scanLine["mac"],
                    "ip_v4" => $scanLine["ip_v4"], 
                    "equipement" => $scanLine["equipement"], 
                    "time" => $scanLine["time"], 
                    "record" => $record
                    ); 
                
                $_return["byTime"][$scanLine["time"].$record][] = array(
                    "time" => $scanLine["time"], 
                    "mac" => $scanLine["mac"], 
                    "mac_id" => $mac_id,
                    "ip_v4" => $scanLine["ip_v4"],
                    "equipement" => $scanLine["equipement"], 
                    "record" => $record
                    );
            }
        }

        ksort($_return["sort"]);
        krsort($_return["byTime"]);
        
        return $_return;
    }
    
}
