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
            $scan_ip->save();
        }
        
        return $scan_ip;
    }
    
    public function setCmdWidgetNetwork($_scanIp){
        
        $refresh = $_scanIp->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new scan_ipCmd();
            $refresh->setName(__('Rafraichir', __FILE__));
        }
        $refresh->setEqLogic_id($_scanIp->getId());
        $refresh->setLogicalId('refresh');
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->save();
        
    }
    
    public static function createNetworkWidget($_this, $_version = 'dashboard', $_replace, $_reseau) {

        log::add('scan_ip', 'debug', 'createNetworkWidget :.  Lancement');

        $replace = $_replace;
        $savingMac = scan_ip_eqLogic::getAlleqLogics();
        $commentMac = scan_ip_json::getCommentaires();

        $replace["#widget_network#"] = '<table style="width: 100%; margin: -5px -5px 22px 0;" id="scan_ip_network_widget">
        <thead>
            <tr style="background-color: grey !important; color: white !important;">
                <th data-sort="string" style="padding : 3px 0 3px 15px !important; text-align: center; width:30px;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b></span></th>
                <th data-sort="int" style="text-align: center; width:30px; padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b></span></th>
                <th data-sort="string" style="width:130px;padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Adresse MAC</span></th>
                <th data-sort="int" style="width:110px;padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Ip</span></th>
                <th data-sort="string" style="width:375px;padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Nom</span></th>
                <th data-sort="string" style="width:375px;padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Commentaire</span></th>
                <th data-sort="int" style="width:170px;padding : 3px 0 3px 15px !important;"><span style="cursor: pointer !important; width: 100%;"><b class="caret"></b> Date de mise à jour</span></th>
            </tr>
        </thead>
        <tbody>';

        foreach ($_reseau["sort"] as $device) {

            $element = scan_ip_tools::getElementVueNetwork($device, $savingMac, $commentMac);

            $replace["#widget_network#"] .= '<tr>'
            . '<td style="padding : 3px 0 3px 15px !important;" title="' . $element["titleOnLine"] .'"><span style="display:none;">' . $element["lineSortOnline"] . '</span>' . scan_ip_tools::getCycle("15px", $element["colorOnLine"]) . '</td>'
            . '<td style="text-align:center !important; padding : 3px 0 3px 15px !important;' . $element["classPresent"] . '" title="' . $element["titleEquipement"] .'"><span style="display:none;">' . $element["lineSortEquipement"] . '</span><span class="' . $element["classSuivi"] . '">' . $element["textPresent"] . '</span></td>'
            . '<td style="padding : 3px 0 3px 15px !important;">' . $device["mac"] . '</td>'
            . '<td style="padding : 3px 0 3px 15px !important;"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($device["ip_v4"]) . '</span>' . $device["ip_v4"] . '</td>'
            . '<td style="text-overflow: ellipsis;padding : 3px 0 3px 15px !important;"><span style="display:none;">' . $element["nameSort"] . '</span>' . $element["name"] . '</td>'
            . '<td style="padding : 3px 0 3px 15px !important;"><span style="display:none;">' . $element["printCommentSort"] . '</span>' . $element["printComment"] . '</td>'
            . '<td style="padding : 3px 0 3px 15px !important;"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($device["time"]) . '</span>' . scan_ip_tools::printDate($device["time"]) . '</td>'
            . '</tr>';

        }
        
        switch ($_this->getConfiguration("saveOrderColonWidegetNetwork")) {
            case "online":
                $orderBy = 0;
                break;
            case "saving":
                $orderBy = 1;
                break;
            case "mac":
                $orderBy = 2;
                break;
            case "ip":
                $orderBy = 3;
                break;
            case "name":
                $orderBy = 4;
                break;
            case "comment":
                $orderBy = 5;
                break;
            case "time":
                $orderBy = 6;
                break;

        }

        $replace["#widget_network#"] .= '</tbody></table>';
        $replace["#widget_network#"] .= '<script src="plugins/scan_ip/desktop/js/lib/stupidtable.min.js"/></script>';
        $replace["#widget_network#"] .= '<script>$(document).ready(function ($) { var $table = $("#scan_ip_network_widget").stupidtable(); var $th_to_sort = $table.find("thead th").eq('.$orderBy.'); $th_to_sort.stupidsort(); });</script>';
        
        return $replace;
    }
    
}
