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
    
    require_once dirname(__FILE__) . '/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php';
    
    ajax::init();
    
    if (init('action') == 'reloadMaj') {
        
        log::add('scan_ip', 'info', '--------------------------------------------');
        log::add('scan_ip', 'info', '>  Mise à jour manuelle :. Démarrage v'.scan_ip_maj::$_versionPlugin);
        
        scan_ip_maj::activationCron(0);
        
        if(scan_ip_maj::checkJsonCommentaires_v1_1() == FALSE) { 
            scan_ip_maj::majJsonCommentaires_v1_1(); }
            
        if(scan_ip_maj::checkJsonEquipements_v1_1() == FALSE) {
            scan_ip_maj::majJsonEquipements_v1_1(); }
            
        if(scan_ip_maj::checkAllEquipements_v1_1() == FALSE) {
            scan_ip_maj::majAllEquipements_v1_1(); }
            
        scan_ip_maj::activationCron(1);
        
        log::add('scan_ip', 'info', '>  Mise à jour manuelle :. Fin v'.scan_ip_maj::$_versionPlugin);
        log::add('scan_ip', 'info', '--------------------------------------------');
        
        ajax::success();
        
    }
    
    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
