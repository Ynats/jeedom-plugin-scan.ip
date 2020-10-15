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

    /*     * **************************Configuration************************* */

    public static function getConfig() {
        $return["folderTampon"] = __DIR__ . "/../../../../plugins/scan_ip/core/json/";
        $return["jsonTamponTemp"] = $return["folderTampon"]."mapping.temp";
        $return["jsonTampon"] = $return["folderTampon"]."mapping.json";
        $return["serializeTampon"] = $return["folderTampon"]."serialize.temp";
        
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
        scan_ipCmd::cmdRefresh($refresh->getEqLogic());

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

// Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin

//    public function toHtml($_version = 'dashboard') {
//
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//        log::add('scan_ip', 'debug', 'toHtml :. lancement');
//
//
//        log::add('scan_ip', 'debug', 'toHtml :. fin');
//        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
//
//        return template_replace($replace, getTemplate('core', $version, 'scan_ip', 'scan_ip'));
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
   
//    public function get() {
//        log::add('scan_ip', 'debug', '#######################################################################################');
//        log::add('scan_ip', 'debug', 'get :. #ID#' . $_id . ' lancement');
//
//        $eqLogic = self::byId($this->getId());
//
//        if ($eqLogic->getIsEnable() == 1) {
//            
//            
//        }
//
//        log::add('scan_ip', 'debug', '#######################################################################################');
//    }
    
    public static function syncScanIp(){
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'syncScanIp :. Lancement du scan du réseau');
        self::scanReseau();
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                scan_ipCmd::cmdRefresh($scan_ip);
                log::add('scan_ip', 'debug', 'syncScanIp :. scan_ipCmd::cmdRefresh('.$scan_ip->getId().')');
            }
        }
    }
    
    public static function scanReseau(){
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'scanReseau :. Lancement du scan');
        
        $config = self::getConfig();
        $ipRoute = self::getIpRoute();
        $infoJeedom = self::getInfoJeedom($ipRoute);
        
        if($config["subReseauEnable"] > 0){
            $new = array();
            foreach ($config["subReseau"] as $sub) { 
                if($sub["enable"] == 1){
                    $scanResult = self::arpScanShell($sub["name"]);
                    $new = array_merge($new, $scanResult);
                }
            }
            if(count($new) == 0){
                log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
            }
            
            $old = self::getFileSerialize($config);

            if(empty($old) OR count($old) == 0){ $now = $new; } 
            else { $now = array_merge($old, $new); } 

            self::createFileSerialize($config, $now);

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
        else {
            log::add('scan_ip', 'error', "Aucun élément n'a été trouvé sur vos réseaux. Vérifiez vos configurations.");
        }

        $now["jeedom"] = $infoJeedom; 
        $now["infos"]["version_arp"] = self::arpVersion();
        $now["infos"]["time"] = time();
        $now["infos"]["date"] = date("d/m/Y H:i:s", $now["infos"]["time"]);
        

        self::recordInJson($config, json_encode($now));
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $now["infos"]["version_arp"] . ']');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        return $now;
    }
     
    public static function searchByMac($_searchMac){ 
        $sort = (array) scan_ip::getJsonTampon();
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

        $exec = shell_exec('sudo ip a');
        $list = preg_split('/[\r\n]+/', $exec);
        
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
        return exec("ip route show default | awk '/default/ {print $3}'");
    }
    
    public static function getAlleqLogics(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            $return[$scan_ip->getConfiguration("adress_mac")]["name"] = $scan_ip->name;
            $return[$scan_ip->getConfiguration("adress_mac")]["enable"] = $scan_ip->getIsEnable();
        }
        return $return;
    }
    
    public static function isOffline($_time){
        $expire = time() - (60 * 5); // 5 minutes
        if($expire <= $_time){ return 0; } 
        else { return 1; }
    }
            
            
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# AFFICHAGE VUES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public static function printInputSubConfig(){
        $return = "";
        foreach (scan_ip::scanSubReseau() as $sub) {
            if($sub["name"] != "lo") {
                $return .= '<div class="form-group" style="margin-top: 15px;">';
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
                $print .= '>' . $value->mac . ' - ' . $value->ip_v4 . '</option>';
            }
        }  
        echo $print;
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# AFFICHAGE VUES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP NMAP
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
//    public static function nmadScan($_ip, $_end = NULL){
//        log::add('scan_ip', 'debug', 'nmad :. Lancement');
//        $nmap = 'sudo nmap -sP ' . $_ip;
//        if($_end != NULL){ 
//            $nmap .= '-' . $_end; 
//        }
//        exec($nmap, $return);
//        
//        log::add('scan_ip', 'debug', $return);
//        return $return;
//    }
//    
//    public static function nmadFilterMac($_input, $_ip, $_exclude = array()){
//        if (preg_match('(MAC Address: )', $_input)) {
//            if(!in_array($_ip, $_exclude)){
//                $mac_out = str_replace('MAC Address: ', "", $_input);
//                $mac_out = str_replace(' (', "|", $mac_out);
//                $mac_out = str_replace(')', "", $mac_out);
//                $mac_out = explode("|", $mac_out);
//                return $mac_out[0];
//            }
//        } 
//    }

    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP NMAP
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function arpScanShell($_subReseau){
        $time = time();
        $return = array();
        $exec = shell_exec('sudo arp-scan --interface='.$_subReseau.' --localnet');
        $scan = preg_split('/[\r\n]+/', $exec);
        foreach ($scan as $scanLine) {
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
        $exec = shell_exec('sudo arp-scan -V');
        $list = preg_split('/[\r\n]+/', $exec);
        foreach ($list as $searchVersion) {
            if(preg_match("(sudo: arp-scan: command not found)", $searchVersion)){ return NULL; }
            elseif(preg_match("(arp-scan )", $searchVersion)){ return $list[0]; }
        }     
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function cronTemplate(){
        self::syncScanIp();
        
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                scan_ipCmd::cmdRefresh($scan_ip);
                log::add('scan_ip', 'debug', 'cron5 :. scan_ipCmd::cmdRefresh('.$scan_ip->getId().')');
            }
        }
    }
    
    public static function cron() {
        
        $now = time();
        $cronConfig = config::byKey('cron_pass', 'scan_ip', 3);
        $cronLast = config::byKey('last_cron', 'scan_ip', 0);
        
        log::add('scan_ip', 'debug', 'cron :. Configuration Minute : '. $cronConfig);
        
        if($cronLast == 0 OR $now >= $cronLast+(60*$cronConfig)) {
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            log::add('scan_ip', 'debug', 'CRON :. START');
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            log::add('scan_ip', 'debug', 'cron :. Lancement');
            
            self::cronTemplate();
            config::save('last_cron', time(), 'scan_ip');
            
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            log::add('scan_ip', 'debug', 'CRON :. FIN');
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        }
        else{
            log::add('scan_ip', 'debug', 'cron :. Annulée ');
        }
        
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
    
    public static function recordInJson($_config, $_json) {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'recordInJson :.  Lancement');
        
        self::prepareJsonFolder($_config);
        self::createJsonFile($_config, $_json);

        log::add('scan_ip', 'debug', 'recordInJson :. Enregistrement du Json : ' . $config["jsonTampon"]);
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
    
    public static function getJsonTampon() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'getJsonTampon :. Lancement');
        $config = self::getConfig();
        $return = json_decode(file_get_contents($config["jsonTampon"]));
        log::add('scan_ip', 'debug', 'getJsonTampon :. Chargement du Json Mapping');
        return $return;
    }
    
    public static function prepareJsonFolder($_config){
        log::add('scan_ip', 'debug', 'prepareJsonFolder :. Lancement');
        if (!is_dir($config["folderTampon"])) {
            log::add('scan_ip', 'debug', 'miseEnCacheJson :.  Création du dossier :' . $_config["folderTampon"]);
            mkdir($config["folderTampon"], 0777);
        }
    }
    
    public static function createJsonFile($_config, $_json){
        log::add('scan_ip', 'debug', 'createJsonFile :. Lancement');
        
        $fichier = fopen($_config["jsonTamponTemp"], 'w');
        fputs($fichier, $_json);
        fclose($fichier);

        unlink($_config["jsonTampon"]);
        rename($_config["jsonTamponTemp"], $_config["jsonTampon"]);
        chmod($_config["jsonTampon"], 0777);
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
        $exec = shell_exec('sudo ip a');
        $list = preg_split('/[\r\n]+/', $exec); 
        
        foreach ($list as $value) {
            if(preg_match(self::getRegex("sub_reseau"), $value)){ 
                $name = trim(explode(":", $value)[1]);
                if($name != $exclude) {
                    $i++; 
                    $return[$i]["name"] = $name; 
                } else {
                    
                }
            }
            if(preg_match(self::getRegex("ip_v4"), $value) AND preg_match("(".self::getPlageIp($ipRoute).")", $value)) {
                $return[$i]["ip_v4"] = self::getPlageIp(trim(str_replace("inet", "", explode("/",$value)[0]))).".*";
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
    
    public static function createFileSerialize($_config, $_data){
        log::add('scan_ip', 'debug', 'createFileSerialize :. Lancement');
        
        $fichier = fopen($_config["serializeTampon"], 'w');
        fputs($fichier, serialize($_data));
        fclose($fichier);

        chmod($_config["fichierTampon"], 0777);
    }
    
    public static function getFileSerialize($_config){
        return unserialize(file_get_contents($_config["serializeTampon"]));
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION CACHE SERIALIZE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public static function compilationOk() {
        if (shell_exec('ls /var/lib/dpkg/info/arp-scan.list | wc -l') == 0) {
            return FALSE;
        }
        return TRUE;
    }
    
    public static function dependancy_info() {
        $return = array();
        $return['log'] = 'scan_ip_update';
        $return['progress_file'] = jeedom::getTmpFolder('scan_ip') . '/dependance';
        
        if (self::compilationOk() != FALSE) {
            $return['state'] = 'ok';
        } else {
            $return['state'] = 'nok';
        }
        
        log::add('scan_ip', 'debug', 'dependancy_info :. ' . $return['state']);
        return $return;
    }

    public static function dependancy_install() {
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
        $eqlogic = $this->getEqLogic();
        switch ($this->getLogicalId()) { //vérifie le logicalid de la commande 			
            case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave 
                log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
                log::add('scan_ip', 'debug', 'execute :. Lancement de la commande refresh : #ID#' . $eqlogic->getId());
                $this->cmdRefresh($eqlogic);
                break;
        }
    }
    
    public static function cmdRefresh($eqlogic){
        log::add('scan_ip', 'debug', 'cmdRefresh :. Lancement');
        $device = scan_ip::searchByMac($eqlogic->getConfiguration("adress_mac"));
        if(scan_ip::isOffline($device["time"]) == 0){
            $eqlogic->checkAndUpdateCmd('ip_v4', $device["ip_v4"]);
        } else {
            $eqlogic->checkAndUpdateCmd('ip_v4', NULl);
        }
        $eqlogic->checkAndUpdateCmd('update_time', $device["time"]);
        $eqlogic->checkAndUpdateCmd('update_date', date("d/m/Y H:i:s", $device["time"]));
    }

    /*     * **********************Getteur Setteur*************************** */
}
