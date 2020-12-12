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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
require_once dirname(__FILE__) . "/../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

function scan_ip_install() {
    
    log::add('scan_ip', 'info', '--------------------------------------------');
    log::add('scan_ip', 'info', '>  Installation :. Démarrage');
    
    try {
        scan_ip_maj::activationCron(0);
        scan_ip_maj::setConfigBase();
        
        log::add('scan_ip', 'info', '>  Initialisation le Widget Network');
        scan_ip_widget_network::getWidgetNetwork();
        
        log::add('scan_ip', 'info', '>  Initialisation le Widget Alerte');
        scan_ip_widget_alerte::getWidgetAlerteNewEquipement();
        
        scan_ip_maj::activationCron(1);
        
        log::add('scan_ip', 'info', '>  Installation :. Fin');
        log::add('scan_ip', 'info', '--------------------------------------------');
        
    } catch (Exception $exc) {
        log::add('scan_ip', 'info', '>  Installation :. ERROR : ' . $exc);
        log::add('scan_ip', 'info', '--------------------------------------------');
    }

}

function scan_ip_update() {
    
    log::add('scan_ip', 'info', '--------------------------------------------');
    log::add('scan_ip', 'info', '>  Mise à jour :. Démarrage v'.scan_ip_maj::$_versionPlugin);
    
    try {
        
        log::add('scan_ip', 'info', '>  Désactivation du CRON');
        scan_ip_maj::activationCron(0);
        
        log::add('scan_ip', 'info', '>  Check des fichiers Json');
        if(@is_file(__DIR__ . "/../../../plugins/scan_ip/core/json/mapping.json")){ 
            shell_exec("sudo mv " . __DIR__ . "/../../../plugins/scan_ip/core/json/*.json " . __DIR__ . "/../../../../plugins/scan_ip/data/json");
        }
        
        scan_ip_maj::setConfigBase(); 
        scan_ip_maj::cleanAfterUpdate(dirname(__FILE__) . '/../../../');
        scan_ip_maj::majJsonCommentaires_v1_1();
        scan_ip_maj::majJsonEquipements_v1_1();
        scan_ip_maj::majAllEquipement();
        
        log::add('scan_ip', 'info', '>  Initialisation le Widget Network');
        scan_ip_widget_network::getWidgetNetwork();
        
        log::add('scan_ip', 'info', '>  Initialisation le Widget Alerte');
        scan_ip_widget_alerte::getWidgetAlerteNewEquipement();
        
        scan_ip_maj::activationCron(1);
        
        log::add('scan_ip', 'info', '>  Mise à jour :. Fin v'.scan_ip_maj::$_versionPlugin);
        log::add('scan_ip', 'info', '--------------------------------------------');

    } catch (Exception $exc) {
        log::add('scan_ip', 'info', '|  Mise à jour :. ERROR : ' . $exc);
        log::add('scan_ip', 'info', '--------------------------------------------');
    }

}


function scan_ip_remove() {
    
}

?>
