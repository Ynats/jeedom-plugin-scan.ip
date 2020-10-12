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
    .spanIpScanInput{
        width: 50px;
    }
    .spanPoint{
        width: 50px;
    }
</style>
<form class="form-horizontal">
    <fieldset>
        <legend>
            <i <?php if($conf["plage_1_enable"] == 1){ echo 'style="color:green;"'; } else { echo 'style="color:red;"'; } ?> class="fas fa-sitemap"></i> Réseau directement associé à votre Jeedom
	</legend>
        
        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Début de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_1 ?>" id="start_plage_1" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_2 ?>" id="start_plage_2" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_3 ?>" id="start_plage_3" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="width: 50px;" maxlength="3" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_start" data-l1key="plage_start" placeholder="1" />
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Fin de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_1 ?>" id="end_plage_1" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_2 ?>" id="end_plage_2" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $plage_ip_3 ?>" id="end_plage_3" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="width: 50px;" maxlength="3" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_end" data-l1key="plage_end" placeholder="255" />
            </div>
        </div>

    </fieldset>
    
    <fieldset>
        <legend>
            <i <?php if($conf["plage_2_enable"] == 1){ echo 'style="color:green;"'; } else { echo 'style="color:red;"'; } ?> class="fas fa-sitemap"></i> 1er sous-réseau (optionnel)
	</legend>
        
        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Début de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" maxlength="3" onkeyup="verif_nombre(this); autoChangeIp('start_plage_2_1', 'end_plage_2_1');" id="start_plage_2_1" class="configKey form-control spanIpScanInput" data-l1key="start_plage_2_1" placeholder="192" /><span class="spanPoint">.</span>
                <input type="text" maxlength="3" onkeyup="verif_nombre(this); autoChangeIp('start_plage_2_2', 'end_plage_2_2');" id="start_plage_2_2" class="configKey form-control spanIpScanInput" data-l1key="start_plage_2_2" placeholder="168" /><span class="spanPoint">.</span>
                <input type="text" maxlength="3" onkeyup="verif_nombre(this); autoChangeIp('start_plage_2_3', 'end_plage_2_3');" id="start_plage_2_3" class="configKey form-control spanIpScanInput" data-l1key="start_plage_2_3" placeholder="2" /><span class="spanPoint">.</span>
                <input type="text" maxlength="3" style="width: 50px;" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_2_start" data-l1key="plage_2_start" placeholder="1" />
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Fin de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" style="color: #039be5 !important;" value="<?php echo $conf["start_plage_2_1"] ?>" id="end_plage_2_1" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $conf["start_plage_2_2"] ?>" id="end_plage_2_2" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $conf["start_plage_2_3"] ?>" id="end_plage_2_3" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="width: 50px;" maxlength="3" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_2_end" data-l1key="plage_2_end" placeholder="255" />
            </div>
        </div>

    </fieldset>
    
    <fieldset>
        <legend>
            <i <?php if($conf["plage_3_enable"] == 1){ echo 'style="color:green;"'; } else { echo 'style="color:red;"'; } ?> class="fas fa-sitemap"></i> 2em sous-réseau (optionnel)
	</legend>
        
        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Début de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" maxlength="3" onkeyup="verif_nombre(this); autoChangeIp('start_plage_3_1', 'end_plage_3_1');" id="start_plage_3_1" class="configKey form-control spanIpScanInput" data-l1key="start_plage_3_1" placeholder="192" /><span class="spanPoint">.</span>
                <input type="text" maxlength="3" onkeyup="verif_nombre(this); autoChangeIp('start_plage_3_2', 'end_plage_3_2');" id="start_plage_3_2" class="configKey form-control spanIpScanInput" data-l1key="start_plage_3_2" placeholder="168" /><span class="spanPoint">.</span>
                <input type="text" maxlength="3" onkeyup="verif_nombre(this); autoChangeIp('start_plage_3_3', 'end_plage_3_3');" id="start_plage_3_3" class="configKey form-control spanIpScanInput" data-l1key="start_plage_3_3" placeholder="3" /><span class="spanPoint">.</span>
                <input type="text" v style="width: 50px;" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_3_start" data-l1key="plage_3_start" placeholder="1" />
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label class="col-sm-3 control-label">{{Fin de la plage IP}}</label>
            <div class="col-sm-3">
                <input type="text" style="color: #039be5 !important;" value="<?php echo $conf["start_plage_3_1"] ?>" id="end_plage_3_1" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $conf["start_plage_3_2"] ?>" id="end_plage_3_2" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="color: #039be5 !important;" value="<?php echo $conf["start_plage_3_3"] ?>" id="end_plage_3_3" class="form-control spanIpScanInput" readonly="" /><span class="spanPoint">.</span>
                <input type="text" style="width: 50px;" maxlength="3" onkeyup="verif_nombre(this);" class="configKey form-control" id="plage_3_end" data-l1key="plage_3_end" placeholder="255" />
            </div>
        </div>

    </fieldset>
    
</form>
