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

function scan_ip_install() {
    
    if (config::byKey('cron_pass', 'scan_ip') == '') {
            config::save('cron_pass', 1, 'scan_ip');
    }
    if (config::byKey('offline_time', 'scan_ip') == '') {
            config::save('offline_time', 4, 'scan_ip');
    }
    
}

function scan_ip_update() {
    
    if (config::byKey('cron_pass', 'scan_ip') == '') {
            config::save('cron_pass', 1, 'scan_ip');
    }
    if (config::byKey('offline_time', 'scan_ip') == '') {
            config::save('offline_time', 4, 'scan_ip');
    }
    
    scan_ip::cleanAfterUpdate(dirname(__FILE__) . '/../../../');
    
    if(is_dir(__DIR__ . "/../../../plugins/scan_ip/core/json")){
            if(!is_dir(__DIR__ . "/../../../plugins/scan_ip/data")){
                exec("sudo mkdir ". __DIR__ . "/../../../../plugins/scan_ip/data");
            }
            exec("sudo chmod 777 -R ". __DIR__ . "/../../../plugins/scan_ip/data");
            exec("sudo mv " . __DIR__ . "/../../../plugins/scan_ip/core/json " . __DIR__ . "/../../../../plugins/scan_ip/data/json");
        }    
    
    foreach (scan_ip::byType('scan_ip') as $scan_ip) {
        try {
            $scan_ip->save();
        } catch (Exception $e) {
        }
    }  
}


function scan_ip_remove() {
    
}

?>
