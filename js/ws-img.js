var ws_img_top = 0;
var ws_img_left = 0;

$(window).load(function(){
	$('div.ws_img_navnext').hide();
	$('div.ws_img_navprev').hide();
});

function ws_img_setLeftRight(postid){
	if(postid.indexOf("photo") !== -1){
		
		var article = $('article#' + postid);
		var navprev = article.find('a.ws_img_navprev');
		var navnext = article.find('a.ws_img_navnext');
		
		var list = article.find('#photolist').text();
		var list = list.split(" ");
		var name = article.find('img').attr("src");
		name = name.split("./img/formatted/feed-")[1];
		var loc = list.indexOf(name);
		
		article.find('#imgnr').text(loc+1 + ' of ' + list.length );
		
		if(list.length <= 1){
			navprev.addClass("hidden");
			navnext.addClass("hidden");
		}
		else if(loc == 0){
			navprev.addClass("hidden");
			navnext.removeClass("hidden");
			navnext.click(function(event){
				article.spin();
				event.preventDefault();
				article.find('img').attr("src",  "./img/formatted/feed-" + list[1]);
				article.find('a#photo').attr("href", "./img/formatted/fs-" + list[1]);
				ws_img_setLeftRight(postid);
				article.find('img').load(function(){
					article.spin(false);
				});
			});
		}
		else if(loc == list.length-1){
			navnext.addClass("hidden");
			navprev.removeClass("hidden");
			navprev.click(function(event){
				article.spin();
				event.preventDefault();
				article.find('img').attr("src",  "./img/formatted/feed-" + list[list.length-2]);
				article.find('a#photo').attr("href", "./img/formatted/fs-" + list[list.length-2]);
				ws_img_setLeftRight(postid);
				article.find('img').load(function(){
					article.spin(false);
				});
			});
		}
		else{
			navprev.removeClass("hidden");
			navnext.removeClass("hidden");
			navprev.click(function(event){
				article.spin();
				event.preventDefault();
				article.find('img').attr("src",  "./img/formatted/feed-" + list[loc - 1]);
				article.find('a#photo').attr("href", "./img/formatted/fs-" + list[loc - 1]);
				ws_img_setLeftRight(postid);
				article.find('img').load(function(){
					article.spin(false);
				});
			});
			navnext.click(function(event){
				article.spin();
				event.preventDefault();
				article.find('img').attr("src",  "./img/formatted/feed-" + list[loc + 1]);
				article.find('a#photo').attr("href", "./img/formatted/fs-" + list[loc + 1]);
				ws_img_setLeftRight(postid);
				article.find('img').load(function(){
					article.spin(false);
				});
			});
		}
	}
}

function ws_img_setLeftRight_fs(link){		
	var article = link.closest('article');
	var list = article.find('#photolist').text();
	var list = list.split(" ");
	var wrapper = $('#ws_img_wrapper');
	var fullwrap = $('#ws_img_fullwrap');
	var navprev = wrapper.find('a.ws_img_navprev_fs');
	var navnext = wrapper.find('a.ws_img_navnext_fs');
	var name = wrapper.find('img').attr("src");	
	name = name.split("./img/formatted/fs-")[1];
	var loc = list.indexOf(name);	
	if(list.length <= 1){
		navprev.addClass("hidden");
		navnext.addClass("hidden");
	}
	else if(loc == 0){
		navprev.addClass("hidden");
		navnext.removeClass("hidden");
		navnext.click(function(event){
			fullwrap.spin();
			event.preventDefault();
			event.stopPropagation();
			wrapper.find('img').attr("src",  "./img/formatted/fs-" + list[1]);
			placePhotoWhenReady();
			ws_img_setLeftRight_fs(link);
		});
	}
	else if(loc == list.length-1){
		navnext.addClass("hidden");
		navprev.removeClass("hidden");
		navprev.click(function(event){
			fullwrap.spin();
			event.preventDefault();
			event.stopPropagation();
			wrapper.find('img').attr("src",  "./img/formatted/fs-" + list[list.length-2]);
			placePhotoWhenReady();
			ws_img_setLeftRight_fs(link);
		});
	}
	else{
		navprev.removeClass("hidden");
		navnext.removeClass("hidden");
		navprev.click(function(event){
			fullwrap.spin();
			event.preventDefault();
			event.stopPropagation();
			wrapper.find('img').attr("src",  "./img/formatted/fs-" + list[loc - 1]);
			placePhotoWhenReady();
			ws_img_setLeftRight_fs(link);
		});
		navnext.click(function(event){
			fullwrap.spin();
			event.preventDefault();
			event.stopPropagation();
			wrapper.find('img').attr("src",  "./img/formatted/fs-" + list[loc + 1]);
			placePhotoWhenReady();
			ws_img_setLeftRight_fs(link);
		});
	}
}

$(document).keyup(function(e) {
  if (e.keyCode == 27) {
  ws_img_stopFullScreen();
  // alert('tes');
  }   // esc
});

$(window).resize(function(){
	ws_img_placeWrappers();
	placePhotoWhenReady();
});

