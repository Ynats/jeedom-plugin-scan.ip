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

scan_ip_maj::cleanAfterUpdate();

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
    
    <div id="dataConfig"></div>
    
    <fieldset>
<?php
        scan_ip_tools::vueSubTitle("{{Widgets dédiés à Scan.Ip}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Widget Network}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Permet d'afficher un widget avec l'ensemble du réseau}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" data-l1key="widget_network">
                    <option value="1">{{Afficher le widget de votre réseau}}</option>
                    <option value="0">{{Masquer le widget de votre réseau}}</option>
                </select> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Widget Alertes}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Permet d'afficher le widget des }} <?php echo scan_ip::$_defaut_alerte_new_equipement ?> {{ derniers équipements entrant dans votre réseau}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" data-l1key="widget_new_equipement">
                    <option value="1">{{Afficher le widget des }} <?php echo  scan_ip::$_defaut_alerte_new_equipement ?> {{ derniers équipements non enregistrés dans le réseau}}</option>
                    <option value="0">{{Masquer le widget des }} <?php echo  scan_ip::$_defaut_alerte_new_equipement ?> {{ derniers équipements non enregistrés dans le réseau}}</option>
                </select> 
            </div>
        </div>
        
        <div id="show_avance" style="display:none;">   
<?php
        scan_ip_tools::vueSubTitle("{{Votre réseau (Mode avancé)}}", "config");
?>     
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Routeur}}</label>
            <div class="col-lg-5">
                <select class="configKey form-control" data-l1key="add_network_routeur">
                    <option value="0">{{Retirer le routeur de la liste}}</option>
                    <option value="1">{{Ajouter le routeur dans la liste}}</option>
                </select> 
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Jeedom}}</label>
            <div class="col-lg-5">
                <select class="configKey form-control" data-l1key="add_network_jeedom">
                    <option value="0">{{Retirer Jeedom de la liste}}</option>
                    <option value="1">{{Ajouter Jeedom dans la liste}}</option>
                </select> 
            </div>
        </div>
<?php
        scan_ip_tools::vueSubTitle("{{Nombre d'essaie lors des scans (Mode avancé)}}", "config");
?>             
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Option Retry}}
            <sup><i class="fa fa-question-circle tooltips" title="{{Cela conditionne la durée du scan mais permet d'avoir de meilleurs retours sur l'exploration de votre réseau'}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" data-l1key="add_retry_scan">
                    <option value="3">{{3 tentatives (à defaut)}}</option>
                    <option value="4">{{4 tentatives}}</option>
                    <option value="5">{{5 tentatives}}</option>
                    <option value="6">{{6 tentatives}}</option>
                </select> 
            </div>
        </div>
                      
<?php
        scan_ip_tools::vueSubTitle("{{Compatibilité avec le navigateur Safari (Mode avancé)}}", "config");
?>             
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Option à n'activer que si vous utilisez Safari}}
            <sup><i class="fa fa-question-circle tooltips" title="{{Pour désactiver les options du menu de sélection des bridges déjà associés. (Sur les autres navigateurs la donnée est cachée)}}"></i></sup>
            </label>
            <div class="col-lg-5">
                <select class="configKey form-control" data-l1key="add_compatibility_safari">
                    <option value="0">{{Désactiver le mode compatibilité}}</option>
                    <option value="1">{{Activer le mode compatibilité}}</option>
                </select> 
            </div>
        </div>
            
    </div> 
           
    <div id="show_oui" style="display:none;">        
<?php
        scan_ip_tools::vueSubTitle("{{Base de données OUI (Mode debug)}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Fichiers présents}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Ce fichier sert à récupérer le nom des constructeurs de matériel}}"></i></sup>
            </label>
            <div class="col-lg-5"><?php echo scan_ip_tools::printFileOuiExist() ?> <sup><i class="fa fa-question-circle tooltips" title="{{Mise à jour le}} <?php echo scan_ip_tools::getDateFile(scan_ip::$_file_oui) ?>"></i></sup>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label"></label>
            <div class="col-lg-5"><?php echo scan_ip_tools::printFileIatExist() ?> <sup><i class="fa fa-question-circle tooltips" title="{{Mise à jour le}} <?php echo scan_ip_tools::getDateFile(scan_ip::$_file_iab) ?>"></i></sup>
            </div>
        </div>
<?php
        scan_ip_tools::vueSubTitle("{{Check de la version (Mode debug)}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">Vérification des données</label>
            <div class="col-lg-5"><?php echo scan_ip_maj::printVersionPlugin(); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">Vérification des commentaires</label>
            <div class="col-lg-5"><?php echo scan_ip_maj::printCheckPlugin(scan_ip_maj::checkJsonCommentaires_v1_1()); ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">Vérification des équipements</label>
            <div class="col-lg-5"><?php echo scan_ip_maj::printCheckPlugin(scan_ip_maj::checkJsonEquipements_v1_1()); ?>
            </div>
        </div>
<?php
        scan_ip_tools::vueSubTitle("{{Dépendances (Mode debug)}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">iproute2</label>
            <div class="col-lg-5"><?php echo scan_ip_shell::dependancy_iproute2()[1] ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">net-tools</label>
            <div class="col-lg-5"><?php echo scan_ip_shell::dependancy_net_tools()[1] ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">arp-scan</label>
            <div class="col-lg-5"><?php echo scan_ip_shell::dependancy_arp_scan()[1] ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">etherwake</label>
            <div class="col-lg-5"><?php echo scan_ip_shell::dependancy_etherwake()[1] ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">wakeonlan</label>
            <div class="col-lg-5"><?php echo scan_ip_shell::dependancy_wakeonlan()[1] ?>
            </div>
        </div>
    </div>
<?php
        scan_ip_tools::vueSubTitle("{{Cadence de rafraichissement}}", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Cadence de rafraîchissement}}
                <sup><i class="fa fa-question-circle tooltips" title="{{Il est recommandé de laisser ce paramètre à }} <?php echo scan_ip::$_defaut_cron_pass ?> {{minute}}"></i></sup>
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
