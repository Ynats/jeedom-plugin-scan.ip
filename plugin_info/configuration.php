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

?>

<form class="form-horizontal">
    <fieldset>
<?php
    scan_ip::vueSubTitle("Cadence de rafraichissement", "config");
?>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Cadence de rafraichissement}}</label>
            <div class="col-lg-2">
                <select class="configKey form-control" data-l1key="cron_pass">
                    <option value="5">{{5 minutes}}</option>
                    <option value="4">{{4 minutes}}</option>
                    <option value="3">{{3 minutes}}</option>
                    <option value="2">{{2 minutes}}</option>
                    <option value="1">{{1 minute}}</option>
                </select> 
            </div>
        </div>
    <?php
        scan_ip::vueSubTitle("Plage(s) à scanner", "config");
        
        echo scan_ip::printInputSubConfig(); 
    
        scan_ip::vueSubTitle("Plug & Play : Plugins compatibles", "config");
    ?> 
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Liste des Plugins pris en compte}}</label>
            <div class="col-lg-2">
                <?php echo scan_ip::plugs_printPlugs(); ?>
            </div>
        </div>
    </fieldset>
    <br />
</form>

