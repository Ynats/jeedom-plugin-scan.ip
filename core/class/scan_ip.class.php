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
    public static $_folderTampon = __DIR__ . "/../../../../plugins/scan_ip/core/json/";
    public static $_jsonTamponTemp = __DIR__ . "/../../../../plugins/scan_ip/core/json/mapping.temp";
    public static $_jsonTampon = __DIR__ . "/../../../../plugins/scan_ip/core/json/mapping.json";
    public static $_serializeTampon = __DIR__ . "/../../../../plugins/scan_ip/core/json/serialize.temp";
    public static $_serializeMacAddress = __DIR__ . "/../../../../plugins/scan_ip/core/json/macaddress.temp";
    public static $_timeRefreshMacAddress = 86400 * 7; // Refresh des équipements nons reconnus tous les 7 jours
    
    public static $_allBridges = array( "xiaomihome",
                                        "broadlink",
                                        "googlecast",
                                        "homepTalk",
                                        "camera",
                                        "networks",
                                        "kodi",
                                        "zigate");

    /*     * ***********************Methode static*************************** */

//    public function preInsert() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preInsert :. Lancement');
//    }
//
//    public function postInsert() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'postInsert :. Lancement');
//    }
//
//    public function preSave() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preSave :. Lancement');
//    }

    public function postSave() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postSave :. Mise à jour de : ' . $this->getId());
                
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


        $refresh = $this->getCmd(null, 'on_line');
        if (!is_object($refresh)) {
            $refresh = new scan_ipCmd();
            $refresh->setName(__('Online', __FILE__));
        }
        $refresh->setEqLogic_id($this->getId());
        $refresh->setLogicalId('on_line');
        $refresh->setType('info');
        $refresh->setSubType('binary');
        $refresh->save();
        
        
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
        
        // Mise à jour des données
        self::cmdRefresh($this);

        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }

