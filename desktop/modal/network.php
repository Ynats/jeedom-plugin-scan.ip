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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$ipsReseau = (array) scan_ip::getJsonTampon();
if (empty($ipsReseau)) {
    scan_ip::syncScanIp();
    $ipsReseau = (array) scan_ip::getJsonTampon();
}
$savingMac = scan_ip::getAlleqLogics();

$list = 1;
?>

<style>
    .scanTd{
        padding : 3px 20px !important;
    }
    .macPresentActif{
        color: green;
    }
    .macPresentInactif{
        color: red;
    }
    .macAbsent{
        color: grey;
    }
    .spanScanIp{
        display: block;
        width: 78x !important;
        padding : 2px 5px;
        color : white;
        text-align: center;
    }
    .EnableScanIp{
        background-color: green;
    }
    .DisableScanIp{
        background-color: red;
    }
    .NoneScanIp{
        background-color: #A9A9A9;
    }
</style>

<div class="col-md-8">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Les plages ip et adresses MAC du réseau (<?php echo $ipsReseau["infos"]->date ?>)</h3>
        </div>
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th style="text-align: center; width:30px;">#</th>
                        <th style="text-align: center;" class="scanTd">{{Equipements}}</th>
                        <th style="width:150px;" class="scanTd">{{Adresse MAC}}</th>
                        <th class="scanTd">{{ip}}</th>
                        <th class="scanTd">{{Nom}}</th>
                        <th class="scanTd"></th>
                    </tr>
                </thead>
                <tbody>
<?php
                    
                    foreach ($ipsReseau["sort"] as $device) {

                        if (scan_ip::isOffline($device->time) == 0) {
                            
                            if (isset($savingMac[$device->mac]["name"])) {
                                $name = $savingMac[$device->mac]["name"];
                            } else {
                                $name = "-";
                            }

                            if (isset($savingMac[$device->mac]["enable"])) {
                                if ($savingMac[$device->mac]["enable"] == 1) {
                                    $classPresent = "macPresentActif";
                                    $textPresent = "Enregistré";
                                    $classSuivi = "spanScanIp EnableScanIp";
                                } else {
                                    $classPresent = "macPresentInactif";
                                    $textPresent = "Désactivé";
                                    $classSuivi = "spanScanIp DisableScanIp";
                                }
                            } else {
                                $classPresent = "macAbsent";
                                $textPresent = "Non enregistré";
                                $classSuivi = "spanScanIp NoneScanIp";
                            }

                            echo '<tr>'
                            . '<td style="text-align:center;" class="' . $classPresent . '">' . $list++ . '</td>'
                            . '<td class="' . $classPresent . '"><span class="' . $classSuivi . '">' . $textPresent . '</span></td>'
                            . '<td class="scanTd ' . $classPresent . '">' . $device->mac . '</td>'
                            . '<td class="scanTd ' . $classPresent . '">' . $device->ip_v4 . '</td>'
                            . '<td class="scanTd ' . $classPresent . '">' . $name . '</td>'
                            . '<td class="scanTd ' . $classPresent . '">' . date("d/m/Y H:i:s", $device->time) . '</td>'
                            . '</tr>';
                        }
                    }
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Votre Jeedom</h3>
        </div>
        <div class="panel-body">
            <div>
                <label class="col-sm-5 control-label">Nom : </label>
                <div><?php echo $ipsReseau["jeedom"]->name ?></div> 
            </div>
            <div>
                <label class="col-sm-5 control-label">ip : </label>
                <div><?php echo $ipsReseau["jeedom"]->ip_v4 ?></div> 
            </div>
            <div>
                <label class="col-sm-5 control-label">Adresse MAC : </label>
                <div><?php echo $ipsReseau["jeedom"]->mac ?></div>
            </div>
<?php if (gethostbyaddr($ipsReseau["jeedom"]->ip_v4) != $ipsReseau["jeedom"]->ip_v4) { ?>            
                <div>
                    <label class="col-sm-5 control-label">Host Name : </label>
                    <div><?php echo gethostbyaddr($ipsReseau["jeedom"]->ip_v4) ?></div>
                </div>
<?php } ?> 
        </div>
        <br />
    </div>
</div>

<div class="col-md-4">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">Votre routeur</h3>
        </div>
        <div class="panel-body">
            <div>
                <label class="col-sm-5 control-label">ip : </label>
                <div><?php echo $ipsReseau["route"]->ip_v4 ?></div>
            </div>
            <div>
                <label class="col-sm-5 control-label">Adresse MAC : </label>
                <div><?php echo $ipsReseau["route"]->mac ?></div>
            </div>
        </div>
        <br />
    </div>
</div>

<?php include_file('core', 'plugin.template', 'js'); ?>