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

require_once dirname(__FILE__) . "/../../../../plugins/scan_ip/core/class/scan_ip.require_once.php";

?>
<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"># ip a</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip_shell::printShell("sudo ip a");
            ?>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"># ifconfig</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip_shell::printShell("sudo ifconfig");
            ?>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"># ip route show default</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip_shell::printShell("ip route show default | awk '/default/ {print $3}'");
            ?>
        </div>
    </div>
</div>

<?php
$subReseau = scan_ip_shell::getSubReseauEnable();
$retry = config::byKey('add_retry_scan', 'scan_ip', 3);
if ($subReseau["subReseauEnable"] > 0) {
    foreach ($subReseau["subReseau"] as $sub) {
        if ($sub["enable"] == 1) {
            ?>

            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"># sudo arp-scan -r <?php echo $retry ?> --interface=<?php echo $sub["name"] ?> --localnet --ouifile=ieee-oui.txt</h3>
                    </div>
                    <div class="panel-body">
                        <?php
                            scan_ip_shell::printShell("sudo arp-scan -r " . $retry . " --interface=" . $sub["name"] . " --localnet --ouifile=".scan_ip::$_file_oui);
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
    }
} 
else {
?>
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"># sudo arp-scan --localnet --ouifile=oui.txt --iabfile=iab.txt</h3>
                    </div>
                    <div class="panel-body">
                        <?php
                            scan_ip_shell::printShell("sudo arp-scan --localnet --ouifile=".scan_ip::$_file_oui." --iabfile=" .  scan_ip::$_file_iab);
                        ?>
                    </div>
                </div>
            </div>
<?php 
} 
?>
<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"># Equipements</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip_tools::printArray(scan_ip_json::getJson(scan_ip::$_jsonEquipement));
            ?>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"># Mappings</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip_tools::printArray(scan_ip_json::getJson(scan_ip::$_jsonMapping));
            ?>
        </div>
    </div>
</div>
<?php
$errorMacIp = scan_ip_maj::getCheckAllEquipements_v1_1();
if($errorMacIp != NULL){
?>
<div class="col-md-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"># Analyse des équipements</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip_tools::printArray($errorMacIp);
            ?>
        </div>
    </div>
</div>
<?php
}
?>