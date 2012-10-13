

jQuery(function(){

	var items = jQuery('.tabs .texto');

	var tabs = jQuery('#tabs A');

	if(tabs.length == 0){
		return;
	}

	tabs.click(function(ev){
		clickFormTab(ev.target);
	});
	
	clickFormTab(tabs[0]);

});

function clickFormTab(target){

	jQuery('.tabs .tab_fieldset').css('position', 'absolute').css('visibility', 'hidden');

	var tabs = jQuery('#tabs A.active');
	tabs.removeClass('active');
	target.addClass('active');

	var href = target.getAttribute('href').substr(1);
	
	jQuery('.tabs .'+href).css('position', 'static').css('visibility', 'visible');

}

function validateTabs(value){
	return true;
}