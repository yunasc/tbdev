function block_switch(clickTarget) {

    /* array with image links */
    var togSrc = [ 'pic/plus.gif', 'pic/minus.gif' ];

    /* initialize objects */
    $ = jQuery;
    var toggleImage = $(clickTarget);
    var blockId = toggleImage.data('bid');
    var block = $('#sb' + blockId);

    /* get cookie, decode it and then toggle block id in array */
    var hiddenBlocks = $.cookie('hb');
    if (typeof hiddenBlocks == 'undefined') {
        hiddenBlocks = [];
        $.cookie('hb', serialize(hiddenBlocks));
    } else
        hiddenBlocks = unserialize(hiddenBlocks);

    /* check if blockId is in array, and if true remove. otherwise add it to array */
    if ($.inArray(blockId, hiddenBlocks) > -1) {
        hiddenBlocks = $.grep(hiddenBlocks, function (value) {
            return value != blockId;
        });
    } else
        hiddenBlocks = $.merge([blockId], hiddenBlocks);

    /* set the cookie to new value */
    $.cookie('hb', serialize(hiddenBlocks));

    /* this block fixed to perfection by Taras Zakus */
    (function (togSrc) {
        toggleImage.attr('src', function (index, value) {
            return value === togSrc[0] ? togSrc[1] : togSrc[0];
        });
    })([ 'pic/plus.gif', 'pic/minus.gif']);

    /* use jquery to collapse/show block content */

    if ($.support.transition) {
        block.toggleClass('orbitalBlockHideCSS3');
        /* Dark magic. Don't use anymore */
        block.bind('transitionend', function () { recalc_height(this);});
    } else
        block.slideToggle({duration: 'medium', step: function f() {
            if ($(this).css('display') == 'inline-block')
                $(this).css('display', 'block');
        }});
}

/* Some dark magic. Never ever use anymore anywhere */
function recalc_height(e) {
    $(e).css('max-height', $(e).prop('scrollHeight'));
}