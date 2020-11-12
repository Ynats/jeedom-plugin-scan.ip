<?php

/**
 * Description of scan_ip
 *
 * @author Ynats
 */

require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip_cmd extends eqLogic {
    
    public function setCmdWidgetNormal($_scanIp){
        
        $info = $_scanIp->getCmd(null, 'ip_v4');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('IpV4', __FILE__));
        }
        $info->setLogicalId('ip_v4');
        $info->setEqLogic_id($_scanIp->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('string');
        $info->save();

        $info = $_scanIp->getCmd(null, 'last_ip_v4');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Last IpV4', __FILE__));
        }
        $info->setLogicalId('last_ip_v4');
        $info->setEqLogic_id($_scanIp->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('string');
        $info->save();

        $info = $_scanIp->getCmd(null, 'update_time');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Last Time', __FILE__));
        }
        $info->setLogicalId('update_time');
        $info->setEqLogic_id($_scanIp->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('numeric');
        $info->save();

        $info = $_scanIp->getCmd(null, 'update_date');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Last Date', __FILE__));
        }
        $info->setLogicalId('update_date');
        $info->setEqLogic_id($_scanIp->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('string');
        $info->save();


        $info = $_scanIp->getCmd(null, 'on_line');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Online', __FILE__));
        }
        $info->setEqLogic_id($_scanIp->getId());
        $info->setLogicalId('on_line');
        $info->setType('info');
        $info->setSubType('binary');
        $info->save();

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

        $wol = $_scanIp->getCmd(null, 'wol');
        if($_scanIp->getConfiguration("enable_wol") == 1){ 
            if (!is_object($wol)) {
                $wol = new scan_ipCmd();
                $wol->setName(__('WoL', __FILE__));
            }
            $wol->setEqLogic_id($_scanIp->getId());
            $wol->setLogicalId('wol');
            $wol->setType('action');
            $wol->setSubType('other');
            $wol->save();
        } else {
            if (is_object($wol)) {
                $wol->remove();
                ajax::success(utils::o2a($_scanIp));
            } 
        }  
        
    }
    
    public function setCmdAlerteNewEquipement($_scanIp){
        
        for ($i = 0; $i <= (scan_ip::$_defaut_alerte_new_equipement -1); $i++) {
            $info = $_scanIp->getCmd(null, 'last_'.$i);
            if (!is_object($info)) {
                $info = new scan_ipCmd();
                $info->setName(__('Connexion '.$i, __FILE__));
            }
            $info->setEqLogic_id($_scanIp->getId());
            $info->setLogicalId('last_'.$i);
            $info->setType('info');
            $info->setSubType('numeric');
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
    
    public static function getCommande($_ComName, $_this){
        $tmp_cmd = $_this->getCmd(null, $_ComName);
        return (is_object($tmp_cmd)) ? $tmp_cmd->execCmd() : '';
    }
    
    public static function cmdRefresh($eqlogic, $_mapping = NULL){

        log::add('scan_ip', 'debug', 'cmdRefresh :. Lancement');
        
        if($_mapping == NULL){
            $_mapping = scan_ip_json::getJson(scan_ip::$_jsonMapping);
        }
        
        if(scan_ip_widgets::getWidgetType($eqlogic) == "normal"){

            $device = scan_ip_json::searchByMac($eqlogic->getConfiguration("adress_mac"), $_mapping);
            $offline_time = $eqlogic->getConfiguration("offline_time", scan_ip::$_defaut_offline_time);

            if(scan_ip_tools::isOffline($offline_time, $device["time"]) == 0){
                $eqlogic->checkAndUpdateCmd('ip_v4', $device["ip_v4"]); 
                $last_ip_v4 = self::getCommande('last_ip_v4', $eqlogic);
                if($last_ip_v4 == "") { $eqlogic->checkAndUpdateCmd('last_ip_v4', $device["ip_v4"]); }
                $eqlogic->checkAndUpdateCmd('on_line', 1); 
            } else {
                $eqlogic->checkAndUpdateCmd('on_line', 0);
                $eqlogic->checkAndUpdateCmd('ip_v4', NULL);
                $eqlogic->checkAndUpdateCmd('last_ip_v4', $device["ip_v4"]);
            }

            ///////////////////////////////////////////
            // Mise à jour de l'élément associé

            scan_ip_bridges::majElementsAssocies($eqlogic, $device);

            // Mise à jour de l'élément associé
            ///////////////////////////////////////////

            if(!empty($device["time"])) {
                $eqlogic->checkAndUpdateCmd('update_time', $device["time"]);
                $eqlogic->checkAndUpdateCmd('update_date', date("d/m/Y H:i:s", $device["time"]));
            } else {
                $eqlogic->checkAndUpdateCmd('update_time', NULL);
                $eqlogic->checkAndUpdateCmd('update_date', NULL);
            }
        
        }
        
        $eqlogic->toHtml('dashboard');
        $eqlogic->refreshWidget();
    }
    
}
