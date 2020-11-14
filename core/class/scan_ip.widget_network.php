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
            . '<td class="scanTd" style="text-align:center;">' . $list++ . '</td>'
            . '<td class="scanTd" title="' . $element["titleOnLine"] .'"><span style="display:none;">' . $element["lineSortOnline"] . '</span>' . scan_ip_tools::getCycle("15px", $element["colorOnLine"]) . '</td>'
            . '<td class="scanTd ' . $element["classPresent"] . '" style="style="text-align:center !important;" title="' . $element["titleEquipement"] .'"><span style="display:none;">' . $element["lineSortEquipement"] . '</span><span class="' . $element["classSuivi"] . '">' . $element["textPresent"] . '</span></td>'
            . '<td class="scanTd">' . $device["mac"] . '</td>'
            . '<td class="scanTd"><span style="display:none;">' . scan_ip_tools::getCleanForSortTable($device["ip_v4"]) . '</span>' . $device["ip_v4"] . '</td>'
            . '<td class="scanTd" style="text-overflow: ellipsis;"><span style="display:none;">' . $element["nameSort"] . '</span>' . $element["name"] . '</td>'
            . '<td class="scanTd"><span style="display:none;">' . $element["printCommentSort"] . '</span>' . $element["printComment"] . '</td>'
            . '<td class="scanTd"><span style="display:none;">' . $device["time"] . '</span>' . date("d/m/Y H:i:s", $device["time"]) . '</td>'
            . '</tr>';

        }

        $replace["#widget_network#"] .= '</tbody></table>';
        $replace["#widget_network#"] .= '<script src="plugins/scan_ip/desktop/js/lib/stupidtable.min.js"/></script>';
        $replace["#widget_network#"] .= '<script>$(document).ready(function ($) { $("#scan_ip_network_widget").stupidtable(); });</script>';
        
        return $replace;
    }
    
}
