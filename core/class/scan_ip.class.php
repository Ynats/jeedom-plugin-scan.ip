<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */

require_once __DIR__ . '/../../../../core/php/core.inc.php';

class scan_ip extends eqLogic {
    /*     * *************************Attributs****************************** */
    
    public static $_widgetPossibility = array('custom' => true);
    
    public static $_folderJson = __DIR__ . "/../../../../plugins/scan_ip/core/json/";
    public static $_jsonBridges = __DIR__ . "/../../../../plugins/scan_ip/core/json/bridges.json";
    public static $_jsonMapping = __DIR__ . "/../../../../plugins/scan_ip/core/json/mapping";
    public static $_jsonEquipement = __DIR__ . "/../../../../plugins/scan_ip/core/json/equipements";
    public static $_jsonCommentairesEquipement = __DIR__ . "/../../../../plugins/scan_ip/core/json/commentMac";
    
    public static $_bash_oui = "sudo get-oui -u http://standards-oui.ieee.org/oui.txt -f " . __DIR__ . "/../../../../plugins/scan_ip/resources/oui.txt";
    public static $_file_oui =  __DIR__ . "/../../../../plugins/scan_ip/resources/oui.txt";
    public static $_bash_iab = "sudo get-iab -u http://standards-oui.ieee.org/iab/iab.txt -f " . __DIR__ . "/../../../../plugins/scan_ip/resources/iab.txt";
    public static $_file_iab =  __DIR__ . "/../../../../plugins/scan_ip/resources/iab.txt";
    public static $_file_lock =  __DIR__ . "/../../../../plugins/scan_ip/resources/lock";
    public static $_defaut_cron_pass = 1;
    public static $_defaut_offline_time = 4;
    public static $_defaut_bridges_by_equipement = 10;

    /*     * ***********************Methode static*************************** */

//    public function preInsert() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preInsert :. Lancement');
//    }
//    public function preSave() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preSave :. Lancement');
//    }

    public function postInsert() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postInsert :. Lancement');
        
        $this->setConfiguration("offline_time", self::$_defaut_offline_time);
        $this->save();
    }
    
    public function postUpdate() {
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postUpdate :. Mise à jour de : ' . $this->getId());
        
        $info = $this->getCmd(null, 'ip_v4');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('IpV4', __FILE__));
        }
        $info->setLogicalId('ip_v4');
        $info->setEqLogic_id($this->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('string');
        $info->save();
        
        $info = $this->getCmd(null, 'last_ip_v4');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Last IpV4', __FILE__));
        }
        $info->setLogicalId('last_ip_v4');
        $info->setEqLogic_id($this->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('string');
        $info->save();
        
        $info = $this->getCmd(null, 'update_time');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Last Time', __FILE__));
        }
        $info->setLogicalId('update_time');
        $info->setEqLogic_id($this->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('numeric');
        $info->save();
        
        $info = $this->getCmd(null, 'update_date');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Last Date', __FILE__));
        }
        $info->setLogicalId('update_date');
        $info->setEqLogic_id($this->getId());
        $info->setIsHistorized(0);
        $info->setIsVisible(0);
        $info->setType('info');
        $info->setSubType('string');
        $info->save();


        $info = $this->getCmd(null, 'on_line');
        if (!is_object($info)) {
            $info = new scan_ipCmd();
            $info->setName(__('Online', __FILE__));
        }
        $info->setEqLogic_id($this->getId());
        $info->setLogicalId('on_line');
        $info->setType('info');
        $info->setSubType('binary');
        $info->save();
        
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new scan_ipCmd();
            $refresh->setName(__('Rafraichir', __FILE__));
        }
        $refresh->setEqLogic_id($this->getId());
        $refresh->setLogicalId('refresh');
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->save();
       
        $wol = $this->getCmd(null, 'wol');
        if($this->getConfiguration("enable_wol") == 1){ 
            if (!is_object($wol)) {
                $wol = new scan_ipCmd();
                $wol->setName(__('WoL', __FILE__));
            }
            $wol->setEqLogic_id($this->getId());
            $wol->setLogicalId('wol');
            $wol->setType('action');
            $wol->setSubType('other');
            $wol->save();
        } else {
            if (is_object($wol)) {
                $wol->remove();
                ajax::success(utils::o2a($this));
            } 
        }
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
    
    public function postSave() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postSave :. Refresh Command : ' . $this->getId());
                
        // Mise à jour des données
        self::cmdRefresh($this, self::getJson(self::$_jsonMapping));

        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }

