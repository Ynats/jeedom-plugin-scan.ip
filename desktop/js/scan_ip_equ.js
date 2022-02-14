
$("#hiden_type_widget").change(function () {
    if($("#hiden_type_widget").val() == "normal"){
        $("#hiden_type_normal").show();
        $("#scan_ip_info_widget").hide();
        $("[data-action='copy']").show();
        $("[data-action='configure']").show();
        $("[data-action='remove']").show();
        $("#scan_ip_widget_network_parametre").hide();
        $("#scan_ip_commandes").show();
    } else { 
        $("#hiden_type_normal").hide();
        $("#scan_ip_info_widget").show();
        $("[data-action='copy']").hide();
        $("[data-action='configure']").hide();  
        $("[data-action='remove']").hide();
        if($("#hiden_type_widget").val() == "network"){
            $("#scan_ip_widget_network_parametre").show();
            $("#scan_ip_commandes").hide();
        } else {
            $("#scan_ip_widget_network_parametre").hide();
            $("#scan_ip_commandes").show();
        }
    }
});

if (!$("#offline_time option:selected").length) {
    $("#offline_time option[value='4']").attr('selected', 'selected');
}

function hideSelect(NbSelect) {
    if(NbSelect > 0){
        $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.ajax.associations.php", function (result) {
            for (let plug = 0; plug <= NbSelect; plug++) {
                $.each(result, function (mac, tableau) {
                    $.each(tableau, function (key, value) {
                        var current = $('#mac_id').val();
                        if (mac != current) {
                            $(".plug_element_plugin_" + plug + " option[value='" + value + "']").hide();
                        }
                    });
                });
            }

        });
    }
}

function hideSelectSafari(NbSelect) {
    if(NbSelect > 0){
           $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.ajax.associations.php", function (result) {
            for (let plug = 0; plug <= NbSelect; plug++) {
                $.each(result, function (mac, tableau) {
                    $.each(tableau, function (key, value) {
                        var current = $('#mac_id').val();
                        if (mac != current) {
                            // hidden/display:none non reconnu sous safari dans les select option
                            $(".plug_element_plugin_" + plug + " option[value='" + value + "']").attr('disabled',true);
                        }
                        $(".plug_element_plugin_" + plug + " option[value='" + $('.plug_element_plugin_' + plug).find(":selected").val() + "']").attr('disabled',false);
                    });
                });
            }

        }); 
    }
}

$('#offline_time').change(function () {
    verifCadence();
    getEquipementById();
    getEquipementCommentaire();
});

function getEquipementById() {
    $("#ConstrunctorMac").val("");
    $("#LastMAC").val("");
    $("#LastIp").val("");
    
    $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.ajax.getEquipementById.php", function (result) {
        $.each(result, function (id, value) {
            var current = $('#mac_id').val();
            if (id == current) {
                if(value["equipement"]){
                    $("#ConstrunctorMac").val(value["equipement"]);
                } else {
                    $("#ConstrunctorMac").val("???");
                }
                $("#LastMAC").val(value["mac"]);
                $("#LastIp").val(value["ip_v4"]);
            }
        });
    });
}

function getEquipementCommentaire() {
    $("#Commentaire").val("");

    $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.ajax.getCommentaireById.php", function (result) {
        $.each(result, function (id, value) {
            var current = $('#mac_id').val();
            if (id == current) {
                $("#Commentaire").val(value);
            }
        });
    });
}

function verifCadence() {
    var offline_time = $("#offline_time").val();
    var cron_pass = $("#cronPass").attr('data-cron');
    var delta = offline_time / cron_pass;

    if (delta < 2) {
        $('#div_alert_scan_ip_cadence').showAlert({message: "{{Si vous validez cette configuration, il est possible que certains de vos équipements soient indiqués comme hors-ligne alors qu'ils ne le sont pas.}}", level: 'warning'});
    } else {
        $('#div_alert_scan_ip_cadence').hide();
    }
}

function timeCron() {
    $.getJSON("/plugins/scan_ip/core/ajax/scan_ip.ajax.conf.php", function (result) { 
        if(result["mode_plugin"] === "debug"){ 
            $("[data-action='remove']").show();
        } 
        $("#cronPass").attr('data-cron', result["cron_pass"]);
        $("#cronPass").val("La cadence de rafraichissement se fait toutes les " + result["cron_pass"] + " minute(s)");
        verifCadence();
    });
}

function verifEquipement(nb) {
    if(nb > 0){
    
        var cpt = 0;
        var nbElement = [];
        var listCore = [];
        var valueEquipement = "";
        
        $('#div_alert_scan_ip_equipement_core').hide();

        for (x = 1; x <= nb; x++) {
            var val = $('.plug_element_plugin_' + x).find(":selected").val();
            if(val){
                var split = val.split("|");
                if (split[0] != "") {
                    nbElement.push(split[0]);
                }
                if (split[0] == "core") {
                    listCore.push(x);
                }
            } 
            
            $('.plug_element_plugin_' + x).attr("style", "");
        }

        red = nbElement.reduce((p, c) => (p[c]++ || (p[c] = 1), p), {});

        $.each(red, function (index1, value1) {
            if (value1 > 1) {
                valueEquipement += "Attention, cet équipement est associé " + value1 + " fois au plugin <span style='font-weight: bold;'>" + index1 + "</span>.<br />";
            } 
        });
        
        if (valueEquipement != "") {
            $('#div_alert_scan_ip_equipement').showAlert({message: valueEquipement + "Il est possible que cela génère des conflits s'il s'agit d'une erreur de votre part.", level: 'warning'});
        } else {
            $('#div_alert_scan_ip_equipement').hide();
        }
        
        $.each(listCore, function (index2, value2) {
            if(value2 != ""){ 
                $('#div_alert_scan_ip_equipement_core').showAlert({message: "{{Attention, cet équipement est associé au bridge <span style='font-weight: bold;'>core</span> de votre Jeedom !<br />Ce qui veut dire que Scan.Ip peut modifier votre configuration Jeedom.<br />Cette association s'adresse à des utilisateurs avancés car cela peut générer des erreurs dans votre config globale ou sur le bon fonctionnement d'autres plugins.}}", level: 'warning'});
                $('.plug_element_plugin_' + value2).attr("style", "color : #cc8500 !important;");
            }  
        });

    }
}

// Synchro
$('#bt_syncEqLogic').off('click').on('click', function () {
    syncEqLogicWithOpenScanId();
});

function syncEqLogicWithOpenScanId() {
    $.ajax({
        type: "POST",
        url: "plugins/scan_ip/core/ajax/scan_ip.ajax.php",
        data: {
            action: "syncEqLogicWithOpenScanId",
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            window.location.reload();
        }
    });
}

// Au changement du menu de sélection on reproduit la valeur dans le champ
$('#scan_ip_mac_select').change(function () {
    var scan_ip_CopyMacId = $('#scan_ip_mac_select').find(":selected").val();
    $("#mac_id").val(scan_ip_CopyMacId);
    getEquipementById();
    getEquipementCommentaire();
});

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }

    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
    tr += '<div class="row">';
    tr += '<div class="col-sm-6">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name">';
    tr += '</div>';
    tr += '</div>';
    tr += '</td>';
    tr += '<td>';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '</td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
//    if (isset(_cmd.type)) {
//        $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
//    }
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
