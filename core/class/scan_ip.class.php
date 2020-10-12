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
        $return["jsonTampon"] = $return["folderTampon"]."mapping.json"; // Fichier des Json en Tampon
        $return["timeSynchro"] = 60 * 5; // Synchro avec le site distant tous les X secondes
        
        $return["ip_route"] = self::getIpRoute();
        
        $return["plage_ip"] = self::getPlageIp($return["ip_route"]);
        $return["plage_start"] = config::byKey('plage_start', 'scan_ip', NULL);
        $return["plage_end"] = config::byKey('plage_end', 'scan_ip', NULL);
        
        if($return["plage_start"] == NULL OR $return["plage_end"] == NULL){
           $return["plage_enable"] = 0;
        } else { $return["plage_enable"] = 1; }
        
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
    
    public static function nmad($_ip, $_end = NULL){
        log::add('scan_ip', 'debug', 'nmad :. Lancement');
        $nmap = 'sudo nmap -sP ' . $_ip;
        if($_end != NULL){ 
            $nmap .= '-' . $_end; 
        }
        exec($nmap, $return);
        
        log::add('scan_ip', 'debug', $return);
        return $return;
    }
    
    public static function scanReseau(){
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'scanReseau :. Lancement du scan');
        
        $config = self::getConfig();
        $infoRouter = self::getInfoRouteur($config["ip_route"]);
        $infoJeedom = self::getInfoJeedom();
        
        $ignoreIps = array($infoRouter["ip_v4"], $infoJeedom["ip_v4"]);  
        
        if($config["plage_enable"] == 1){
            log::add('scan_ip', 'debug', 'scanReseau :. Scan du réseau principal');
            $nmapResult = self::nmad($config["plage_ip"] . '.' . $config["plage_start"], $config["plage_end"]);
            $now = self::filtreIpMac($now, $nmapResult, $ignoreIps);
        }
        
        $now["routeur"] = $infoRouter;
        
        if(empty($now["jeedom"]["ip_v4"])){
            $now["jeedom"]["ip_v4"] = $_SERVER["REMOTE_ADDR"];
        }
        
        $now["jeedom"] = $infoJeedom;
        
        $now["timestamp"]["time"] = time();
        $now["timestamp"]["date"] = date("d/m/Y H:i:s", $now["timestamp"]["time"]);
        
        log::add('scan_ip', 'debug', 'scanReseau :. Fin du scan [' . $now["infos"]["nmap"] . ']');
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        return $now;
    }
    
    public static function filtreIpMac($_now, $_retour_nman, $_ips_ignore = array()){
        
        $i = 0;
        
        foreach ($_retour_nman as $value) {
            
            if(preg_match('(Starting Nmap)', $value) AND empty($now["infos"]["nmap"])) {
                $_now["infos"]["nmap"] = explode(" (", $value)[0];
            }   
            elseif (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $value)) {
                preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $value, $ip_out);
                if(!in_array($ip_out[0], $_ip_ignore)){
                    $i++;
                    $valueIp = $ip_out[0];
                }  
            } 
            elseif (preg_match('(MAC Address: )', $value)) {
                 if(!in_array($ip_out[0], $_ips_ignore)){
                    $_now["all"][$i] = self::filtreMacNameAdresse($value);
                    $_now["all"][$i]["ip_v4"] = $valueIp;
                    // Pour les recherches
                    $_now["by_mac"][$_now["all"][$i]["mac"]] = $valueIp;
                    $_now["by_ip_v4"][$valueIp] = $_now["all"][$i]["mac"];
                }
            }       
        }
        
        return $_now;
        
    }
    
    public static function searchByIp($_searchIp){
        $sort = (array) scan_ip::getJsonTampon();
        if(!empty($sort["by_ip_v4"]->{$_searchIp})){
            return $sort["by_ip_v4"]->{$_searchIp};
        } else {
            return NULL;
        }
    }
    
    public static function searchByMac($_searchMac){
        $sort = (array) scan_ip::getJsonTampon();
        if(!empty($sort["by_mac"]->{$_searchMac})){
        return $sort["by_mac"]->{$_searchMac};
        } else {
            return NULL;
        }
    }
    
    public static function filtreMacNameAdresse($_value){
        $mac_out = str_replace('MAC Address: ', "", $_value);
        $mac_out = str_replace(' (', "|", $mac_out);
        $mac_out = str_replace(')', "", $mac_out);
        $mac_out = explode("|", $mac_out);
        $return["mac"] = $mac_out[0];
        if($mac_out[1] != "Unknown"){ $return["name"] = $mac_out[1]; }
        else { $return["name"] = "-"; }
        return $return; 
    }
    
    public static function getInfoRouteur($ip_route){
        log::add('scan_ip', 'debug', 'getInfoRouteur :. Lancement');
        $nmad = self::nmad($ip_route);
        
        foreach ($nmad as $value) {
            if (preg_match('(MAC Address: )', $value)) {
                $return = self::filtreMacNameAdresse($value);
            }       
        }
        
        $return["ip_v4"] = $ip_route;
        return $return;
    }
    
    public static function getInfoJeedom(){ 
        log::add('scan_ip', 'debug', 'getInfoJeedom :. Lancement');
        
        $ipRoute = exec("ip route show default | awk '/default/ {print $3}'");
        $config = self::getConfig(); 
        $tmp = json_decode(exec("ip -j a"));
        
        foreach ($tmp as $mac) {
            $adress = $mac->address;
            foreach ($mac->addr_info as $ip) {
                if(preg_match('('.$config["plage_ip"].'.)', $ip->local)){
                    $return["ip_v4"] = $ip->local;
                    $return["mac"] = strtoupper($adress);
                    $return["name"] = config::byKey('name');
                }
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
