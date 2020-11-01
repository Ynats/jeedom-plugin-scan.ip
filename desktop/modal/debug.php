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

<div class="col-md-12">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title"># ip a</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip::printShell("sudo ip a");
            ?>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="panel panel-primary" id="div_functionalityPanel">
        <div class="panel-heading">
            <h3 class="panel-title"># ip route show default</h3>
        </div>
        <div class="panel-body">
            <?php
                scan_ip::printShell("ip route show default | awk '/default/ {print $3}'");
            ?>
        </div>
    </div>
</div>

<?php
$subReseau = scan_ip::getSubReseauEnable();
if ($subReseau["subReseauEnable"] > 0) {
    foreach ($subReseau["subReseau"] as $sub) {
        if ($sub["enable"] == 1) {
            ?>

            <div class="col-md-12">
                <div class="panel panel-primary" id="div_functionalityPanel">
                    <div class="panel-heading">
                        <h3 class="panel-title"># sudo arp-scan --interface=<?php echo $sub["name"] ?> --localnet --ouifile=ieee-oui.txt</h3>
                    </div>
                    <div class="panel-body">
                        <?php
                            scan_ip::printShell("sudo arp-scan --interface=" . $sub["name"] . " --localnet --ouifile=".scan_ip::$_file_oui);
                        ?>
                    </div>
                </div>
            </div>

            <?php
        }
    }
}
?>
