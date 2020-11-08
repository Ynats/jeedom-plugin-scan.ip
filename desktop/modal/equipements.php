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
    .active{
        font-weight: bold;
    }
</style>

<div>
    <div id="content">

        <ul id="tabs_network" class="nav nav-tabs">
            <li id="scan_ip_tab_equipement" class="active"><a href="#scan_ip_equipement"> <i class="fa fa-picture-o"></i> {{Equipements enregistrés}}</a></li>
            <li id="scan_ip_tab_no_equipement"><a href="#scan_ip_tab_no_equipement"> <i class="fa fa-tachometer"></i> {{Equipements non enregistrés}}</a></li>
        </ul>
        <br />
        <div id="scan_ip_modal_equipement">
            <?php include_once(__DIR__ . "/../../../../plugins/scan_ip/desktop/modal/equipements/yes_equipement.php"); ?>
        </div>
        <div id="scan_ip_modal_no_equipement" style="display:none;">
            <?php include_once(__DIR__ . "/../../../../plugins/scan_ip/desktop/modal/equipements/no_equipement.php"); ?>
        </div>

    </div>
</div>

<script>

</script>

<?php include_file('desktop', 'lib/stupidtable.min', 'js', 'scan_ip'); ?>
<?php include_file('desktop', 'scan_ip_equipements', 'js', 'scan_ip'); ?>
<?php include_file('desktop', 'scan_ip_yes_equipements', 'js', 'scan_ip'); ?>
<?php include_file('desktop', 'scan_ip_no_equipements', 'js', 'scan_ip'); ?>