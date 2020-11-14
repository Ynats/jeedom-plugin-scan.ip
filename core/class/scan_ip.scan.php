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
        
        // Si json pas au bon endroit
        if(@is_file(__DIR__ . "/../../../../plugins/scan_ip/core/json/mapping.json")){
            shell_exec("sudo mv " . __DIR__ . "/../../../../plugins/scan_ip/core/json/*.json " . __DIR__ . "/../../../../plugins/scan_ip/data/json");
        }
        
        $lastScan = self::scanReseau();
        
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                log::add('scan_ip', 'debug', 'syncScanIp :. cmdRefresh('.$scan_ip->getId().')');
                scan_ip_cmd::cmdRefresh($scan_ip, $lastScan);
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

        if($subReseau["subReseauEnable"] > 0) {
            $new = array();
            foreach ($subReseau["subReseau"] as $sub) { 
                if($sub["enable"] == 1){
                    $scanResult = scan_ip_shell::arpScanShell($sub["name"]); 
                    $new = scan_ip_tools::arrayCompose($new, $scanResult);
                }
            }
        } else {
            $new = scan_ip_shell::arpScanShell();
        }

        if(count($new) == 0){
            log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
            exit();
        } 
        else {
            
            
            $old = scan_ip_json::getJson(scan_ip::$_jsonMapping);
            
            if(empty($old) OR count($old) == 0){ $now = $new; } 
            else { $now = scan_ip_tools::arrayCompose($old, $new); } 
            
            $now = scan_ip_tools::cleanArrayEquipement($now);
            scan_ip_json::createJsonFile(scan_ip::$_jsonEquipement, $now); 
            
            $nowMapping = self::createJsonMapping($now, $ipRoute);
            
        }
        
        $nowMapping["jeedom"] = $infoJeedom; 
        $nowMapping["infos"]["version_arp"] = scan_ip_shell::arpVersion();
        $nowMapping["infos"]["time"] = time();
        $nowMapping["infos"]["date"] = date("d/m/Y H:i:s", $nowMapping["infos"]["time"]);

        scan_ip_json::recordInJson(scan_ip::$_jsonMapping, $nowMapping);
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $now["infos"]["version_arp"] . ']');
        log::add('scan_ip', 'debug', "////////////////////////////////////////////////////////////////////");
        
        return $now;
    }
    
    public static function createJsonMapping($_now, $_ipRoute){
        
        $timeNow = time();
        $_return = array();
            
            foreach ($_now as $mac => $scanLine) {
                if($scanLine["ip_v4"] == $_ipRoute){
                    $_return["route"]["ip_v4"] = $scanLine["ip_v4"];
                    $_return["route"]["mac"] = $mac;
                } else {
                    if(empty($old["byMac"][$mac]["record"])){
                        $create = $timeNow;
                    } else {
                        $create = $old["byMac"][$mac]["record"];
                    }
                    $_return["sort"][explode(".",$scanLine["ip_v4"])[3]] = array(
                            "record" => $create,
                            "ip_v4" => $scanLine["ip_v4"], 
                            "mac" => $mac, 
                            "time" => $scanLine["time"], 
                            "equipement" => $scanLine["equipement"]
                    );
                    $_return["byIpv4"][$scanLine["ip_v4"]] = array(
                        "mac" => $mac, 
                        "equipement" => $scanLine["equipement"], 
                        "time" => $scanLine["time"], 
                        "record" => $create
                        );
                    $_return["byMac"][$mac] = array(
                        "ip_v4" => $scanLine["ip_v4"], 
                        "equipement" => $scanLine["equipement"], 
                        "time" => $scanLine["time"], 
                        "record" => $create
                        );  
                    $_return["byTime"][$scanLine["time"].$create][] = array(
                        "time" => $scanLine["time"], 
                        "mac" => $mac, "ip_v4" => $scanLine["ip_v4"], 
                        "equipement" => $scanLine["equipement"], 
                        "record" => $create
                        );
                }
            }

            ksort($_return["sort"]);
            krsort($_return["byTime"]);
        
        return $_return;
    }
    
}
