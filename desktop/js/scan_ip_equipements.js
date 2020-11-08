
$('#scan_ip_tab_equipement').click(function () {
    $("#scan_ip_modal_equipement").show();
    $("#scan_ip_modal_no_equipement").hide();
    $("#scan_ip_tab_equipement").addClass("active");
    $("#scan_ip_tab_no_equipement").removeClass("active");
});

$('#scan_ip_tab_no_equipement').click(function () {
    $("#scan_ip_modal_equipement").hide();
    $("#scan_ip_modal_no_equipement").show();
    $("#scan_ip_tab_equipement").removeClass("active");
    $("#scan_ip_tab_no_equipement").addClass("active");
});