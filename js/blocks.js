function block_switch(id) {

    /* array with image links */
    var togSrc = [ 'pic/plus.gif', 'pic/minus.gif' ];

    /* some magic i event don't wanna know it */
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
    /* magic */

    /* the work itself */
    /* set the cookie to new value */
    $.cookie('hb', serialize(hb));

    var toggleImage = $('#picb' + id);

    /* i didn't find better way to implement toggle. sorry, perfectionists ;) */
    if (toggleImage.attr('src') == togSrc[0])
        toggleImage.attr('src', togSrc[1]);
    else
        toggleImage.attr('src', togSrc[0]);

    /* use jquery to collapse/show block content */
    jQuery('#sb' + id).slideToggle("medium");
}