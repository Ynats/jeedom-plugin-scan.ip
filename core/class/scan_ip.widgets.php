<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_widgets extends eqLogic {
    
    public static function getWidgetType($_eq) {
        return $_eq->getConfiguration('type_widget', 'normal');
    }
    
    public static function getWidgetNetwork(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if(self::getWidgetType($scan_ip) == 'network') {
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
            $scan_ip->save();
        }
        
        return $scan_ip;
    }
    
    public static function postConfig_widget_network() { 
        
        $eqLogic = self::getWidgetNetwork();
        
        if(config::byKey('widget_network', 'scan_ip', '1') == 1) {
            $eqLogic->setIsVisible(1);
            $eqLogic->setIsEnable(1);
        } else {
            $eqLogic->setIsVisible(0);
            $eqLogic->setIsEnable(0);
        }
        
        $eqLogic->save();        
    }
    
    public static function getWidgetAlerteNewEquipement(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if(self::getWidgetType($scan_ip) == 'new_equipement') {
                $exist = 1;
                break;
            }
        }
        
        if($exist == 0){
            $scan_ip = new scan_ip();
            $scan_ip->setEqType_name("scan_ip");
            $scan_ip->setIsEnable(1);
            if(config::byKey('widget_new_equipement', 'scan_ip', '1') == 1) { 
                $scan_ip->setIsVisible(1); 
                $scan_ip->setIsEnable(1);
            } else { 
                $scan_ip->setIsVisible(0); 
                $scan_ip->setIsEnable(0);
            }
            $scan_ip->setDisplay('height',"230px");
            $scan_ip->setDisplay('width',"712px");
            $scan_ip->setName("Scan.Ip Widget Alertes");
            $scan_ip->setConfiguration('type_widget', 'new_equipement');
            $scan_ip->save();
        }
        
        return $scan_ip;
    }
    
    public static function postConfig_widget_new_equipement() { 
        
        $eqLogic = self::getWidgetAlerteNewEquipement();
        
        if(config::byKey('widget_new_equipement', 'scan_ip', '1') == 1) {
            $eqLogic->setIsVisible(1);
            $eqLogic->setIsEnable(1);
        } else {
            $eqLogic->setIsVisible(0);
            $eqLogic->setIsEnable(0);
        }
        
        $eqLogic->save();        
    }
    
    public static function createNetworkWidget($_version = 'dashboard', $_replace, $_reseau) {

        log::add('scan_ip', 'debug', 'createNetworkWidget :.  Lancement');

        $replace = $_replace;
        $savingMac = scan_ip_eqLogic::getAlleqLogics();
        $commentMac = scan_ip_json::getCommentaires();

        $replace["#widget_network#"] = '<table style="width: 100%; margin: -5px -5px 22px 0;" id="scan_ip_network_widget">
        <thead>
            <tr style="background-color: grey !important; color: white !important;">
                <th data-sort="int" class="scanTd" style="text-align: center; width:30px;"><span class="scanHender"><b class="caret"></b></span></th>
                <th data-sort="string" class="scanTd" style="text-align: center; width:30px;" class="scanTd"><span class="scanHender"><b class="caret"></b></span></th>
                <th data-sort="int" style="text-align: center; width:30px;" class="scanTd"><span class="scanHender"><b class="caret"></b></span></th>
                <th data-sort="string" style="width:130px;" class="scanTd"><span class="scanHender"><b class="caret"></b> Adresse MAC</span></th>
                <th data-sort="int" class="scanTd" style="width:110px;"><span class="scanHender"><b class="caret"></b> Ip</span></th>
                <th data-sort="string" class="scanTd" style="width:375px;"><span class="scanHender"><b class="caret"></b> Nom</span></th>
                <th data-sort="string" class="scanTd" style="width:375px;"><span class="scanHender"><b class="caret"></b> Commentaire</span></th>
                <th data-sort="int" class="scanTd" style="width:170px;"><span class="scanHender"><b class="caret"></b> Date de mise à jour</span></th>
            </tr>
        </thead>
        <tbody>';

        $list = 1;
        foreach ($_reseau["sort"] as $device) {

            $element = scan_ip_tools::getElementVueNetwork($device, $savingMac, $commentMac);

            $replace["#widget_network#"] .= '<tr>'
            . '<td class="scanTd ' . $element["classPresent"] . '" style="text-align:center;">' . $list++ . '</td>'
            . '<td class="scanTd" title="' . $element["titleOnLine"] .'"><span style="display:none;">' . $element["lineSortOnline"] . '</span>' . scan_ip_tools::getCycle("15px", $element["colorOnLine"]) . '</td>'
            . '<td class="scanTd ' . $element["classPresent"] . '" style="style="text-align:center !important;" title="' . $element["titleEquipement"] .'"><span style="display:none;">' . $element["lineSortEquipement"] . '</span><span class="' . $element["classSuivi"] . '">' . $element["textPresent"] . '</span></td>'
            . '<td class="scanTd ' . $element["classPresent"] . '">' . $device["mac"] . '</td>'
            . '<td class="scanTd ' . $element["classPresent"] . '"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($device["ip_v4"]) . '</span>' . $device["ip_v4"] . '</td>'
            . '<td class="scanTd ' . $element["classPresent"] . '" style="text-overflow: ellipsis;"><span style="display:none;">' . $element["nameSort"] . '</span>' . $element["name"] . '</td>'
            . '<td class="scanTd ' . $element["classPresent"] . '"><span style="display:none;">' . $element["printCommentSort"] . '</span>' . $element["printComment"] . '</td>'
            . '<td class="scanTd ' . $element["classPresent"] . '"><span style="display:none;">' . $device["time"] . '</span>' . date("d/m/Y H:i:s", $device["time"]) . '</td>'
            . '</tr>';

        }

        $replace["#widget_network#"] .= '</tbody></table>';
        $replace["#widget_network#"] .= '<script src="plugins/scan_ip/desktop/js/lib/stupidtable.min.js"/></script>';
        $replace["#widget_network#"] .= '<script>$(document).ready(function ($) { $("#scan_ip_network_widget").stupidtable(); });</script>';
        
        return $replace;
    }
    
    public static function createAlerteWidget($scan_ip, $_version = 'dashboard', $_replace) {

        log::add('scan_ip', 'debug', 'createAlerteWidget :.  Lancement');

        $replace = $_replace;
        
        $temp = array();
        
        $replace["#last_unknown_equipement#"] = '<table style="width: 100%; margin: -5px -5px 22px 0;">
        <thead>
            <tr style="background-color: grey !important; color: white !important;">
                <th class="scanTd" style="width:170px;">Date</th>
                <th class="scanTd" style="width:130px;">Adresse MAC</th>
                <th class="scanTd" style="width:110px;">Ip</th>
                <th class="scanTd" style="width:375px;">Information</th>
            </tr>
        </thead>
        <tbody>';
        
        for ($i = 0; $i <= (scan_ip::$_defaut_alerte_new_equipement -1); $i++) {
            if(!empty(scan_ip_cmd::getCommande("last_".$i."_date", $scan_ip))){  
                $replace["#last_unknown_equipement#"] .= '<tr>'
                . '<td class="scanTd">' . scan_ip_cmd::getCommande("last_".$i."_date", $scan_ip) . '</td>'
                . '<td class="scanTd">' . scan_ip_cmd::getCommande("last_".$i."_mac", $scan_ip) . '</td>'
                . '<td class="scanTd">' . scan_ip_cmd::getCommande("last_".$i."_ip_v4", $scan_ip) . '</td>'
                . '<td class="scanTd">' . scan_ip_cmd::getCommande("last_".$i."_equipement", $scan_ip) . '</td>'
                . '</tr>';
            }
            
        }
        
        $replace["#last_unknown_equipement#"] .= '</tbody></table>';
        
        return $replace;
    }
    
    public static function createSimpleWidget($scan_ip, $_version = "dashboard", $_replace) {

        log::add('scan_ip', 'debug', 'createSimpleWidget :.  Lancement');

        $replace = $_replace;

        $replace["#ip_v4#"] = scan_ip_cmd::getCommande('ip_v4', $scan_ip);
        if($replace["#ip_v4#"] == ""){ $replace["#ip_v4#"] = "..."; }

        if(!empty(scan_ip_cmd::getCommande('last_ip_v4', $scan_ip))){ $replace["#last_ip_v4#"] = scan_ip_cmd::getCommande('last_ip_v4', $scan_ip); } 
        else { $replace["#last_ip_v4#"] = "..."; }

        if(!empty(scan_ip_cmd::getCommande('update_date', $scan_ip))){ $replace["#update_date#"] = scan_ip_cmd::getCommande('update_date', $scan_ip); } 
        else { $replace["#update_date#"] = "..."; }

        $replace["#mac#"] = $scan_ip->getConfiguration("adress_mac");

        if($replace["#ip_v4#"] == "..."){ $replace["#etat_cycle#"] = "red"; } 
        else{ $replace["#etat_cycle#"] = "#50aa50"; } 

        if($replace["#last_ip_v4#"] != $replace["#ip_v4#"] AND $replace["#ip_v4#"] != "..."){ $replace["#etat_last_ip#"] = ' color:orange;'; } 
        else { $replace["#etat_last_ip#"] = ''; }

        $wol = $scan_ip->getCmd(null,'wol');
        $replace['#cmdWol#'] = (is_object($wol)) ? $wol->getId() : '';

        if($scan_ip->getConfiguration("enable_wol") == 0){ $replace['#enableWol#'] = "display:none;"; }
        else { $replace['#enableWol#'] = ""; }

        return $replace;
    }
    
    public static function getIdWidgetSpeciaux(){
        $return = NULL;
        $widgetDetect = 0;
        $widgetMax = 2;
        foreach ($eqLogics as $eqLogic) {
            if($eqLogic->getConfiguration('type_widget', 'normal') == "network"){
                $return["network"] = $eqLogic->getId();
                $widgetDetect++;
            } 
            elseif($eqLogic->getConfiguration('type_widget', 'normal') == "new_equipement"){
                $return["new_equipement"] = $eqLogic->getId();
                $widgetDetect++;
            }
            if($widgetDetect >= $widgetMax){
                break;
            }
        }
        return $return;
    }
    
}