//    public function preUpdate() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preUpdate :. lancement');
//    }
//
//    public function preRemove() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preRemove :. lancement');
//    }
//
//    public function postRemove() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'postRemove :. lancement');
//    }


    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
      public static function postConfig_<Variable>() {
      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
      public static function preConfig_<Variable>() {
      }
     */

    /*     * **********************Getteur Setteur*************************** */
    
    public static function getSubReseauEnable($_ipRoute = NULL) {
        log::add('scan_ip', 'debug', 'getSubReseauEnable :. Lancement');
        
        $a = $return["subReseauEnable"] = 0;
        foreach (self::scanSubReseau($_ipRoute) as $sub) { 
            $return["subReseau"][$a]["enable"] = config::byKey('sub_enable_'.md5($sub["name"]), 'scan_ip', 0);
            $return["subReseau"][$a]["name"] = $sub["name"];
            $return["subReseauEnable"] =  $return["subReseau"][$a]["enable"] + $return["subReseauEnable"];
            $a++;
        }
        
        return $return; 
    }
    
    public static function syncScanIp($_mapping = NULL){
        log::add('scan_ip', 'debug', '////////////////////////////////////////////////////////////////////');
        log::add('scan_ip', 'debug', 'syncScanIp :. Lancement du scan du réseau');
        
        if($_mapping == NULL){
            $_mapping = self::getJson(self::$_jsonMapping);
        }
        
        self::scanReseau();
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                log::add('scan_ip', 'debug', 'syncScanIp :. cmdRefresh('.$scan_ip->getId().')');
                self::cmdRefresh($scan_ip, $_mapping);
            }
        }  
        
        log::add('scan_ip', 'debug', 'syncScanIp :. Fin du scan du réseau');
        log::add('scan_ip', 'debug', '////////////////////////////////////////////////////////////////////');
    }
    
    public static function scanReseau(){
        log::add('scan_ip', 'debug', "////////////////////////////////////////////////////////////////////");
        log::add('scan_ip', 'debug', 'scanReseau :. Lancement');
        
        $ipRoute = self::getIpRoute();
        $subReseau = self::getSubReseauEnable($ipRoute);  
        $infoJeedom = self::getInfoJeedom($ipRoute);

        if($subReseau["subReseauEnable"] > 0) {
            $new = array();
            foreach ($subReseau["subReseau"] as $sub) { 
                if($sub["enable"] == 1){
                    $scanResult = self::arpScanShell($sub["name"]); 
                    $new = self::arrayCompose($new, $scanResult);
                }
            }
        } else {
            $new = self::arpScanShell();
        }

        if(count($new) == 0){
            log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
            exit();
        } 
        else {
            $old = self::getJson(self::$_jsonMapping);
            
            if(empty($old) OR count($old) == 0){ $now = $new; } 
            else { $now = self::arrayCompose($old, $new); } 
            
            $now = self::cleanArrayEquipement($now);
            self::createJsonFile(self::$_jsonEquipement, $now); 
            
            foreach ($now as $mac => $scanLine) {
                if($scanLine["ip_v4"] == $ipRoute){
                    $now["route"]["ip_v4"] = $scanLine["ip_v4"];
                    $now["route"]["mac"] = $mac;
                } else {
                    $now["sort"][explode(".",$scanLine["ip_v4"])[3]] = array(
                            "ip_v4" => $scanLine["ip_v4"], 
                            "mac" => $mac, 
                            "time" => $scanLine["time"], 
                            "equipement" => $scanLine["equipement"]
                    );
                    $now["byIpv4"][$scanLine["ip_v4"]] = array("mac" => $mac, "equipement" => $scanLine["equipement"], "time" => $scanLine["time"]);
                    $now["byMac"][$mac] = array("ip_v4" => $scanLine["ip_v4"], "equipement" => $scanLine["equipement"], "time" => $scanLine["time"]);   
                }
            }

            ksort($now["sort"]);
        }

        $now["jeedom"] = $infoJeedom; 
        $now["infos"]["version_arp"] = self::arpVersion();
        $now["infos"]["time"] = time();
        $now["infos"]["date"] = date("d/m/Y H:i:s", $now["infos"]["time"]);

        self::recordInJson(self::$_jsonMapping, $now);
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $now["infos"]["version_arp"] . ']');
        log::add('scan_ip', 'debug', "////////////////////////////////////////////////////////////////////");
    }
    
    public static function getCleanForSortTable($_string){
        if (preg_match(self::getRegex("ip_v4"), $_string)) { 
            return str_replace(".", "", $_string);
        } else {
            return strtolower($_string);
        }
    }
    
    public static function cleanArrayEquipement($_array){
        $return = NULL;
        foreach ($_array as $mac => $scanLine) {
            if(!empty($scanLine["ip_v4"]) AND !empty($scanLine["time"]) AND !empty($scanLine["equipement"]) AND !empty($mac)){
                $return[$mac] = $scanLine;
            }
        }
        return $return;
    }
     
    public static function searchByMac($_searchMac, $_mapping = NULL){ 
        log::add('scan_ip', 'debug', 'searchByMac :. Lancement');
        
        if($_mapping == NULL){
            $_mapping = self::getJson(self::$_jsonMapping);
        }
        
        if(!empty($_mapping["byMac"][$_searchMac]["ip_v4"])){
            $return["ip_v4"] = $_mapping["byMac"][$_searchMac]["ip_v4"];
            $return["time"] = $_mapping["byMac"][$_searchMac]["time"];
            $return["equipement"] = $_mapping["byMac"][$_searchMac]["equipement"];
            return $return;
        } else {
            return NULL;
        }
    }
    
    public static function getInfoJeedom($_ipRoute){ 
        log::add('scan_ip', 'debug', 'getInfoJeedom :. Lancement');

        exec('sudo ip a', $list);

        foreach ($list as $i => $value) {
            if(preg_match(self::getRegex("ip_v4"), $value) AND preg_match("(".self::getPlageIp($_ipRoute).")", $value)) {
                $return["ip_v4"] = trim(str_replace("inet", "", explode("/",$value)[0]));
                $return["mac"] = strtoupper(trim(str_replace("link/ether", "", explode("brd",$list[$i-1])[0])));
                $return["name"] = config::byKey('name');
                break;
            }
        }
        return $return;
    }
    
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
    
    public static function getPlageIp($_ip){
        list($a, $b, $c) = explode('.', $_ip);
        return $a . "." . $b . "." . $c;
    }
    
    public static function getRegex($_type){
        if($_type == "ip_v4") { return "/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/"; }
        elseif($_type == "mac") { return "/([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/"; }
        elseif($_type == "sub_reseau") { return "/\d{1,3}\:\s(.+)\:\s/"; }
        elseif($_type == "()") { return "/(\((.*?)\))/"; }
        else { return NULL; }
    }
    
    public static function getIpRoute(){
        log::add('scan_ip', 'debug', 'getIpRoute :. Lancement');
        return exec("ip route show default | awk '/default/ {print $3}'");
    }
    
    public static function getAlleqLogics(){
        log::add('scan_ip', 'debug', 'getAlleqLogics :. Lancement');
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            $return[$scan_ip->getConfiguration("adress_mac")]["name"] = $scan_ip->name;
            $return[$scan_ip->getConfiguration("adress_mac")]["enable"] = $scan_ip->getIsEnable();
            $return[$scan_ip->getConfiguration("adress_mac")]["offline_time"] = $scan_ip->getConfiguration("offline_time", self::$_defaut_offline_time);
        }
        log::add('scan_ip', 'debug', 'getAlleqLogics :. "' . $scan_ip->name .'"');
        return $return;
    }
    
    public static function isOffline($_expire = NULL, $_time){
        if($_expire == NULL){ $_expire = 4; }
        $expire = time() - (60 * $_expire);
        if($expire <= $_time){ return 0; } 
        else { return 1; }
    }
    
    public static function cmdRefresh($eqlogic, $_mapping = NULL){

        log::add('scan_ip', 'debug', 'cmdRefresh :. Lancement');
        
        if($_mapping == NULL){
            $_mapping = self::getJson(self::$_jsonMapping);
        }

        $device = self::searchByMac($eqlogic->getConfiguration("adress_mac"), $_mapping);
        $offline_time = $eqlogic->getConfiguration("offline_time", self::$_defaut_offline_time);
              
        if(self::isOffline($offline_time, $device["time"]) == 0){
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
        
        self::majElementsAssocies($eqlogic, $device);
        
        // Mise à jour de l'élément associé
        ///////////////////////////////////////////
        
        if(!empty($device["time"])) {
            $eqlogic->checkAndUpdateCmd('update_time', $device["time"]);
            $eqlogic->checkAndUpdateCmd('update_date', date("d/m/Y H:i:s", $device["time"]));
        } else {
            $eqlogic->checkAndUpdateCmd('update_time', NULL);
            $eqlogic->checkAndUpdateCmd('update_date', NULL);
        }
        
        $eqlogic->toHtml('dashboard');
        $eqlogic->refreshWidget();
    }
    
    public static function majElementsAssocies($eqlogic, $device){
        log::add('scan_ip', 'debug', 'majElementsAssocies :. Lancement');
        $deamons = NULL;
        
        $bridges = self::bridges_getElements();
        
        if($bridges != FALSE){ 
            for ($index = 1; $index <= self::$_defaut_bridges_by_equipement; $index++) {
                
                $plug_element_plugin = $eqlogic->getConfiguration("plug_element_plugin_".$index);
                
                if($plug_element_plugin != ""){
                    
                    $testBridge = explode("|", $plug_element_plugin);
                    
                    if(self::bridges_pluginExists($testBridge[0])){
                        if(self::bridges_existId($testBridge[1]) == TRUE){
                            if($device["ip_v4"] != "" AND $plug_element_plugin != ""){ 
                                $add_deamon = self::bridges_majElement($device["ip_v4"], $plug_element_plugin);
                                $deamons = self::arrayCompose($deamons, $add_deamon);
                            }
                        } else {
                            $eqlogic->setConfiguration("plug_element_plugin_".$index, "");
                            $eqlogic->save();
                        }
                    } else {
                        log::add('scan_ip', 'debug', 'cmdRefresh :. Suppression du bridge car le plugin "'.$testBridge[0].'" n\'est pas installé');
                        $eqlogic->setConfiguration("plug_element_plugin_".$index, "");
                        $eqlogic->save();
                    } 
                }
                
            }  
            
            self::bridges_startDeamons($deamons);
        }
    }
    
    public static function getCommande($_ComName, $_this){
        $tmp_cmd = $_this->getCmd(null, $_ComName);
        return (is_object($tmp_cmd)) ? $tmp_cmd->execCmd() : '';
    }
           
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# ELEMENTS DE VUES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

    public static function excludeSubReseau($_string){
        if($_string == "lo"){ return FALSE; }
        elseif(preg_match('/(tun)[0-9]*/', $_string)){ return FALSE; }
        else { return TRUE; }
    }
    
    public static function printInputSubConfig(){
        log::add('scan_ip', 'debug', 'printInputSubConfig :. Lancement');
        $return = "";
        foreach (self::scanSubReseau() as $sub) {
            if(self::excludeSubReseau($sub["name"]) == TRUE) {
                $return .= '<div class="form-group"">';
                $return .= '<label class="col-sm-4 control-label">{{Scanner le sous-réseau ['.$sub["name"].']}} </label>';
                $return .= '<div class="col-sm-2">';
                $return .= '<input type="checkbox" class="configKey" data-l1key="sub_enable_'.md5($sub["name"]).'"><span style="font-weight: bold;">'.$sub["ip_v4"].'</span>';
                $return .= '</div>';
                $return .= '</div>';
            }
        }
        return $return;
    }
    
    public static function printSelectOptionAdressMac($_selected = NULL){
        log::add('scan_ip', 'debug', 'printSelectOptionAdressMac :. Lancement');
        $record = self::getAlleqLogics();
        $list = self::getJson(self::$_jsonMapping);
        $print = "";
        foreach ($list["sort"] as $value) {
            if(empty($record[$value["mac"]])){
                $print .= '<option value="'. $value["mac"] .'"';
                if($_selected != NULL AND $_selected == $value["mac"]) { $print .= ' selected'; }
                $print .= '>' . $value["mac"] . ' | ' . $value["ip_v4"] . ' | '. $value["equipement"] .'</option>';
            }
        }  
        echo $print;
    }
    
    public static function showEquipements(){
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'showEquipements :. Lancement');
        
        $allEquipementsBridges = self::bridges_getEquiementsById();
        
        $a = 0;
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            $return[$a]["name"] = $scan_ip->name;
            $return[$a]["link"] = "<a href='/index.php?v=d&m=scan_ip&p=scan_ip&id=".$scan_ip->getId()."'>".$scan_ip->name."</a>";
            $return[$a]["mac"] = $scan_ip->getConfiguration("adress_mac");
            $return[$a]["ip_v4"] = self::getCommande('ip_v4', $scan_ip);
            $return[$a]["last_ip_v4"] = self::getCommande('last_ip_v4', $scan_ip);
            $return[$a]["update_date"] = self::getCommande('update_date', $scan_ip);
            $return[$a]["on_line"] = self::getCommande('on_line', $scan_ip);
            
            $return[$a]["plug_element_plugin"] = NULL;
            
            $bridge = self::bridges_getElements();
            
            if($bridge != FALSE){
                for ($index = 1; $index <= self::$_defaut_bridges_by_equipement; $index++) {
                    if(!empty($scan_ip->getConfiguration("plug_element_plugin_".$index))){
                        $split = explode("|", $scan_ip->getConfiguration("plug_element_plugin_".$index));
                        if(!empty($allEquipementsPlugs[$split[1]]["name"])){
                            $equipement = $allEquipementsPlugs[$split[1]]["name"];
                        } else {
                            $equipement = "";
                        }
                        $return[$a]["plug_element_plugin"] .= "<div><a href='/index.php?v=d&m=".$split[0]."&p=".$split[0]."&id=".$split[1]."' target='_blank'>#".$split[1]." (".$split[0].") ".$equipement."</a></div>";
                    } 
                }
            }
            $a++;
        }  
       return $return;
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
    
    public static function printShell($_shell){
        $output = shell_exec($_shell);
        echo "<pre style='background-color: #1b2426 !important; color: white !important;'>".$output."</pre>";
    }
    
    public static function printArray($_array){
        echo "<pre style='background-color: #1b2426 !important; color: white !important;'>".print_r($_array, true)."</pre>";
    }
    
    public static function majNetworkCommentaires($_array){
        self::createJsonFile(self::$_jsonCommentairesEquipement, $_array);
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# ELEMENTS DE VUES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU WIDGET
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public function toHtml($_version = 'dashboard') {

        log::add('scan_ip', 'debug', 'toHtml :.  Lancement');

        $replace = $this->preToHtml($_version); //récupère les informations de notre équipement

        if (!is_array($replace)) {
            return $replace;
        }

        $this->emptyCacheWidget(); //vide le cache. Pratique pour le développement
        $version = jeedom::versionAlias($_version);

        $replace["#ip_v4#"] = self::getCommande('ip_v4', $this);
        if($replace["#ip_v4#"] == ""){ $replace["#ip_v4#"] = "..."; }
        
        if(!empty(self::getCommande('last_ip_v4', $this))){ $replace["#last_ip_v4#"] = self::getCommande('last_ip_v4', $this); } 
        else { $replace["#last_ip_v4#"] = "..."; }
        
        if(!empty(self::getCommande('update_date', $this))){ $replace["#update_date#"] = self::getCommande('update_date', $this); } 
        else { $replace["#update_date#"] = "..."; }

        $replace["#mac#"] = $this->getConfiguration("adress_mac");

        if($replace["#ip_v4#"] == "..."){ $replace["#etat_cycle#"] = "red"; } 
        else{ $replace["#etat_cycle#"] = "#50aa50"; } 

        if($replace["#last_ip_v4#"] != $replace["#ip_v4#"] AND $replace["#ip_v4#"] != "..."){ $replace["#etat_last_ip#"] = ' color:orange;'; } 
        else { $replace["#etat_last_ip#"] = ''; }
        
        $wol = $this->getCmd(null,'wol');
        $replace['#cmdWol#'] = (is_object($wol)) ? $wol->getId() : '';
        
        if($this->getConfiguration("enable_wol") == 0){ $replace['#enableWol#'] = "display:none;"; }
        else { $replace['#enableWol#'] = ""; }
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        return template_replace($replace, getTemplate('core', $version, 'scan_ip', 'scan_ip'));
        
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU WIDGET
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function arpScanShell($_subReseau = NULL){
        log::add('scan_ip', 'debug', 'arpScanShell :. Lancement');
        $time = time();
        $return = array();
        
        if($_subReseau == NULL){
            exec('sudo arp-scan --localnet --ouifile=' . self::$_file_oui. ' --iabfile=' .  self::$_file_iab, $output );
        } else {
            exec('sudo arp-scan --interface=' . $_subReseau . ' --localnet --ouifile=' . self::$_file_oui. ' --iabfile=' .  self::$_file_iab, $output);
        }
        
        foreach ($output as $scanLine) {
            if (preg_match(self::getRegex("ip_v4"), $scanLine)) { 
                
                    preg_match(self::getRegex("ip_v4"), $scanLine, $sortIp); 
                    preg_match(self::getRegex("mac"), $scanLine, $sortMac);
                    
                    $mac = strtoupper($sortMac[0]);
                    
                    $return[$mac]["equipement"] = self::getEquipement($scanLine);
                    $return[$mac]["ip_v4"] = $sortIp[0];
                    $return[$mac]["time"] = $time;
            }
        } 
        
        return $return;
    }
    
    public static function getEquipement($_scanLine){
        $equipement = preg_replace(self::getRegex("ip_v4"), "", $_scanLine);
        $equipement = preg_replace(self::getRegex("mac"), "", $equipement);
        $equipement = preg_replace(self::getRegex("()"), "", $equipement);
        $equipement = trim($equipement);
        if($equipement == ""){ return "..."; }
        else { return utf8_encode($equipement); }
    }
    
    public static function arpVersion(){
        log::add('scan_ip', 'debug', 'arpVersion :. Lancement');
        $exec = exec('sudo arp-scan -V 2>&1',$output, $return_var);
        if($return_var == 0) { 
            foreach ($output as $searchVersion) {
                if(preg_match("(arp-scan )", $searchVersion)) { return substr($searchVersion,9); }
            }    
        } else {
          log::add('scan_ip', 'error', 'sudo: arp-scan: command not found');
          return("arp-scan not found");
        }
    }
    
    public static function printFileOuiExist(){
        if(@file_exists(self::$_file_oui) == TRUE){
            return "<span style='color:green'>Installé</span>";
        } else {
            return '<a class="btn btn-danger btn-sm" onclick= "recordBtMac()" style="position:relative;top:-5px;"><i class="fas fa-paperclip"></i> Fichier Manquant. Cliquez ici pour le télécharger.</a>';
        }
    }
    
    public static function getDateFile($_filename){
        if (@file_exists($_filename)) {
            return date("d/m/Y", filemtime($_filename));
        } else {
            return NULL;
        }
        
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function cron() {
  
        $cronConfig = config::byKey('cron_pass', 'scan_ip', 1);

        
        if((date('i') % $cronConfig) == 0) {
            
            if(self::lockProcess() == TRUE){
                ////////////////////////////////////////////////////////////////////
                log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
                log::add('scan_ip', 'debug', 'CRON :. START');
                log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
                
                log::add('scan_ip', 'debug', 'cron :. Configuration Minute : '. $cronConfig);
                
                self::syncScanIp(self::getJson(self::$_jsonMapping));
                self::unlockProcess();
                
                log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
                log::add('scan_ip', 'debug', 'CRON :. FIN');
                log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
            } else {
                log::add('scan_ip', 'debug', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
                log::add('scan_ip', 'debug', '!! cron :. Annulé parce qu\'il y a déjà un processus en cours');
                log::add('scan_ip', 'debug', '!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
            } 
            
        } else {
            log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
            log::add('scan_ip', 'debug', 'cron :. Annulé :. Configuration Minute : '. $cronConfig);
            log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
        }
        

        
    }
    
    public static function cronDaily() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'cronDaily :. START');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        
        self::downloadOui();
        self::downloadIab();
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'cronDaily :. FIN');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DES JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
    public static function lockProcess(){
        if(file_exists(self::$_file_lock)) {
            if((time() - filemtime (self::$_file_lock)) > 180){
                self::unlockProcess();
            } else {
               return FALSE; 
            }
        } else {
            fopen(self::$_file_lock, "w");
            chmod(self::$_file_lock, 0777);
            return TRUE;
        } 
        return TRUE;
    }
    
    public static function unlockProcess(){
        if(file_exists(self::$_file_lock)) {
            unlink(self::$_file_lock);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function recordInJson($_file, $_data) {
        log::add('scan_ip', 'debug', 'recordInJson :.  Lancement');
        
        self::prepareJsonFolder();
        self::createJsonFile($_file, $_data);

        log::add('scan_ip', 'debug', 'recordInJson :. Enregistrement du Json : mapping.json');
    }
    
    public static function getJson($_file) {
        log::add('scan_ip', 'debug', 'getJson :. Lancement');
        $return = json_decode(file_get_contents($_file.".json"),true);
        log::add('scan_ip', 'debug', 'getJson :. Chargement du Json Mapping');
        return $return;
    }
    
    public static function prepareJsonFolder(){
        log::add('scan_ip', 'debug', 'prepareJsonFolder :. Lancement');
        if (!is_dir(self::$_folderJson)) {
            log::add('scan_ip', 'debug', 'miseEnCacheJson :.  Création du dossier :' . self::$_folderJson);
            mkdir(self::$_folderJson, 0777);
        }
    }
    
    public static function createJsonFile($_file, $_data){
        log::add('scan_ip', 'debug', 'createJsonFile :. Lancement');
        
        $fichier = fopen($_file.'.temp', 'w');
        fputs($fichier, json_encode($_data));
        fclose($fichier);

        unlink($_file.'.json');
        rename($_file.'.temp', $_file.'.json');
        chmod($_file.'.json', 0777);
    }
    
//    public static function createJsonEquipements($_data){ // Ynats Go
//        log::add('scan_ip', 'debug', 'createJsonEquipements :. Lancement');
//        
//        $fichier = fopen(self::$_jsonEquipement, 'w');
//        fputs($fichier, json_encode($_data));
//        fclose($fichier);
//
//        chmod(self::$_jsonEquipement, 0777);
//    }    
    
//    public static function getJsonEquipements(){
//        log::add('scan_ip', 'debug', 'getJsonEquipements :. Lancement');
//        return json_decode(file_get_contents(self::$_jsonEquipement));
//    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DES JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DES SOUS-RESEAUX
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      
    
    public static function scanSubReseau($_ipRoute = NULL){ 
        log::add('scan_ip', 'debug', 'scanSubReseau :. Lancement');
        
        if($_ipRoute != NULL){
            $ipRoute = $_ipRoute;
        } else {
            $ipRoute = self::getIpRoute();
        }
        
        exec('sudo ip a', $list);
        $i = 0;
        
        foreach ($list as $value) {
            if(preg_match(self::getRegex("sub_reseau"), $value)){ 
                $name = trim(explode(":", $value)[1]);
                $i++; 
                $return[$i]["name"] = $name; 
            }
            if(preg_match(self::getRegex("ip_v4"), $value) AND preg_match("(".self::getPlageIp($ipRoute).")", $value)) {
                $return[$i]["ip_v4"] = self::getPlageIp(trim(str_replace("inet", "", explode("/",$value)[0]))).".*";
            } else {
                $return[$i]["ip_v4"] = NULL;
            }
        }
        return $return;
    }
     
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DES SOUS-RESEAUX
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# BRIDGES PLUG AND PLAY
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
//    public static function bridges_allBridges(){ // Ynats
//        $all = scandir(__DIR__ . "/../../../../plugins/scan_ip/core/bridges/");
//        foreach ($all as $bridge) {
//            if(!in_array($bridge, self::$_exceptionBridges()) AND $bridge != "." AND $bridge != ".."){
//                $return[] = str_replace(".php", "", $bridge);
//            }
//        }
//        return $return;
//    }
    
    public static function bridges_printPlugs($_nb = 100, $_start = 0){     
        $i =1;
        $allBridges = self::getJsonBridges();
        natcasesort($allBridges);
        foreach ($allBridges as $gridge) {      
            if($i > $_start AND $i <= ($_start + $_nb)) {
                if(self::bridges_pluginExists($gridge)){
                    echo "<div><span style='font-weight: bold;'>".$gridge."</span> <span style='color:green;'>(Installé)</span></div>";
                } else {
                    echo "<div><span style='font-weight: bold;'>".$gridge."</span> <span style='color:orange;'>(Non installé)</span></div>";
                }
            }
            $i++;
        }
    }
    
    public static function getJsonBridges(){
        return json_decode(file_get_contents(self::$_jsonBridges),true);
    }
    
    public static function bridges_require($_gridge){
        require_once(__DIR__ . "/../../../../plugins/scan_ip/core/bridges/".$_gridge.".php");
    }
    
    public static function bridges_getPlugsElements($_bridges){
        self::bridges_require($_bridges);
        $class = "scan_ip_".$_bridges;
        $Get = new $class;
        return $Get->getAllElements();
    }
    
    public static function bridges_getElements(){
        $array = NULL;
        $i = 0; 
        foreach (self::getJsonBridges() as $bridges) {
            if(self::bridges_pluginExists($bridges) == TRUE){
                $mergeArray = self::bridges_getPlugsElements($bridges);
                if(is_array($mergeArray)){
                    $i++;
                    $array = self::arrayCompose($array, $mergeArray);
                }
            }
        }
        if(!is_array($array) AND $array != NULL){
            return FALSE;
        } else {
            $return["array"] = $array;
            $return["nb"] = $i;
            return $return;
        }
    }
    
    public static function bridges_majElement($_ip, $_element){
        $plug = explode("|", $_element);
        self::bridges_require($plug[0]);
        $class = "scan_ip_".$plug[0];
        $Maj = new $class;
        return $Maj->majIpElement($_ip, $plug[1]);
    }
    
    public static function bridges_printSelectOptionEquiements(){
        
        $allBridges = self::bridges_getElements();
        
        if($allBridges != FALSE){
            $print = $oldEquip = "";
            foreach ($allBridges["array"] as $equipement) {
                $print .= "<option value=\"". $equipement["plugin"] ."|".$equipement["id"] ."\">[ " . $equipement["plugin_print"] . " ][ ". $equipement["ip_v4"] ." ] " . $equipement["name"] . "</option>";
                $oldEquip = $equipement["plugin"];
            }
            return $print;
        } else {
            return FALSE;
        }
        
    }
    
    public static function bridges_printOptionEquiements(){
        
        $selection = scan_ip::bridges_printSelectOptionEquiements();
        
        if($selection != FALSE){
            for ($index = 1; $index <= self::$_defaut_bridges_by_equipement; $index++) {
                echo '<div class="form-group">';
                echo '<label class="col-sm-3 control-label">{{Association '.$index.'}}</label>';
                echo '<div class="col-sm-5">';
                echo '<select class="form-control eqLogicAttr" onchange="verifEquipement('. self::$_defaut_bridges_by_equipement .')" data-l1key="configuration"  data-l2key="plug_element_plugin_'.$index.'" id="plug_element_plugin_'.$index.'">';
                echo '<option value="">Sélectionnez un élément</option>';
                echo $selection;
                echo '</select>';
                echo '</div>';
                echo '</div>';
            }
        } else {
                echo '<div class="form-group">';
                echo '<label class="col-sm-3 control-label">{{Association 1}}</label>';
                echo '<div class="col-sm-5">';
                echo '<input class="form-control" style="color:var(--al-warning-color) !important;" type="text" value="Aucun élément compatible n\'est associé aux bridges." readonly="">';
                echo '</div>';
                echo '</div>';
        }
        
    }
                        
    public static function bridges_getEquiementsById(){
        log::add('scan_ip', 'debug', 'bridges_getEquiementsById :. Lancement'); 
        $all = self::bridges_getElements();
        foreach ($all["array"] as $equipement) { 
            $return[$equipement["id"]]["name"] = $equipement["name"];
            $return[$equipement["id"]]["ip_v4"] = $equipement["ip_v4"];
            $return[$equipement["id"]]["plugin"] = $equipement["plugin"];
        }  
        return $return;
    }
    
    public static function bridges_existId($_id){
        $bridgeExist = TRUE;
        try {
            $return = eqLogic::byId($_id);
        } catch (Exception $e) {
            $bridgeExist = FALSE;
        }
        return $bridgeExist;
    }
    
    public static function bridges_getAllAssignEquipement($_ouput = NULL){ 
        log::add('scan_ip', 'debug', 'bridges_getAllAssignEquipement :. Lancement');
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) { 
            $mac = $scan_ip->getConfiguration("adress_mac");
            for($i = 1; $i <= self::$_defaut_bridges_by_equipement; $i++){ 
                $plug = $scan_ip->getConfiguration("plug_element_plugin_".$i);
                if(!empty($plug)) {
                    $return[$mac][] = $plug;
                }
            }  
        }
        
        if($_ouput == "json" and !empty($return)){
            return json_encode($return);
        } else {
            return $return;
        }
    }
    
    public static function bridges_pluginExists($_name) {
        //log::add('scan_ip', 'debug', 'bridges_pluginExists :. Lancement');
        $bridgeExists = TRUE;
        try {
            $plugin = plugin::byId($_name);
        } catch (Exception $e) {
            $bridgeExists = FALSE;
        }
        return $bridgeExists;
    }
    
    public static function bridges_startDeamons($_deamons = NULL){
        if($_deamons != NULL){
            foreach ($_deamons as $deamon) {
                log::add('scan_ip', 'debug', 'cmdRefresh :. Lancement du deamon "'.$deamon.'"');
                $deamon::deamon_start();
            }
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# BRIDGES PLUG AND PLAY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
  
    public static function wakeOnLan($_mac){
        log::add('scan_ip', 'debug', 'wakeOnLan :. wakeonlan '.$_mac);
        shell_exec("sudo wakeonlan ".$_mac);
        log::add('scan_ip', 'debug', 'etherwake :. etherwake '.$_mac);
        shell_exec("sudo /usr/sbin/etherwake ".$_mac);
    }
    
    public static function wakeOnLanByCmd($_eqlogic){ 
        log::add('scan_ip', 'debug', 'wakeOnLanByCmd :. Lancement');
        $mac = $_eqlogic->getConfiguration("adress_mac");
        if(preg_match(self::getRegex("mac"), $mac)){
            self::wakeOnLan($mac);
        } else {
            
        }
    }
  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
   
    public static function downloadOui(){
        try {
            shell_exec(self::$_bash_oui);
        } catch (Exception $e) {
            log::add('scan_ip', 'debug', 'downloadOui :. ' .  $e->getMessage());
        }
    }
    
    public static function downloadIab(){
        try {
            shell_exec(self::$_bash_iab);
        } catch (Exception $e) {
            log::add('scan_ip', 'debug', 'downloadOui :. ' .  $e->getMessage());
        }
    }
    
    public static function dependancy_info() {
        $error = 0;
        $return = array();
        $return['state'] = 'nok';
        $return['log'] = 'scan_ip_update';
        $return['progress_file'] = jeedom::getTmpFolder('scan_ip') . '/dependance';

        if (self::arpVersion() != "arp-scan not found") {
            if (exec('which etherwake | wc -l') == 1 || exec('which wakeonlan | wc -l') == 1) {
                if (exec(" dpkg --get-selections | grep -v deinstall | grep -E 'wakeonlan|etherwake' | wc -l") == 2) {
                    $return['state'] = 'ok';
                }
            }
        }

        return $return;
    }

    public static function dependancy_install() {
        log::remove(__CLASS__ . '_update');
        return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('scan_ip') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
    }
    
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# DEAMON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
    
//    public static function deamon_info() {
//        
//    }   
//
//    public static function deamon_start() {
//        
//    }  
//
//    public static function deamon_stop() {
//
//    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# DEAMON
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
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
            
    
    
}

class scan_ipCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'execute :. Lancement');
        
        $eqlogic = $this->getEqLogic();
        switch ($this->getLogicalId()) { //vérifie le logicalid de la commande 			
            case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave 
                log::add('scan_ip', 'debug', 'execute :. Lancement de la commande refresh : #ID#' . $eqlogic->getId());
                $mapping = scan_ip::getJson(self::$_jsonMapping);
                scan_ip::cmdRefresh($eqlogic, $mapping);
                log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
                break;
            case 'wol': 
                log::add('scan_ip', 'debug', 'execute :. Lancement de la commande wol : #ID#' . $eqlogic->getId());
                scan_ip::wakeOnLanByCmd($eqlogic);
                log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
                break;
        }
    }
    
    

    /*     * **********************Getteur Setteur*************************** */
}
