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

require_once __DIR__ . "/../../../../core/php/core.inc.php";
require_once __DIR__ . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

class scan_ip extends eqLogic {
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# START | VARIABLES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public static $_widgetPossibility = array('custom' => true);
    public static $_folderJson = __DIR__ . "/../../../../plugins/scan_ip/data/json/";
    public static $_jsonMapping = __DIR__ . "/../../../../plugins/scan_ip/data/json/mapping";
    public static $_jsonEquipement = __DIR__ . "/../../../../plugins/scan_ip/data/json/equipements";
    public static $_jsonCommentairesEquipement = __DIR__ . "/../../../../plugins/scan_ip/data/json/commentMac";
    
    public static $_pngIconMac = "./plugins/scan_ip/data/png/scan_ip_icon_mac.png";
    public static $_pngIconWidget = "./plugins/scan_ip/data/png/scan_ip_icon_widget.png";
    
    public static $_bash_oui = "sudo get-oui -u http://standards-oui.ieee.org/oui.txt -f " . __DIR__ . "/../../../../plugins/scan_ip/resources/oui.txt";
    public static $_file_oui =  __DIR__ . "/../../../../plugins/scan_ip/resources/oui.txt";
    public static $_bash_iab = "sudo get-iab -u http://standards-oui.ieee.org/iab/iab.txt -f " . __DIR__ . "/../../../../plugins/scan_ip/resources/iab.txt";
    public static $_file_iab =  __DIR__ . "/../../../../plugins/scan_ip/resources/iab.txt";
    public static $_file_lock =  __DIR__ . "/../../../../plugins/scan_ip/resources/lock";
    public static $_defaut_cron_pass = 1;
    public static $_defaut_offline_time = 4;
    public static $_defaut_alerte_new_equipement = 10;
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# END | VARIABLES
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# START | CONFIGURATON
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public static function getConfigMode() {
        return config::byKey('mode_plugin', 'scan_ip', "normal");
    }
    
    public function postInsert() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postInsert :. Lancement');
        
        $this->setConfiguration("offline_time", self::$_defaut_offline_time);
        $this->save();
    }
    
    public static function postConfig_widget_network() { 
        
        $eqLogic = scan_ip_widget_network::getWidgetNetwork();
        
        if(config::byKey('widget_network', 'scan_ip', '1') == 1) {
            $eqLogic->setIsVisible(1);
            $eqLogic->setIsEnable(1);
        } else {
            $eqLogic->setIsVisible(0);
            $eqLogic->setIsEnable(0);
        }
        
        $eqLogic->save();        
    }
    
    public static function postConfig_widget_new_equipement() { 
        
        $eqLogic = scan_ip_widget_alerte::getWidgetAlerteNewEquipement();
        
        if(config::byKey('widget_new_equipement', 'scan_ip', '1') == 1) {
            $eqLogic->setIsVisible(1);
        } else {
            $eqLogic->setIsVisible(0);
        }
        
        $eqLogic->save();        
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# END | CONFIGURATON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# START | CONFIGURATON
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public function postUpdate() {
        
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postUpdate :. Mise à jour de : ' . $this->getId());
        
        switch (scan_ip_widgets::getWidgetType($this)) {
            
            case "normal":
                scan_ip_widget_normal::setCmdWidgetNormal($this);
                break;
            
            case "network":
                scan_ip_widget_network::setCmdWidgetNetwork($this);
                break;
            
            case "new_equipement":
                scan_ip_widget_alerte::setCmdAlerteNewEquipement($this);
                break;
        }
             
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
    
    public function postSave() {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'postSave :. Refresh Command : ' . $this->getId());
                
        // Mise à jour des données
        scan_ip_cmd::cmdRefresh($this, scan_ip_json::getJson(self::$_jsonMapping));

        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
    }
   
    public function toHtml($_version = 'dashboard') {

        log::add('scan_ip', 'debug', 'toHtml :.  Lancement');

        $replace = $this->preToHtml($_version); //récupère les informations de notre équipement

        if (!is_array($replace)) {
            return $replace;
        }
        
        $reseau = scan_ip_json::getJson(scan_ip::$_jsonMapping);

        $this->emptyCacheWidget(); //vide le cache. Pratique pour le développement
        $version = jeedom::versionAlias($_version);
        
        if(scan_ip_widgets::getWidgetType($this) == "network"){
            log::add('scan_ip', 'debug', 'toHtml :.  Création widget Network');
            $replace = scan_ip_widget_network::createNetworkWidget($this, $version = 'dashboard', $replace, $reseau);
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            return template_replace($replace, getTemplate('core', $version, 'scan_ip_network', 'scan_ip')); 
        }elseif(scan_ip_widgets::getWidgetType($this) == "new_equipement"){
            log::add('scan_ip', 'debug', 'toHtml :.  Création widget Alerte');
            $replace = scan_ip_widget_alerte::createAlerteWidget($this, $version = 'dashboard', $replace);
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            return template_replace($replace, getTemplate('core', $version, 'scan_ip_alerte', 'scan_ip')); 
        } else {
            log::add('scan_ip', 'debug', 'toHtml :.  Création widget Normal');
            $replace = scan_ip_widget_normal::createSimpleWidget($this, $version = 'dashboard', $replace);
            log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
            return template_replace($replace, getTemplate('core', $version, 'scan_ip', 'scan_ip')); 
        }
        
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# GESTION DU WIDGET
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# TACHES CRON
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function cron() {
  
        $cronConfig = config::byKey('cron_pass', 'scan_ip', 1);

        
        if((date('i') % $cronConfig) == 0) {
            
            if(scan_ip_tools::lockProcess() == TRUE){
                ////////////////////////////////////////////////////////////////////
                log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
                log::add('scan_ip', 'debug', 'CRON :. START');
                log::add('scan_ip', 'debug', '_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_');
                
                log::add('scan_ip', 'debug', 'cron :. Configuration Minute : '. $cronConfig);
                 
                scan_ip_scan::syncScanIp();
                scan_ip_tools::unlockProcess();
                
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
    
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
# INSTALL & DEPENDENCY
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
    
    public static function dependancy_info() {
        $error = 0;
        $return = array();
        $return['state'] = 'nok';
        $return['log'] = 'scan_ip_update';
        $return['progress_file'] = jeedom::getTmpFolder('scan_ip') . '/dependance';

        if (scan_ip_shell::arpVersion() != "arp-scan not found") {
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
  
    
}

class scan_ipCmd extends cmd {

    public function execute($_options = array()) {
        log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
        log::add('scan_ip', 'debug', 'execute :. Lancement');
        
        $eqlogic = $this->getEqLogic();
        $mapping = scan_ip_json::getJson(scan_ip::$_jsonMapping);
        
        switch ($this->getLogicalId()) { //vérifie le logicalid de la commande 			
            case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave 
                log::add('scan_ip', 'debug', 'execute :. Lancement de la commande refresh : #ID#' . $eqlogic->getId());
                scan_ip_cmd::cmdRefresh($eqlogic, $mapping);
                log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
                break;
            case 'wol': 
                log::add('scan_ip', 'debug', 'execute :. Lancement de la commande wol : #ID#' . $eqlogic->getId());
                scan_ip_shell::wakeOnLanByCmd($eqlogic->getConfiguration("adress_mac"));
                log::add('scan_ip', 'debug', '---------------------------------------------------------------------------------------');
                break;
        }
    }
    
   
    /*     * **********************Getteur Setteur*************************** */
}
