

jQuery(function(){
	
	setInterval(function ping(){
		jQuery.get(Phaxsi.Util.url('admin/ping_json'));
	}, 300000); //Ping every 5 minutes to mantain session open
	
	jQuery('a.confirm-delete').click(function(ev){
		var name = jQuery(this).attr('title');
		var url = jQuery(this).attr('data-url');
		if(!confirm(listDeleteConfirmation(name))){
			ev.preventDefault();
		}
	});
	
	//$("#menu li a.current-main-entry").parent().find("ul").toggle(); 
	
	$("#menu li a.main-menu-entry").click(function(e) {
			if($(this).next().length == 0){
				return;
			}			
			$(this).parent().siblings().find("ul").slideUp("normal"); // Slide up all sub menus except the one clicked
			$(this).next().slideToggle("normal"); // Slide down the clicked sub menu
			e.preventDefault();
	});

	$("#menu li a.no-submenu").click( // When a menu item with no sub menu is clicked...
		function () {
			window.location.href=(this.href); // Just open the link instead of a sub menu
			return false;
		}
	);

// Sidebar Accordion Menu Hover Effect:

	$("#menu li .nav-top-item").hover(
		function () {
			$(this).stop().animate({ paddingRight: "25px" }, 200);
		}, 
		function () {
			$(this).stop().animate({ paddingRight: "15px" });
		}
	);
	
	
});



Phaxsi.Validator.Error.prototype =  {
	show: function(inputName, message){
		var element = document.getElementById("error-message-"+inputName);
		if(!element)return;
		element.innerHTML = message;
		element.style.display = "";
		var image = document.getElementById( "error-image-"+inputName );
		if(image) image.style.display = "";
	},

	hide: function(inputName){
		var element = document.getElementById("error-message-"+inputName);
		if(!element)return;
		element.style.display = "none";
		var image = document.getElementById( "error-image-"+inputName );
		if(image) image.style.display = "none";
	}
}


jQuery(function(){
	
	var filters = jQuery('SELECT.entity-filter');
	var i;
	
	for(i =0; i < filters.length; i++){
		jQuery(filters[i]).change(function(ev){
			var value = ev.target.value;
			location.href = Phaxsi.Util.url('admin/'+value);
		});
	}

	jQuery('INPUT[type=text].entity-filter').each(function(){
		
		var el = jQuery(this);
		
		if(el.val() == ''){			
			el.val(el.attr('data-default'));
			el.addClass('entity-default');
		}

		el.focus(function(ev){
			var target = jQuery(ev.target);
			if(target.val() == target.attr('data-default')){
				target.removeClass('entity-default');
				target.val('');
			}
			else{
				jQuery(target).select();
			}
		});
		
		el.blur(function(ev){
			var target = jQuery(ev.target);
			if(target.val() == ''){
				target.val(target.attr('data-default'));
				target.addClass('entity-default');
			}
		});

		el.keyup(function(ev){
			var key = ev.keyCode;
			if(key == 13){
				var url = jQuery(ev.target).attr('data-url');
				var text_filters = jQuery('INPUT[type=text].entity-filter');
				var self = this;
				
				text_filters.each(function(){
					if(self.value != self.getAttribute('data-default')){
						url += '&'+self.getAttribute('name')+'='+encodeURIComponent(self.value);
					}
					self.disabled = self;
				});
				
				jQuery.get(Phaxsi.Util.url('admin/'+url), function(data){
					location.href = Phaxsi.Util.url('admin/'+data.url);
				}, 'json');
			}
		});
		
	});

});
	
jQuery(function(){
	
	if(!$.tableDnD){
		return;
	}

	$('TABLE.ordered').tableDnD(
		{dragHandle: 'order',
		 onDragClass: 'on-drag',
		 onDrop: function(table, row){
			 var data = {
				first: jQuery(row).prevAll().last().attr('data-id'),
				prev: jQuery(row).prev().attr('data-id'),
				next: jQuery(row).next().attr('data-id'),
				last: jQuery(row).nextAll().last().attr('data-id')
			 }

			 var url = Phaxsi.Util.url(jQuery(row).find('img.order-handler').attr('data-url'));
			 jQuery.post(url, data);
		 }}
	);

	$('.gallery UL.ordered').sortable({
		handle: '.order-handler',
		update: function(e, ui){
			var data = {
				first: jQuery(ui.item).prevAll().last().attr('data-id'),
				prev: jQuery(ui.item).prev().attr('data-id'),
				next: jQuery(ui.item).next().attr('data-id'),
				last: jQuery(ui.item).nextAll().last().attr('data-id')
			 }
			 var url = Phaxsi.Util.url(jQuery(ui.item).find('img.order-handler').attr('data-url'));
			 jQuery.post(url, data);
		}
	});

	$('.gallery UL.ordered').disableSelection();

	
});

