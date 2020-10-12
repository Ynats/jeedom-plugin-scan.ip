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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
$conf = scan_ip::getConfig();
list($plage_ip_1, $plage_ip_2, $plage_ip_3) = explode('.', $conf["ip_route"]);

?>
<style>
    .spanIpScanReadOnly{
        width: 50px;
    }
    .spanPoint{
        width: 50px;
    }
</style>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Début de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_1 ?>" id="start_plage_1" class="form-control spanIpScanReadOnly" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_2 ?>" id="start_plage_2" class="form-control spanIpScanReadOnly" placeholder="?" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_3 ?>" id="start_plage_3" class="form-control spanIpScanReadOnly" placeholder="?" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="width: 50px;" maxlength="3" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_start" data-l1key="plage_start" placeholder="1" />
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Fin de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_1 ?>" id="end_plage_1" class="form-control spanIpScanReadOnly" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_2 ?>" id="end_plage_2" class="form-control spanIpScanReadOnly" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_3 ?>" id="end_plage_3" class="form-control spanIpScanReadOnly" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="width: 50px;" maxlength="3" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_end" data-l1key="plage_end" placeholder="255" />
            </div>
        </div>

    </fieldset>
</form>
