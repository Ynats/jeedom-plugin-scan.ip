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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }
    
    require_once dirname(__FILE__) . '/../../../../plugins/scan_ip/core/class/scan_ip.tools.php';
    require_once dirname(__FILE__) . '/../../../../plugins/scan_ip/core/class/scan_ip.json.php';
    require_once dirname(__FILE__) . '/../../../../plugins/scan_ip/core/class/scan_ip.eqLogic.php';
    
    ajax::init();
    
    if (init('action') == 'syncEqLogicWithOpenScanId') {
        
        if(scan_ip_tools::lockProcess() == TRUE){
            scan_ip_scan::syncScanIp();
            scan_ip_tools::unlockProcess();  
        } else {
            event::add('jeedom::alert', array(
                'level' => 'warning',
                'page' => 'scan_ip',
                'message' => 'Action annulée : Une synchronisation est déjà en cours.'
            ));
        }
                
        ajax::success();
    }
    
    elseif (init('action') == 'recordCommentaires') {

        scan_ip_json::majNetworkCommentaires(init('data'));
        ajax::success();
        
    }
    
    elseif (init('action') == 'addEquipement') {
        
        scan_ip_eqLogic::addEquipementsTab(init('data'));
        ajax::success();
        
    }
    
    elseif (init('action') == 'removeEquipement') {
        
        scan_ip_json::removeEquipementsTab(init('data'));
        ajax::success();
        
    }
    
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
