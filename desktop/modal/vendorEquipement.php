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
            <a class="btn btn-success"><i class="far fa-check-circle icon-white"></i> Cliquez ici pour relancer une recherche</a>
        </div>
        <div class="panel-body">
            <?php
                scan_ip::printShell("sudo ip a");
            ?>
        </div>
    </div>
</div>

