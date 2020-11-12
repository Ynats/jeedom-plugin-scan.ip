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

require_once dirname(__FILE__) . "/../../../plugins/scan_ip/core/class/scan_ip.bridges.php";
require_once dirname(__FILE__) . "/../../../plugins/scan_ip/core/class/scan_ip.tools.php";

// Brigde affachés par paquet de ...
$paquetBridges = ceil(count(scan_ip_bridges::getJsonBridges())/3);

scan_ip_tools::cleanAfterUpdate();

?>
<form class="form-horizontal">
    
    <div class="form-group">
        <div class="col-lg-2" style="right:15px; position: absolute;">
            <select onchange="scan_ip_mode_plugin()" class="configKey form-control" data-l1key="mode_plugin" id="scan_ip_mode">
                <option value="normal">{{Mode normal}}</option>
                <option value="advanced">{{Mode avancé}}</option>
                <option value="debug">{{Mode debug}}</option>
            </select>
        </div>
    </div>
    
    <fieldset>
<?php
        scan_ip_tools::vueSubTitle("{{Widgets dédiés à Scan.Ip}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Votre réseau}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Permet d'afficher un widget avec l'ensemble du réseau}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" id="cron_pass" data-l1key="widget_network">
                    <option value="1">{{Afficher le widget de votre réseau}}</option>
                    <option value="0">{{Masquer le widget de votre réseau}}</option>
                </select> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label"><?php echo  scan_ip::$_defaut_alerte_new_equipement ?> {{derniers équipements non enregistrés}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Permet d'afficher un widget }} <?php echo scan_ip::$_defaut_alerte_new_equipement ?> {{ derniers équipements entrant dans votre réseau}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" id="cron_pass" data-l1key="widget_new_equipement">
                    <option value="1">{{Afficher le widget des }} <?php echo  scan_ip::$_defaut_alerte_new_equipement ?> {{ derniers équipements non enregistrés dans le réseau}}</option>
                    <option value="0">{{Masquer le widget des }} <?php echo  scan_ip::$_defaut_alerte_new_equipement ?> {{ derniers équipements non enregistrés dans le réseau}}</option>
                </select> 
            </div>
        </div> 
        
    <div id="show_oui" style="display:none;">        
<?php
        scan_ip_tools::vueSubTitle("{{Base de données OUI (Mode debug)}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Fichier présent}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Ce fichier sert à récupérer le nom des constructeurs de matériel}}"></i></sup>
            </label>
            <div class="col-lg-5)"><?php echo scan_ip_tools::printFileOuiExist() ?> <sup><i class="fa fa-question-circle tooltips" title="{{Mise à jour le}} <?php echo scan_ip_tools::getDateFile(scan_ip::$_file_oui) ?>"></i></sup>
            </div>
        </div>
    </div>
<?php
        scan_ip_tools::vueSubTitle("{{Cadence de rafraichissement}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Cadence de rafraichissement}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Il est recommendé de laisser ce paramètre à }} <?php echo scan_ip::$_defaut_cron_pass ?> {{minute}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" id="cron_pass" data-l1key="cron_pass">
                    <option value="1">{{1 minute (recommandé)}}</option>
                    <option value="2">{{2 minutes}}</option>
                    <option value="3">{{3 minutes}}</option>
                </select> 
            </div>
        </div>
    
    <div id="show_sous_reseau" style="display:none;">     
    <?php
        scan_ip_tools::vueSubTitle("{{Spécifier des plages à scanner (Mode avancé)}}", "config");
        echo scan_ip_tools::printInputSubConfig(); 
    ?> 
    </div>
    <?php
        scan_ip_tools::vueSubTitle("{{Bridges : Plugins compatibles}}", "config");
    ?> 
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Liste des Plugins pris en compte}}</label>
            <div class="col-lg-2">
                <?php echo scan_ip_bridges::bridges_printPlugs($paquetBridges, 0); ?>
            </div>
            <div class="col-lg-2">
                <?php echo scan_ip_bridges::bridges_printPlugs($paquetBridges, $paquetBridges); ?>
            </div>
            <div class="col-lg-2">
                <?php echo scan_ip_bridges::bridges_printPlugs($paquetBridges, ($paquetBridges*2)); ?>
            </div>
        </div>
    </fieldset>
    <br />
</form>

<?php include_file('desktop', 'scan_ip_configuration', 'js', 'scan_ip'); ?>