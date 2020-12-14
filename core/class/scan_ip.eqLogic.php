<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_eqLogic extends eqLogic {
    
    public static function getAlleqLogics(){
        log::add('scan_ip', 'debug', 'getAlleqLogics :. Lancement');
        $eqLogics = eqLogic::byType('scan_ip');
        
        foreach ($eqLogics as $scan_ip) {
            $macId = $scan_ip->getConfiguration('mac_id');
            $return[$macId]["name"] = $scan_ip->name;
            $return[$macId]["enable"] = $scan_ip->getIsEnable();
            $return[$macId]["offline_time"] = $scan_ip->getConfiguration("offline_time", scan_ip::$_defaut_offline_time);
            log::add('scan_ip', 'debug', 'getAlleqLogics :. "' . $scan_ip->name .'"');
        } 
        return $return;
    }
    
    public static function searcheqLogicByType($_type){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if($_type == "network" and $scan_ip->getConfiguration('type_widget') == "network"){
                return $scan_ip;
            } elseif($_type == "new_equipement" and $scan_ip->getConfiguration('type_widget') == "new_equipement"){
                return $scan_ip;
            }
        } 
        return NULL;
    }
    
    public static function addEquipementsTab($_array){ 
        foreach ($_array as $equ) {
            self::createElement($equ[0]["mac"]);
        }
    }
    
    public static function createElement($_mac) { 
        $eqLogic = new scan_ip();
        $eqLogic->setEqType_name("scan_ip");
        $eqLogic->setIsEnable(0);
        $eqLogic->setIsVisible(1);
        $eqLogic->setName($_mac);
        $eqLogic->setConfiguration('adress_mac', $_mac);
        $eqLogic->setConfiguration('mac_id', scan_ip_tools::getLastMac($_mac));
        $eqLogic->save();
    }
    
    public static function showEquipements(){
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'showEquipements :. Lancement');
        
        $allEquipementsBridges = scan_ip_bridges::bridges_getEquiementsById();
        
        $return = NULL;
        $a = 0;
        
        $eqLogics = eqLogic::byType('scan_ip');
        $bridge = scan_ip_bridges::bridges_getElements();
        
        foreach ($eqLogics as $scan_ip) {
            
            if(scan_ip_widgets::getWidgetType($scan_ip) == "normal"){

                if(!empty($scan_ip->getConfiguration('mac_id'))){
                    $return[$a]["mac_id"] = $scan_ip->getConfiguration('mac_id');
                    $return[$a]["mac"] = scan_ip_json::getMac($return[$a]["mac_id"]);
                } else {
                   $return[$a]["mac_id"] = NULL; 
                   $return[$a]["mac"] = "...";
                }
                
                $return[$a]["plug_element_plugin"] = "";
                $return[$a]["name"] = $scan_ip->name;
                $return[$a]["link"] = "<a href='/index.php?v=d&m=scan_ip&p=scan_ip&id=".$scan_ip->getId()."'>".$scan_ip->name."</a>";
                
                if(!empty(scan_ip_cmd::getCommande('ip_v4', $scan_ip))){
                    $return[$a]["ip_v4"] = scan_ip_cmd::getCommande('ip_v4', $scan_ip);
                } else {
                    $return[$a]["ip_v4"] = "...";
                }
                
                if(!empty(scan_ip_cmd::getCommande('ip_v4', $scan_ip))){
                    $return[$a]["ip_v4"] = scan_ip_cmd::getCommande('ip_v4', $scan_ip);
                } else {
                    $return[$a]["ip_v4"] = "...";
                }
                
                if(!empty(scan_ip_cmd::getCommande('update_date', $scan_ip))){
                    $return[$a]["update_date"] = scan_ip_cmd::getCommande('update_date', $scan_ip);
                } else {
                    $return[$a]["update_date"] = "...";
                }
                
                if(!empty(scan_ip_cmd::getCommande('on_line', $scan_ip))){
                    $return[$a]["on_line"] = scan_ip_cmd::getCommande('on_line', $scan_ip);
                } else {
                    $return[$a]["on_line"] = 0;
                }

                if($bridge != FALSE){
                    for ($index = 1; $index <= scan_ip_bridges::$_defaut_bridges_by_equipement; $index++) {
                        if(!empty($scan_ip->getConfiguration("plug_element_plugin_".$index))){
                            $split = explode("|", $scan_ip->getConfiguration("plug_element_plugin_".$index));
                            if(!empty($allEquipementsPlugs[$split[1]]["name"])){
                                $equipement = $allEquipementsPlugs[$split[1]]["name"];
                            } else {
                                $equipement = "";
                            }
                            if($split[0] == 'core') {
                                $return[$a]["plug_element_plugin"] .= "<div><a href='index.php?v=d&p=administration#".$split[1]."' target='_blank'>#".$split[2]." (".$split[0].") ".$equipement."</a></div>";
                            } else {
                                $return[$a]["plug_element_plugin"] .= "<div><a href='/index.php?v=d&m=".$split[0]."&p=".$split[0]."&id=".$split[1]."' target='_blank'>#".$split[1]." (".$split[0].") ".$equipement."</a></div>";
                            }
                        } 
                    }
                }
                $a++;
            }
        }  
       return $return;
    }
    
    public static function getEquipementsbyId(){
        $eqLogics = eqLogic::byType('scan_ip');
        $return = NULL;
        foreach ($eqLogics as $scan_ip) {
            if(scan_ip_widgets::getWidgetType($scan_ip) == "normal"){
                $return[] = $scan_ip->getConfiguration("mac_id"); 
            }
        }
        return $return;
    }
    
    public static function hideSelect(){
        if(config::byKey('add_compatibility_safari', 'scan_ip', 0) == 0){
            echo "hideSelect";
        } else {
            echo "hideSelectSafari";
        }
    }

}

