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
	jQuery.get("switch_blocks.php", {"type": type, "bid": id}, function(data){}, 'html');
	jQuery(document).ready(function(){
		jQuery('#sb' + id).slideToggle("medium");
	});
}