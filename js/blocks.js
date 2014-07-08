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
        hb = [];
        $.cookie('hb', serialize(hb));
    } else
        hb = unserialize(hb);
    var index = hb.indexOf(id);
    if (index > -1) {
    	hb.splice(index, 1);
    } else {
        hb[hb.length] = id;
    }
    if (typeof hb == 'boolean')
        hb = [];
    $.cookie('hb', serialize(hb));
    jQuery(document).ready(function () {
        jQuery('#sb' + id).slideToggle("medium");
    });
}