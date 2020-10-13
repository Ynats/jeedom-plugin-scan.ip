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
    public static $regex_ip_v4 = "/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/";
    public static $regex_mac = "/([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}/";

    /*     * **************************Configuration************************* */

    public static function getConfig() {
        $return["folderTampon"] = __DIR__ . "/../../../../plugins/scan_ip/core/json/";
        $return["jsonTamponTemp"] = $return["folderTampon"]."mapping.temp";
        $return["jsonTampon"] = $return["folderTampon"]."mapping.json"; // Fichier des Json en Tampon
        $return["timeSynchro"] = 60 * 5; // Synchro avec le site distant tous les X secondes
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
            $info->setName(__('Update Time', __FILE__));
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
            $info->setName(__('Update Date', __FILE__));
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
        self::recordInJson(json_encode(self::scanReseau()));
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
        
        $ipRoute = self::getIpRoute();
        $infoJeedom = self::getInfoJeedom($ipRoute);

        $now = self::arpScan($ipRoute); 
        $now["jeedom"] = $infoJeedom; 
        $now["infos"]["version_arp"] = self::arpVersion();
        $now["infos"]["time"] = time();
        $now["infos"]["date"] = date("d/m/Y H:i:s", $now["infos"]["time"]);
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $now["infos"]["version_arp"] . ']');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        return $now;
    }
    
    public static function searchByIp($_searchIp){
        $sort = (array) scan_ip::getJsonTampon();
        if(!empty($sort["byIpv4"]->{$_searchIp})){
            return $sort["byIpv4"]->{$_searchIp};
        } else {
            return NULL;
        }
    }
    
    public static function searchByMac($_searchMac){
        $sort = (array) scan_ip::getJsonTampon();
        if(!empty($sort["byMac"]->{$_searchMac})){
        return $sort["byMac"]->{$_searchMac};
        } else {
            return NULL;
        }
    }
    
    public static function getInfoJeedom($_ipRoute){ 
        log::add('scan_ip', 'debug', 'getInfoJeedom :. Lancement');

        $exec = shell_exec('sudo ip a');
        $list = preg_split('/[\r\n]+/', $exec);
        
        foreach ($list as $i => $value) {
            if (preg_match(self::$regex_ip_v4, $value) AND preg_match("(".self::getPlageIp($_ipRoute).")", $value)) {
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
    
    public static function getIpRoute(){
        return exec("ip route show default | awk '/default/ {print $3}'");
    }
    
    public static function printSelectOptionAdressMac($_selected = NULL){
        $record = self::getAlleqLogics();
        $list = (array) self::getJsonTampon();
        $print = "";
        foreach ($list["all"] as $value) {
            if(empty($record[$value->mac])){
                $print .= '<option value="'. $value->mac .'"';
                if($_selected != NULL AND $_selected == $value->mac) { $print .= ' selected'; }
                $print .= '>' . $value->mac . ' - ' . $value->ip_v4 . '</option>';
            }
        }  
        echo $print;
    }
    
    public static function getAlleqLogics(){
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            $return[$scan_ip->getConfiguration("adress_mac")]["name"] = $scan_ip->name;
            $return[$scan_ip->getConfiguration("adress_mac")]["enable"] = $scan_ip->getIsEnable();
        }
        return $return;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function arpScan($_ipRoute){
        $exec = shell_exec('sudo arp-scan  --localnet');
        $list = preg_split('/[\r\n]+/', $exec);
        foreach ($list as $scanLine) {
            if (preg_match(self::$regex_ip_v4, $scanLine)) {
                preg_match(self::$regex_ip_v4, $scanLine, $sortIp); 
                preg_match(self::$regex_mac, $scanLine, $sortMac);
                if($sortIp[0] == $_ipRoute){
                    $return["route"]["ip_v4"] = $sortIp[0];
                    $return["route"]["mac"] = strtoupper($sortMac[0]);
                } else {
                    $return["sort"][explode(".",$sortIp[0])[3]] = array("ip_v4" => $sortIp[0], "mac" => strtoupper($sortMac[0]));
                    $return["byIpv4"][$sortIp[0]] = strtoupper($sortMac[0]);
                    $return["byMac"][strtoupper($sortMac[0])] = $sortIp[0];                    
                }
            }
        }
        ksort($return["sort"]);
        return $return;
    }
    
    public static function arpVersion(){
        $exec = shell_exec('sudo arp-scan  -V');
        $list = preg_split('/[\r\n]+/', $exec);
        return $list[0];
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# APP ARP-SCAN
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function cron5() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'CRON 5 :. START');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'cron5 :. Lancement');
        
        log::add('scan_ip', 'debug', 'cron5 :. self::syncScanIp()');
        self::syncScanIp();
        
        $eqLogics = eqLogic::byType('scan_ip');
        foreach ($eqLogics as $scan_ip) {
            if ($scan_ip->getIsEnable() == 1) {
                scan_ipCmd::cmdRefresh($scan_ip);
                log::add('scan_ip', 'debug', 'cron5 :. scan_ipCmd::cmdRefresh('.$scan_ip->getId().')');
            }
        }
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'CRON 5 :. FIN');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');

    }
    
//    public static function cronHourly() {
//        
//    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
    
    public static function recordInJson($_json) {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'recordInJson :.  Lancement');
        
        $config = self::getConfig();
        self::prepareJsonFolder();
        self::createJsonFile($_json);

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
    
    public static function prepareJsonFolder(){
        log::add('scan_ip', 'debug', 'prepareJsonFolder :. Lancement');
        $config = self::getConfig();
        if (!is_dir($config["folderTampon"])) {
            log::add('scan_ip', 'debug', 'miseEnCacheJson :.  Création du dossier :' . $config["folderTampon"]);
            mkdir($config["folderTampon"], 0777);
        }
    }
    
    public static function createJsonFile($_json){
        log::add('scan_ip', 'debug', 'createJsonFile :. Lancement');
        $config = self::getConfig();
        
        $fichier = fopen($config["jsonTamponTemp"], 'w');
        fputs($fichier, $_json);
        fclose($fichier);

        unlink($config["jsonTampon"]);
        rename($config["jsonTamponTemp"], $config["jsonTampon"]);
        chmod($config["jsonTampon"], 0777);
    }
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU JSON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public static function dependancy_info() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'dependancy_info :. Lancement');
        $return = array();
        $return['progress_file'] = jeedom::getTmpFolder('scan_ip') . '/dependance';
        
        $test = exec('nmap -V | grep "Nmap version"');
        
        if (preg_match('(Nmap version )', $test)) {
            $return['state'] = 'ok';
        } else {
            $return['state'] = 'nok';
        }
        log::add('scan_ip', 'debug', 'dependancy_info :. ' . $return['state']);
        return $return;
    }

    public static function dependancy_install() {
        log::remove(__CLASS__ . '_update');
        return array('script' => dirname(__FILE__) . '/../../resources/install_scan_ip.sh ' . jeedom::getTmpFolder('scan_ip') . '/dependance', 'log' => log::getPathToLog(__CLASS__ . '_update'));
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
        $json = (array) scan_ip::getJsonTampon();
        $eqlogic->checkAndUpdateCmd('ip_v4', scan_ip::searchByMac($eqlogic->getConfiguration("adress_mac")));
        $eqlogic->checkAndUpdateCmd('update_time', $json["timestamp"]->time);
        $eqlogic->checkAndUpdateCmd('update_date', $json["timestamp"]->date);
    }

    /*     * **********************Getteur Setteur*************************** */
}
