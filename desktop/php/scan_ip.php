<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('scan_ip');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());

foreach ($eqLogics as $eqLogic) {
    if($eqLogic->getConfiguration('type_widget', 'normal') == "network"){
        $idWidgetNetwork = $eqLogic->getId();
        break;
    }
}

?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i>
                <br>
                <span>{{Ajouter une adresse MAC}}</span>
            </div>
            <div class="cursor logoSecondary" id="bt_syncEqLogic">
                <i class="fas fa-sync-alt"></i>
                <br>
                <span>Synchroniser</span>
            </div>
            <div class="cursor logoSecondary" id="bt_scanIpNetwork">
                <i class="fas fa-sitemap"></i>
                <br>
                <span>{{Réseau}}</span>
            </div>
            <div class="cursor logoSecondary" id="bt_scanIpEquipement">
                <i class="fas fa-sitemap"></i>
                <br>
                <span>{{Equipements}}</span>
            </div>
<?php if(scan_ip::getConfigMode() == "debug"){ ?>
            <div class="cursor logoSecondary" id="bt_scanIpDebug">
                <i class="fas fa-medkit"></i>
                <br>
                <span>{{Debug}}</span>
            </div>
<?php  }  ?>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i>
                <br>
                <span>{{Configuration}}</span>
            </div>
        </div>
        <legend><i class="fas fa-table"></i> {{Mes configurations}}</legend>
        <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
                <a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a>
                <a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
                <a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
            </span>
        </div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                
                <br />
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Nom de l'appareil à suivre}}</label>
                            <div class="col-sm-3">
                                <input type="text" class="eqLogicAttr form-control" onchange="timeCron()" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" id="scan_ip_name_eq" placeholder="{{Nom de la configuration}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                            <div class="col-sm-3">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                        foreach (jeeObject::all() as $object) {
                                            echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Catégorie}}</label>
                            <div class="col-sm-9">
                                <?php
                                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                        echo '<label class="checkbox-inline">';
                                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                        echo '</label>';
                                    }
                                ?>
                            </div>
                        </div>
                        <div id="scan_ip_info_widget" style="display:none;">
                            <br />
                            <div class="form-group" >
                                <label class="col-sm-3 control-label">Information</label>
                                <div class="col-sm-9">
                                    <span style="color:green;">Cet équipement est géré par Scan.Ip et permet d'afficher l'état de votre réseau dans un widget dédié.</span>
                                </div>
                            </div>
                            <div class="form-group" >
                                <label class="col-sm-3 control-label"></label>
                                <div class="col-sm-9">
                                    <span style="color:green;">Pour gérer sa visibilité, vous devez passer par l'espace configuration du plugin. </span>
                                </div>
                            </div>
                            
                        </div>
                        
                        
                    <div id="hiden_type_normal">
                        
                        <div class="form-group" id="show_scan_ip_widget_normal_visibility">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                                <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                            </div>
                        </div>
                        
                        <div style="display:none;">
                            <select id="hiden_type_widget" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="type_widget">
                                <option value="normal">Normal</option>
                                <option value="network">Network</option>
                            </select>
                        </div>
                        
<?php    
                        scan_ip::vueSubTitle("{{Associer une adresse MAC}}");
?>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Rechercher une adresse MAC}}</label>
                            <div class="col-sm-5">
                                <select id="scan_ip_mac_select" style="color: #039be5 !important;" class="form-control">
                                    <option value="">Sélectionner parmi les adresses Mac non enregistrées</option>
                                    <?php
                                        scan_ip::printSelectOptionAdressMac();
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Adresse MAC associée}}</label>
                            <div class="col-sm-5">
                                <input type="text" onchange="hideSelect(<?php echo scan_ip::$_defaut_bridges_by_equipement ?>);" maxlength="17" id="scan_ip_adressMacTemp" class="form-control eqLogicAttr" data-l1key="configuration"  data-l2key="adress_mac" placeholder="{{##:##:##:##:##:##}}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Constructeur}}</label>
                            <div class="col-lg-5">
                                <input type="text" id="ConstrunctorMac" class="form-control" style="color: #039be5 !important;" readonly="">
                            </div>
                        </div>
                     
                                  
                    <div id="show_off_line"<?php scan_ip::showEquCadence() ?>>                        
<?php
                        scan_ip::vueSubTitle("{{On Line ou Off line ? (Mode avancé)}}");
?>                        

                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Rappel "Configuration"}} <sup><i class="fa fa-question-circle tooltips" title="{{Il est conseillé d'avoir au moins 2 raffraichissements minimum'}}"></i></sup></label>
                            <div class="col-lg-5">
                                <input type="text" id="cronPass" data-cron="" class="form-control" style="color: #039be5 !important;" readonly="">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Présumé hors-ligne au bout de }} <sup><i class="fa fa-question-circle tooltips" title="{{Il est recommendé de laisser ce paramètre à }} <?php echo scan_ip::$_defaut_offline_time ?> {{minutes}}"></i></sup>
                            </label>
                            <div class="col-lg-5">
                                <select class="eqLogicAttr form-control" id="offline_time" data-l1key="configuration" data-l2key="offline_time">
                                    <option value="2">{{2 minutes}}</option>
                                    <option value="3">{{3 minutes}}</option>
                                    <option value="4">{{4 minutes (recommandé)}}</option>
                                    <option value="5">{{5 minutes}}</option>
                                    <option value="6">{{6 minutes}}</option>
                                    <option value="7">{{7 minutes}}</option>
                                    <option value="8">{{8 minutes}}</option>
                                    <option value="9">{{9 minutes}}</option>
                                    <option value="10">{{10 minutes}}</option>
                                    <option value="15">{{15 minutes}}</option>
                                </select> 
                            </div>
                        </div>
                    </div>
                        
<?php
                        scan_ip::vueSubTitle("{{Réveiller votre équipement à distance ?}}");
?> 
                        <div class="form-group">
                            <label class="col-lg-3 control-label">{{Wake-on-LAN}} <sup><i class="fa fa-question-circle tooltips" title="{{Vérifiez que votre équipement soit compatible et/ou que l'option ait bien été activé pour que cela fonctionne}}"></i></sup>
                            </label>
                            <div class="col-lg-5">
                                <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="enable_wol">
                                    <option value="0">{{Désactiver la commande WoL sur cet équipement}}</option>
                                    <option value="1">{{Activer la commande WoL sur cet équipement}}</option>
                                </select> 
                            </div>
                        </div>
                        
<?php
                            scan_ip::vueSubTitle("Associer cette adresse MAC à un ou plusieurs bridges (optionnel)");
                            scan_ip::bridges_printOptionEquiements();
 ?>
                   
                        </div>
                    </fieldset>
                </form>
 
            </div>
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>{{Nom}}</th><th>{{Action}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php include_file('desktop', 'scan_ip', 'js', 'scan_ip'); ?>
<?php include_file('desktop', 'scan_ip_equ', 'js', 'scan_ip'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>

