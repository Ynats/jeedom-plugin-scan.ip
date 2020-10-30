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
$list = 1;

?>

<style>
    li{
        margin-left: 15px;
    }
</style>

<div class="col-md-12">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title">{{Forcer une recherche sur les constructeurs d'équipement}}</h3>
        </div>
        <br />
        <div class="alert alert-info">
            <label>Pour information</label>
            <li>Cette fonctionnalité permet de relancer la synchronisation "MacVendor" qui permet de récupérer le nom des fabriquants d'équipement.</li>
            <li>Cette recherche peut être un peu longue suivant le nombre d'équipement que vous avez.</li>
            <li>Pour information, si certains de vos équipements ne sont pas encore reconnus (car pas encore référencés) une tâche planifiée permet de mettre à jour vos équipements tous les <?php echo scan_ip::$_timeRefreshMacAddress ?> jours.</li>
        </div>
        <div class="panel-body" style="text-align:right;">
            <a class="btn btn-success" id="scan_ip_search"><i class="far fa-check-circle icon-white"></i> Cliquez ici pour relancer une recherche. La recherche peut durée plus d'une minute.</a>
        </div>
        <div class="panel-body">
            <table style="width: 100%; margin: -5px -5px 10px 5px;">
                <thead>
                    <tr style="background-color: grey !important; color: white !important;">
                        <th style="text-align: center; width:40px;">#</th>
                        <th style="width:160px;" class="scanTd">{{Adresse MAC}}</th>
                        <th class="scanTd">{{Nom}}</th>
                    </tr>
                </thead>
                <tbody>
<?php                
                    foreach ($ipsReseau["sort"] as $device) {

                        if (scan_ip::isOffline($device->time) == 0) {
                            
                            $name = scan_ip::showMacVendor($device->mac);
                            if($name == "..."){
                                $name = "<span style='color:darkorange;'>OUI Inconnue ?</span>";
                            }

                            echo '<tr>'
                            . '<td style="text-align:center;">' . $list++ . '</td>'
                            . '<td class="scanTd">' . $device->mac . '</td>'
                            . '<td class="scanTd" style="text-overflow: ellipsis;">' . $name . '</td>'
                            . '</tr>';
                        }
                    }
?>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_file('desktop', 'scan_ip', 'js', 'scan_ip'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>