// function resizePhoto(){
	// // landscape = 90 or -90... rest is 180 or 0
	// var photo = $('#ws_img_photo');
	// var width = photo.width();
	// var height = photo.height();
	// var maxwidth = 0.9 * $(window).width();
	// var maxheight = 0.9 * $(window).height();
	// var aspectratio = width/height;
	// if(width > maxwidth){
		// photo.css("width", maxwidth);
		// photo.css("height", maxwidth / aspectratio);
	// }
	// if(height > maxheight){
		// ratio = maxheight / height;
		// photo.css("height", maxheight);
		// photo.css("width", maxheight / aspectratio);
	// }
// }

$(window).scroll(function(){
	ws_img_placeWrappers();
	placePhotoWhenReady();
});

function placePhotoWhenReady(){
	var img = $('#ws_img_photo');
	if(img.prop('complete')){
		placePhoto(img);
		$('#ws_img_fullwrap').spin(false);
		img.show();
	} 
	else {
		img.load(function() { 
			placePhoto(img); 
			$('#ws_img_fullwrap').spin(false);
			img.show();
		});
	}
}
	
	
function placePhoto(photo){
		// resizePhoto();
		if($(window).width() > photo.width()){
			var left = ($(window).width() - photo.width())/2;
		}
		else{
			var left = 0;
		}
		photo.css({ "position": "absolute", "left": left, "top" : '0 px' });
}

function ws_img_startFullScreen(link){
	var article = link.closest('article');
	var list = article.find('#photolist').text();
	var date = article.find('#photodate').text();
	var href = link.attr('href');
	ws_img_top = $(document).scrollTop();
	ws_img_left = $(document).scrollLeft();
	$('#wrapper').hide();
	var lefthtml = '<a href="" class="ws_img_navprev_fs"><div class="ws_img_navprev_fs color-bg-vlight color-dark padding-10-20">prev</div></a>';
	var righthtml = '<a href="" class="ws_img_navnext_fs"><div class="ws_img_navnext_fs color-bg-vlight color-dark padding-10-20">next</div></a>';
	var overlayhtml ='<div id="ws_img_overlay"></div>';
	var bghtml = '<div id="ws_img_bg" class="color-bg-vdark"></div>';
	var datehtml = '<h4 id="ws_img_date" class="padding-10-20 color-bg-vlight color-vdark">posted '+date+'</h4>';
	var html = '<div id="ws_img_fullwrap">' + bghtml + datehtml + '<div id="ws_img_wrapper"><img class="padding-20-20" id="ws_img_photo" src="'+ href + '"></img>' + overlayhtml + lefthtml + righthtml + '</div></div>';
	$(html).appendTo($('body')).hide();
	ws_img_placeWrappers();
	$('#ws_img_fullwrap').show();
	$('#ws_img_fullwrap').spin();
	$('#ws_img_overlay').click(function(event){
		ws_img_stopFullScreen();
	});
	$('#ws_img_photo').hide();
	placePhotoWhenReady();
	ws_img_hide_fs();
	ws_img_addListeners_fs();
	ws_img_setLeftRight_fs($(link));
}

function ws_img_stopFullScreen(){
	$('#ws_img_fullwrap').remove();
	$('#wrapper').show();
	$(document).scrollTop(ws_img_top).scrollLeft(ws_img_top);
}

function ws_img_placeWrappers(){
	$('#ws_img_bg').css({'top': $(document).scrollTop(), 'left': $(document).scrollLeft()});
	$('#ws_img_bg').width($(window).width());
	$('#ws_img_bg').height($(window).height());
	$('#ws_img_overlay').css({'top': $(document).scrollTop(), 'left': $(document).scrollLeft()});
	$('#ws_img_overlay').width($(window).width());
	$('#ws_img_overlay').height($(window).height());
	$('#ws_img_fullwrap').width($(window).width());
	$('#ws_img_fullwrap').height($(window).height());
	$('#ws_img_wrapper').width($(window).width());
	$('#ws_img_wrapper').height($(window).height());
}

function ws_img_hide_fs(){
	$('div.ws_img_navnext_fs').hide();
	$('div.ws_img_navprev_fs').hide();
}

function ws_img_addListeners_fs(){
	$('a.ws_img_navnext_fs').mouseenter(function(event){
		$(this).find('div.ws_img_navnext_fs').show();
	});
	$('a.ws_img_navnext_fs').mouseleave(function(event){
		$(this).find('div.ws_img_navnext_fs').hide();
	});
	$('a.ws_img_navprev_fs').mouseenter(function(event){
		$(this).find('div.ws_img_navprev_fs').show();
	});
	$('a.ws_img_navprev_fs').mouseleave(function(event){
		$(this).find('div.ws_img_navprev_fs').hide();
	});
}

function ws_img_addListenersTo(element){
	$(element).find('a.ws_img_navnext').mouseenter(function(event){
		$(this).find('div.ws_img_navnext').show();
		// alert('test');
	});
	$(element).find('a.ws_img_navnext').mouseleave(function(event){
		$(this).find('div.ws_img_navnext').hide();
	});
	$(element).find('a.ws_img_navprev').mouseenter(function(event){
		$(this).find('div.ws_img_navprev').show();
	});
	$(element).find('a.ws_img_navprev').mouseleave(function(event){
		$(this).find('div.ws_img_navprev').hide();
	});
	$(element).find('a#photo').click(function(event){
		event.preventDefault();
		ws_img_startFullScreen($(this));
	});
}