function block_switch(id) {
    var klappText = document.getElementById('sb' + id);
    var klappBild = document.getElementById('picb' + id);
    if (klappText.style.display == 'block') {
        klappBild.src = 'pic/plus.gif';
        type = "hide";
    } else {
        klappBild.src = 'pic/minus.gif';
        type = "show";
    }
    var hb = $.cookie('hb');
    if (typeof hb == 'undefined') {
        hb = []
        $.cookie('hb', serialize(hb));
    } else
        hb = unserialize(hb);
    if (hb.indexOf(id) == -1) {
        hb[id] = id;
    } else {
        hb = delete(hb[id]);
    }
    if (typeof hb == 'boolean')
        hb = []
    $.cookie('hb', serialize(hb));
    jQuery(document).ready(function () {
        jQuery('#sb' + id).slideToggle("medium");
    });
}