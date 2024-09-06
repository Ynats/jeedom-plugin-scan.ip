<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_widget_alerte extends eqLogic {

    public static function getWidgetAlerteNewEquipement(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if(scan_ip_widgets::getWidgetType($scan_ip) == 'new_equipement') {
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
            $scan_ip->setDisplay('width',"1130px");
            $scan_ip->setName("Scan.Ip Widget Alertes");
            $scan_ip->setConfiguration('type_widget', 'new_equipement');
            $scan_ip->save();
        }
        
        return $scan_ip;
    }
    
    public static function setCmdAlerteNewEquipement($_scanIp){
        
        for ($i = 0; $i <= (scan_ip::$_defaut_alerte_new_equipement -1); $i++) {
            $info = $_scanIp->getCmd(null, 'last_'.$i.'_mac');
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i.' MAC', __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i.'_mac');
            $info->setType('info');
            $info->setSubType('string');
            $info->save();
            
            $info = $_scanIp->getCmd(null, 'last_'.$i.'_time');
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i.' Time', __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i.'_time');
            $info->setType('info');
            $info->setSubType('numeric');
            $info->save();
            
            $info = $_scanIp->getCmd(null, 'last_'.$i.'_date');
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i.' Date', __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i.'_date');
            $info->setType('info');
            $info->setSubType('string');
            $info->save();
            
            $info = $_scanIp->getCmd(null, 'last_'.$i.'_ip_v4');
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i.' IpV4', __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i.'_ip_v4');
            $info->setType('info');
            $info->setSubType('string');
            $info->save();
            
            $info = $_scanIp->getCmd(null, 'last_'.$i.'_equipement');
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i.' Equipement', __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i.'_equipement');
            $info->setType('info');
            $info->setSubType('string');
            $info->save();
            
            $info = $_scanIp->getCmd(null, 'last_'.$i.'_record');
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i.' Création', __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i.'_record');
            $info->setType('info');
            $info->setSubType('string');
            $info->save();
        }
        
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
    
    public static function cmdRefreshWidgetAlerte($_eqlogic, $_mapping = NULL){
        
        $temp = array();
        
        $mac_id = scan_ip_eqLogic::getEquipementsbyId();
        
        if($mac_id != NULL){
            
            foreach ($_mapping["byTime"] as $allElement) { 
                foreach ($allElement as $element) { 
                    if(!in_array($element["mac_id"], $mac_id)){
                        $temp[] = array(
                            "time" => $element["time"], 
                            "record" => $element["record"],
                            "mac" => $element["mac"], 
                            "ip_v4" => $element["ip_v4"],
                            "equipement" => $element["equipement"]
                        );   
                    }  
                }
            }

            for ($i = 0; $i <= (scan_ip::$_defaut_alerte_new_equipement -1); $i++) {

                if(!empty($temp[$i]["record"])){
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_record', $temp[$i]["record"]);
                } else {
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_record', NULL);
                }
                if(!empty($temp[$i]["mac"])){
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_mac', $temp[$i]["mac"]);
                } else {
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_mac', NULL);
                }
                if(!empty($temp[$i]["time"])){
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_time', $temp[$i]["time"]);
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_date', scan_ip_tools::printDate($temp[$i]["time"]));
                } else {
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_time', NULL);
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_date', NULL);
                }
                if(!empty($temp[$i]["equipement"])){
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_equipement', $temp[$i]["equipement"]);
                } else {
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_equipement', NULL);
                }
                if(!empty($temp[$i]["ip_v4"])){
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_ip_v4', $temp[$i]["ip_v4"]);
                } else {
                    $_eqlogic->checkAndUpdateCmd('last_'.$i.'_ip_v4', NULL);
                }
            }
            
        }
        
    }
    
    public static function createAlerteWidget($scan_ip, $_version = 'dashboard', $_replace) {

        log::add('scan_ip', 'debug', 'createAlerteWidget :.  Lancement');

        $replace = $_replace;
        
        $temp = array();
        
        $replace["#last_unknown_equipement#"] = '<table style="width: 100%; margin: -5px -5px 22px 0;">
        <thead>
            <tr style="background-color: grey !important; color: white !important;">
                <th style="padding : 3px 0 3px 15px !important; text-align: center;"></th>
                <th style="padding : 3px 0 3px 15px !important;">Date</th>
                <th style="padding : 3px 0 3px 15px !important;">Adresse MAC</th>
                <th style="padding : 3px 0 3px 15px !important;">Ip</th>
                <th style="padding : 3px 0 3px 15px !important;">Information</th>
                <th style="padding : 3px 0 3px 15px !important;">Vu la première fois</th>
            </tr>
        </thead>
        <tbody>';
        
        for ($i = 0; $i <= (scan_ip::$_defaut_alerte_new_equipement -1); $i++) {
            if(!empty(scan_ip_cmd::getCommande("last_".$i."_date", $scan_ip))){ 
                
                $record = scan_ip_cmd::getCommande("last_".$i."_record", $scan_ip, NULL);
                $time = scan_ip_cmd::getCommande("last_".$i."_time", $scan_ip, NULL);
                $offline_time = $scan_ip->getConfiguration("offline_time", scan_ip::$_defaut_offline_time);
                
               
                if($record != NULL){
                    $record = date("d/m/Y H:i:s", $record);
                } else {
                    $record = NULL;
                }
                
                if (scan_ip_tools::isOffline($offline_time, $time) == 0) {
                    $element["colorOnLine"] = "#50aa50";
                    $element["titleOnLine"] = "En ligne";
                } else {
                    $element["colorOnLine"] = "red";
                    $element["titleOnLine"] = "Hors ligne";
                }
                
                $replace["#last_unknown_equipement#"] .= '<tr>'
                . '<td style="padding : 3px 0 3px 15px !important;" title="' . $element["titleOnLine"] .'">' . scan_ip_tools::getCycle("15px", $element["colorOnLine"]) . '</td>'
                . '<td style="padding : 3px 0 3px 15px !important;">' . scan_ip_cmd::getCommande("last_".$i."_date", $scan_ip) . '</td>'
                . '<td style="padding : 3px 0 3px 15px !important;">' . scan_ip_cmd::getCommande("last_".$i."_mac", $scan_ip) . '</td>'
                . '<td style="padding : 3px 0 3px 15px !important;">' . scan_ip_cmd::getCommande("last_".$i."_ip_v4", $scan_ip) . '</td>'
                . '<td style="padding : 3px 0 3px 15px !important;">' . scan_ip_cmd::getCommande("last_".$i."_equipement", $scan_ip) . '</td>'
                . '<td style="padding : 3px 0 3px 15px !important;">' . $record . '</td>'
                . '</tr>';
            }
        }
        
        $replace["#last_unknown_equipement#"] .= '</tbody></table>';
        
        return $replace;
    }
    
}