//    public function preUpdate() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'preUpdate :. lancement');
//    }
//
//    public function postUpdate() {
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'postUpdate :. lancement');
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
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
      public static function preConfig_<Variable>() {
      }
     */

    /*     * **********************Getteur Setteur*************************** */
    
    public static function getSubReseauEnable() {
        log::add('scan_ip', 'debug', 'getSubReseauEnable :. Lancement');
        
        $a = $return["subReseauEnable"] = 0;
        foreach (self::scanSubReseau() as $sub) { 
            $return["subReseau"][$a]["enable"] = config::byKey('sub_enable_'.md5($sub["name"]), 'scan_ip', 0);
            $return["subReseau"][$a]["name"] = $sub["name"];
            $return["subReseauEnable"] =  $return["subReseau"][$a]["enable"] + $return["subReseauEnable"];
            $a++;
        }
        
        if($return["subReseauEnable"] == 0){
            log::add('scan_ip', 'error', "Aucun réseau n'est activé. Allez dans la configuration du plugin pour activer un réseau.");
        }
        return $return; 
    }
    
    public static function syncScanIp(){
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'syncScanIp :. Lancement du scan du réseau');
        self::scanReseau();
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                log::add('scan_ip', 'debug', 'syncScanIp :. cmdRefresh('.$scan_ip->getId().')');
                self::cmdRefresh($scan_ip);
            }
        }  
    }
    
    public static function scanReseau(){
        log::add('scan_ip', 'debug', 'scanReseau :. Lancement');
        
        $subReseau = self::getSubReseauEnable();
        $ipRoute = self::getIpRoute();
        $infoJeedom = self::getInfoJeedom($ipRoute);
        
        if($subReseau["subReseauEnable"] > 0){
            $new = array();
            foreach ($subReseau["subReseau"] as $sub) { 
                if($sub["enable"] == 1){
                    $scanResult = self::arpScanShell($sub["name"]);
                    $new = array_merge($new, $scanResult);
                }
            }
            if(count($new) == 0){
                log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
                log::add('scan_ip', 'debug', "-----------------------------------------------------------------------------------------------------");
                log::add('scan_ip', 'debug', serialize($new));
                log::add('scan_ip', 'debug', "-----------------------------------------------------------------------------------------------------");
                exit();
            } 
            else {
            
                $old = self::getFileSerialize();

                if(empty($old) OR count($old) == 0){ $now = $new; } 
                else { $now = array_merge($old, $new); } 

                self::createFileSerialize($now);

                foreach ($now as $mac => $scanLine) {
                    if($scanLine["ip_v4"] == $ipRoute){
                        $now["route"]["ip_v4"] = $scanLine["ip_v4"];
                        $now["route"]["mac"] = $mac;
                    } else {
                        $now["sort"][explode(".",$scanLine["ip_v4"])[3]] = array("ip_v4" => $scanLine["ip_v4"], "mac" => $mac, "time" => $scanLine["time"]);
                        $now["byIpv4"][$scanLine["ip_v4"]] = array("mac" => $mac, "time" => $scanLine["time"]);
                        $now["byMac"][$mac] = array("ip_v4" => $scanLine["ip_v4"], "time" => $scanLine["time"]);           
                    }
                }

                ksort($now["sort"]);
            }
        }
        else {
            log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
            log::add('scan_ip', 'debug', "-----------------------------------------------------------------------------------------------------");
            log::add('scan_ip', 'debug', serialize($subReseau["subReseau"]));
            log::add('scan_ip', 'debug', "-----------------------------------------------------------------------------------------------------");
        }

        $now["jeedom"] = $infoJeedom; 
        $now["infos"]["version_arp"] = self::arpVersion();
        $now["infos"]["time"] = time();
        $now["infos"]["date"] = date("d/m/Y H:i:s", $now["infos"]["time"]);
        
        self::recordInJson(json_encode($now));
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $now["infos"]["version_arp"] . ']');
    }
     
    public static function searchByMac($_searchMac){ 
        log::add('scan_ip', 'debug', 'searchByMac :. Lancement');
        $sort = (array) self::getJsonTampon();
        if(!empty($sort["byMac"]->{$_searchMac}->ip_v4)){
            $return["ip_v4"] = $sort["byMac"]->{$_searchMac}->ip_v4;
            $return["time"] = $sort["byMac"]->{$_searchMac}->time;
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
    
    public static function getPlageIp($_ip){
        list($a, $b, $c) = explode('.', $_ip);
        return $a . "." . $b . "." . $c;
    }
    
    public static function getRegex($_type){
        if($_type == "ip_v4") { return "/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/"; }
        elseif($_type == "mac") { return "/([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/"; }
        elseif($_type == "sub_reseau") { return "/\d{1,3}\:\s(.+)\:\s/"; }
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
        }
        return $return;
    }
    
    public static function isOffline($_time){
        $expire = time() - (60 * config::byKey('offline_time', 'scan_ip', 4));
        if($expire <= $_time){ return 0; } 
        else { return 1; }
    }
    
    public static function cmdRefresh($eqlogic){
        log::add('scan_ip', 'debug', 'cmdRefresh :. Lancement');
        $device = self::searchByMac($eqlogic->getConfiguration("adress_mac"));
        $offline_time = $eqlogic->getConfiguration("offline_time");
        
        
        if(self::isOffline($device["time"]) == 0){
            
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
        
        $bridge = self::bridges_getElements();
        
        if($bridge != FALSE){
            for ($index = 1; $index <= $bridge["nb"]; $index++) {
                $plug_element_plugin = $eqlogic->getConfiguration("plug_element_plugin_".$index);
                
                if($plug_element_plugin != ""){
                    
                    $testBridge = explode("|", $plug_element_plugin);
                    
                    if(self::bridges_pluginExists($testBridge[0])){
                        if(self::bridges_existId($testBridge[1]) == TRUE){
                            if($device["ip_v4"] != "" AND $plug_element_plugin != ""){ 
                                self::bridges_majElement($device["ip_v4"], $plug_element_plugin);
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
        }
        
        // Mise à jour de l'élément associé
        ///////////////////////////////////////////
        
        $eqlogic->checkAndUpdateCmd('update_time', $device["time"]);
        $eqlogic->checkAndUpdateCmd('update_date', date("d/m/Y H:i:s", $device["time"]));
        
        $eqlogic->toHtml('dashboard');
        $eqlogic->refreshWidget();
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
        $record = self::getAlleqLogics();
        $list = (array) self::getJsonTampon();
        $print = "";
        foreach ($list["sort"] as $value) {
            if(empty($record[$value->mac])){
                $print .= '<option value="'. $value->mac .'"';
                if($_selected != NULL AND $_selected == $value->mac) { $print .= ' selected'; }
                $print .= '>' . $value->mac . ' | ' . $value->ip_v4 . ' | '. self::showMacVendor($value->mac) .'</option>';
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
                for ($index = 1; $index <= $bridge["nb"]; $index++) {
                    if(!empty($scan_ip->getConfiguration("plug_element_plugin_".$index))){
                        $split = explode("|", $scan_ip->getConfiguration("plug_element_plugin_".$index));

                        $return[$a]["plug_element_plugin"] .= "<div><a href='/index.php?v=d&m=".$split[0]."&p=".$split[0]."&id=".$split[1]."' target='_blank'>#".$split[1]." (".$split[0].") ".$allEquipementsPlugs[$split[1]]["name"]."</a></div>";
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

        $replace["#last_ip_v4#"] = self::getCommande('last_ip_v4', $this);
        $replace["#update_date#"] = self::getCommande('update_date', $this);

        $replace["#mac#"] = $this->getConfiguration("adress_mac");

        if($replace["#ip_v4#"] == "..."){
            $replace["#etat_cycle#"] = "red";
        } else{
            $replace["#etat_cycle#"] = "#50aa50";
        } 

        if($replace["#last_ip_v4#"] != $replace["#ip_v4#"] AND $replace["#ip_v4#"] != "..."){
            $replace["#etat_last_ip#"] = ' color:orange;';
        } else {
            $replace["#etat_last_ip#"] = '';
        }

        return template_replace($replace, getTemplate('core', $version, 'scan_ip', 'scan_ip'));
        
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU WIDGET
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function arpScanShell($_subReseau){
        log::add('scan_ip', 'debug', 'arpScanShell :. Lancement');
        $time = time();
        $return = array();
        exec('sudo arp-scan --interface='.$_subReseau.' --localnet', $output);
        
        foreach ($output as $scanLine) {
            if (preg_match(self::getRegex("ip_v4"), $scanLine)) {
                preg_match(self::getRegex("ip_v4"), $scanLine, $sortIp); 
                preg_match(self::getRegex("mac"), $scanLine, $sortMac);
                $mac = strtoupper($sortMac[0]);
                $return[$mac]["ip_v4"] = $sortIp[0];
                $return[$mac]["time"] = $time;
            }
        } 
        return $return;
    }
    
    public static function arpVersion(){
        log::add('scan_ip', 'debug', 'arpVersion :. Lancement');
        $exec = exec('sudo arp-scan -V 2>&1',$output, $return_var);
        if($return_var == 0) { // execution commande OK
            foreach ($output as $searchVersion) {
                if(preg_match("(arp-scan )", $searchVersion)) { return substr($searchVersion,9); }
            }    
        } else {
          log::add('scan_ip', 'error', 'sudo: arp-scan: command not found');
          return("arp-scan not found");
        }
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function cron() {

        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'CRON :. START');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            
        $cronConfig = config::byKey('cron_pass', 'scan_ip', 1);
        log::add('scan_ip', 'debug', 'cron :. Configuration Minute : '. $cronConfig);
        
        if((date('i') % $cronConfig) == 0) {
          log::add('scan_ip', 'debug', 'cron :. Lancement');
          self::syncScanIp();
        } else {
            log::add('scan_ip', 'debug', 'cron :. Annulé');
        }
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'CRON :. FIN');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        
    }
    
    public static function cronDaily() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'cronDaily :. START');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        
        self::majMacVendorApi();
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'cronDaily :. FIN');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
    public static function recordInJson($_json) {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'recordInJson :.  Lancement');
        
        self::prepareJsonFolder();
        self::createJsonFile($_json);

        log::add('scan_ip', 'debug', 'recordInJson :. Enregistrement du Json : ' . self::$_jsonTampon);
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
    
    public static function getJsonTampon() {
        log::add('scan_ip', 'debug', 'getJsonTampon :. Lancement');
        $return = json_decode(file_get_contents(self::$_jsonTampon));
        log::add('scan_ip', 'debug', 'getJsonTampon :. Chargement du Json Mapping');
        return $return;
    }
    
    public static function prepareJsonFolder(){
        log::add('scan_ip', 'debug', 'prepareJsonFolder :. Lancement');
        if (!is_dir(self::$_folderTampon)) {
            log::add('scan_ip', 'debug', 'miseEnCacheJson :.  Création du dossier :' . self::$_folderTampon);
            mkdir(self::$_folderTampon, 0777);
        }
    }
    
    public static function createJsonFile($_json){
        log::add('scan_ip', 'debug', 'createJsonFile :. Lancement');
        
        $fichier = fopen(self::$_jsonTamponTemp, 'w');
        fputs($fichier, $_json);
        fclose($fichier);

        unlink(self::$_jsonTampon);
        rename(self::$_jsonTamponTemp, self::$_jsonTampon);
        chmod(self::$_jsonTampon, 0777);
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DES SOUS-RESEAUX
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      
    
    public static function scanSubReseau(){ 
        log::add('scan_ip', 'debug', 'scanSubReseau :. Lancement');
        
        $ipRoute = self::getIpRoute();
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
# GESTION CACHE SERIALIZE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
    
    public static function createFileSerialize($_data){
        log::add('scan_ip', 'debug', 'createFileSerialize :. Lancement');
        
        $fichier = fopen(self::$_serializeTampon, 'w');
        fputs($fichier, serialize($_data));
        fclose($fichier);

        chmod(self::$_serializeTampon, 0777);
    }
    
    public static function getFileSerialize(){
        log::add('scan_ip', 'debug', 'getFileSerialize :. Lancement');
        return unserialize(file_get_contents(self::$_serializeTampon));
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION CACHE SERIALIZE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# BRIDGES PLUG AND PLAY
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function bridges_printPlugs(){
        foreach (self::$_allBridges as $gridge) {
            if(self::bridges_pluginExists($gridge)){
                echo "<div><span style='font-weight: bold;'>".$gridge."</span> <span style='color:green;'>(Installé)</span></div>";
            } else {
                echo "<div><span style='font-weight: bold;'>".$gridge."</span> <span style='color:orange;'>(Non installé)</span></div>";
            }
        }
    }
    
    public static function bridges_require($_gridge){
        require_once(__DIR__ . "/../../../../plugins/scan_ip/core/bridges/".$_gridge.".php");
    }
    
    public static function bridges_getPlugsElements($_bridges){
        self::bridges_require($_bridges);
        $class = "scan_ip_".$_bridges;
        ${$_bridges} = new $class;
        return ${$_bridges}->getAllElements();
    }
    
    public static function bridges_getElements(){
        $array = array();
        $i = 0;
        foreach (self::$_allBridges as $bridges) {
            if(self::bridges_pluginExists($bridges) == TRUE){
                $mergeArray = self::bridges_getPlugsElements($bridges);
                if(is_array($mergeArray)){
                    $i++;
                    $array = array_merge($array, $mergeArray); 
                }
            }
        }
        if(empty($array[0])){
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
        ${$plug[0]} = new $class;
        ${$plug[0]}->majIpElement($_ip, $plug[1]);
    }
    
    public static function bridges_printSelectOptionEquiements(){
        
        $allBridges = self::bridges_getElements();
        
        if($allBridges != FALSE){
            $print = $oldEquip = ""; 
            foreach ($allBridges["array"] as $equipement) {
                if($equipement["plugin"] != $oldEquip){ 
                    if($oldEquip != ""){ 
                        $print .= "</optgroup>";
                    }
                    $print .= "<optgroup label=\"Plugin ".$equipement["plugin"]."\">";
                }
                $print .= "<option value=\"". $equipement["plugin"] ."|".$equipement["id"] ."\">[ " . $equipement["plugin_print"] . " ][ ". $equipement["ip_v4"] ." ] " . $equipement["name"] . "</option>";
                $oldEquip = $equipement["plugin"];
            }

            $print .= "</optgroup>";

            return $print;
        } else {
            return FALSE;
        }
        
    }
    
    public static function bridges_printOptionEquiements(){
        
        $selection = scan_ip::bridges_printSelectOptionEquiements();
        $nb = self::bridges_getElements()["nb"];
        
        if($selection != FALSE){
            for ($index = 1; $index <= $nb; $index++) {
                echo '<div class="form-group">';
                echo '<label class="col-sm-3 control-label">{{Association '.$index.'}}</label>';
                echo '<div class="col-sm-5">';
                echo '<select class="form-control eqLogicAttr" onchange="verifEquipement('.$nb.')" data-l1key="configuration"  data-l2key="plug_element_plugin_'.$index.'" id="plug_element_plugin_'.$index.'">';
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
                echo '<input class="form-control" style="var(--al-warning-color) !important" type="text" value="Aucune association possible car aucun plugin installé n\'est compatible" readonly="">';
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
    
    public static function bridges_getAllAssignEquipement(){
        log::add('scan_ip', 'debug', 'bridges_getAllAssignEquipement :. Lancement');
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            $tmp = $scan_ip->getConfiguration("plug_element_plugin");
            if(!empty($tmp)) {
                $return[] = $scan_ip->getConfiguration("plug_element_plugin");
            }
        }
        return $return;
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# BRIDGES PLUG AND PLAY
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GET OUI VENDOR MAC
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

    public static function majMacVendorApi(){
        log::add('scan_ip', 'debug', 'majMacVendorApi :. Lancement');
        $records = self::getFileSerialize(); 
        foreach ($records as $mac => $scanLine) {
            self::recordMacVendor($mac);
            sleep(1);
        }
        log::add('scan_ip', 'debug', 'majMacVendorApi :. Fin');
    }
    
    public static function getMacVendorApi($_mac) {
        log::add('scan_ip', 'debug', 'getMacVendorApi :. Lancement');
        $url = "https://macvendors.co/api/" . urlencode($_mac);
        $value = json_decode(file_get_contents($url))->result;
        
        if(!empty($value->company)){
            $return["company"] = $value->company;
            $return["type"] = $value->type;
            $return["country"] = $value->country;
            $return["last"] = time();
            
            return $return;
        } else {
            return "error";
        }
    }

    public static function recordMacVendor($_mac) {
        log::add('scan_ip', 'debug', 'recordMacVendor :. Lancement');
        $rest = substr($_mac, 0, 8); 
        $arayVendor = self::getMacRecord();
        $pass = 0;
        
        if($arayVendor[$rest]["company"] == "..." AND (time() - $arayVendor[$rest]["last"]) > self::$_timeRefreshMacAddress ){ 
            log::add('scan_ip', 'debug', 'recordMacVendor :. Refresh equipement');
            $result = self::getMacVendorApi($_mac);
            $pass = 1;
        } elseif(empty($arayVendor[$rest])){
            log::add('scan_ip', 'debug', 'recordMacVendor :. Search new equipement');
            $result = self::getMacVendorApi($_mac);
            $pass = 1;
        }
        
        if($result != "error" AND $pass == 1){
            log::add('scan_ip', 'debug', 'recordMacVendor :. Mise en cache');
            $tmp[$rest] = $result;
            $result = array_merge($arayVendor, $tmp);

            $fichier = fopen(self::$_serializeMacAddress, 'w');
            fputs($fichier, serialize($result));
            fclose($fichier);   
        }
    }
    
    public static function showMacVendor($_mac) {
        $rest = substr($_mac, 0, 8); 
        $arayVendor = self::getMacRecord();
        if(!empty($arayVendor[$rest])){
            return $arayVendor[$rest]["company"];
        } else {
            return "...";
        }
    }
    
    public static function getMacRecord() {
        if(is_file(self::$_serializeMacAddress)){
            return unserialize(file_get_contents(self::$_serializeMacAddress));
        } else {
            return array();
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GET OUI VENDOR MAC
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
   
    public static function dependancy_info() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'dependancy_info :. Lancement');
        $return = array();
        $return['log'] = 'scan_ip_update';
        $return['progress_file'] = jeedom::getTmpFolder('scan_ip') . '/dependance';
        
        if (self::arpVersion() == "arp-scan not found") {
            $return['state'] = 'nok';
        } else {
            $return['state'] = 'ok';
        }
        
        log::add('scan_ip', 'debug', 'dependancy_info :. ' . $return['state']);
        return $return;
    }

    public static function dependancy_install() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'dependancy_install :. Lancement');
        log::remove(__CLASS__ . '_update');
        return array('script' => dirname(__FILE__) . '/../../resources/install_#stype#.sh ' . jeedom::getTmpFolder('scan_ip') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
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
                scan_ip::cmdRefresh($eqlogic);
                log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
                break;
        }
    }
    
    

    /*     * **********************Getteur Setteur*************************** */
}
