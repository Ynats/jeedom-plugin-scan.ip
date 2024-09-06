<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_widget_network extends eqLogic {
    
    public static function getWidgetNetwork(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if(scan_ip_widgets::getWidgetType($scan_ip) == 'network') {
                $exist = 1;
                break;
            }
        }
        
        if($exist == 0){
            $scan_ip = new scan_ip();
            $scan_ip->setEqType_name("scan_ip");
            $scan_ip->setIsEnable(1);
            if(config::byKey('widget_network', 'scan_ip', '1') == 1) { 
                $scan_ip->setIsVisible(1); 
                $scan_ip->setIsEnable(1);
            } else { 
                $scan_ip->setIsVisible(0); 
                $scan_ip->setIsEnable(0);
            }
            $scan_ip->setDisplay('height',"570px");
            $scan_ip->setDisplay('width',"1130px");
            $scan_ip->setName("Scan.Ip Widget Network");
            $scan_ip->setConfiguration('type_widget', 'network');
            $scan_ip->setConfiguration('saveOrderColonWidegetNetwork', 'ip');
            $scan_ip->save();
        }
        
        return $scan_ip;
    }
    
    public static function setCmdWidgetNetwork($_scanIp){
        
//        $refresh = $_scanIp->getCmd(null, 'refresh');
//        if (!is_object($refresh)) {
//            $refresh = new scan_ipCmd();
//            $refresh->setName(__('Rafraichir', __FILE__));
//        }
//        $refresh->setEqLogic_id($_scanIp->getId());
//        $refresh->setLogicalId('refresh');
//        $refresh->setType('action');
//        $refresh->setSubType('other');
//        $refresh->save();
        
    }
    
    public static function createNetworkWidget($_this, $_version = 'dashboard', $_replace, $_reseau) {

        log::add('scan_ip', 'debug', 'createNetworkWidget :.  Lancement');

        $replace = $_replace;
        $savingMac = scan_ip_eqLogic::getAlleqLogics();
        $commentMac = scan_ip_json::getCommentaires();

        $replace["#widget_network#"] = '<table style="width: 100%; margin: -5px -5px 22px 0;" id="scan_ip_network_widget">
        <thead>
            <tr style="background-color: grey !important; color: white !important;">
                <th data-sort="string" style="padding : 3px 0 3px 15px !important; text-align: center;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b></span></th>
                <th data-sort="int" style="text-align: center; padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b></span></th>
                <th data-sort="string" style="padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Adresse MAC</span></th>
                <th data-sort="int" style="padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Ip</span></th>
                <th data-sort="string" style="padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Nom</span></th>
                <th data-sort="string" style="padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Commentaire</span></th>
                <th data-sort="int" style="padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Date de mise à jour</span></th>
            </tr>
        </thead>
        <tbody>';

        foreach ($_reseau["sort"] as $device) {

            $element = self::getElementVueNetwork($device, $savingMac, $commentMac);

            $replace["#widget_network#"] .= '<tr>'
            . '<td style="padding : 3px 0 3px 15px !important;" title="' . $element["titleOnLine"] .'"><span style="display:none;">' . $element["lineSortOnline"] . '</span>' . scan_ip_tools::getCycle("15px", $element["colorOnLine"]) . '</td>'
            . '<td style="text-align:center !important; padding : 3px 0 3px 15px !important;' . $element["classPresent"] . '" title="' . $element["titleEquipement"] .'"><span style="display:none;">' . $element["lineSortEquipement"] . '</span><span class="' . $element["classSuivi"] . '">' . $element["textPresent"] . '</span></td>'
            . '<td style="padding : 3px 0 3px 15px !important;">' . $device["mac"] . '</td>'
            . '<td style="padding : 3px 0 3px 15px !important;"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($device["ip_v4"], "int") . '</span>' . $device["ip_v4"] . '</td>'
            . '<td style="text-overflow: ellipsis;padding : 3px 0 3px 15px !important;"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($element["name"], "string") . '</span>' . $element["name"] . '</td>'
            . '<td style="padding : 3px 0 3px 15px !important;"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($element["printComment"], "string") . '</span>' . $element["printComment"] . '</td>'
            . '<td style="padding : 3px 0 3px 15px !important;"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($device["time"], "date") . '</span>' . scan_ip_tools::printDate($device["time"]) . '</td>'
            . '</tr>';

        }

        $replace["#widget_network#"] .= '</tbody></table>';
        $replace["#widget_network#"] .= '<script src="plugins/scan_ip/3rdparty/stupidtable.min.js"/></script>';
        $replace["#widget_network#"] .= '<script>$(document).ready(function ($) { var $table = $("#scan_ip_network_widget").stupidtable(); var $th_to_sort = $table.find("thead th").eq('.self::getOrderBy(self::getConfigOrder($_this)).'); $th_to_sort.stupidsort(); });</script>';
        
        return $replace;
    }
    
    public static function getConfigOrder($_this){
        $return = $_this->getConfiguration("saveOrderColonWidegetNetwork");
        if(empty($return) OR $return == ""){
            return "ip";
        } else {
            return $return;
        }
    }
    
    public static function getOrderBy($_config){
         switch ($_config) {
            case "online":
                return 0;
                break;
            case "saving":
                return 1;
                break;
            case "mac":
                return 2;
                break;
            case "ip":
                return 3;
                break;
            case "name":
                return 4;
                break;
            case "comment":
                return 5;
                break;
            case "time":
                return 6;
                break;
        }
    }
    
    public static function getElementVueNetwork($_device, $_savingMac, $_commentMac = NULL){
        
        if(empty($_savingMac[$_device["mac_id"]]["offline_time"])){
            $return["offline_time"] = NULL;
        } else {
            $return["offline_time"] = $_savingMac[$_device["mac_id"]]["offline_time"];
        }

        if (isset($_savingMac[$_device["mac_id"]]["name"])) {
            $return["name"] = $_savingMac[$_device["mac_id"]]["name"];
        } else {
            $return["name"] = "| ". $_device["equipement"];
        }

        if (scan_ip_tools::isOffline($return["offline_time"], $_device["time"]) == 0) {
            $return["colorOnLine"] = "#50aa50";
            $return["titleOnLine"] = "En ligne";
            $return["lineSortOnline"] = 1;
        } else {
            $return["colorOnLine"] = "red";
            $return["titleOnLine"] = "Hors ligne";
            $return["lineSortOnline"] = 0;
        }

        if(!empty($_commentMac[$_device["mac_id"]])){
            $return["printComment"] = $_commentMac[$_device["mac_id"]];
        } else {
            $return["printComment"] = "";
        }

        if (isset($_savingMac[$_device["mac_id"]]["enable"])) {
            if ($_savingMac[$_device["mac_id"]]["enable"] == 1) {
                $return["classPresent"] = "color: green;";
                $return["textPresent"] = '<i class="fas fa-check"></i>';
                $return["classSuivi"] = " display: block; width: 78x !important; padding : 2px 5px; color : white; text-align: center; color: green;";
                $return["titleEquipement"] = "Cet équipement est enregistré et activé";
                $return["lineSortEquipement"] = 2;
            } else {
                $return["classPresent"] = "color: #FF4500;";
                $return["textPresent"] = '<i class="fas fa-exclamation-circle"></i>';
                $return["classSuivi"] = " display: block; width: 78x !important; padding : 2px 5px; color : white; text-align: center; color: #FF4500;";
                $return["titleEquipement"] = "Cet équipement est enregistré mais désactivé";
                $return["lineSortEquipement"] = 1;
            }
        } else {
            $return["classPresent"] = "color: grey;";
            $return["textPresent"] = '<i class="fas fa-info-circle"></i>';
            $return["classSuivi"] = " display: block; width: 78x !important; padding : 2px 5px; color : white; text-align: center; color: grey;";
            $return["titleEquipement"] = "Cet équipement n'est pas enregistré";
            $return["lineSortEquipement"] = 0;
        }

        return $return;
    }
    
}
