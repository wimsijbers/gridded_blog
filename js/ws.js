//basic setup

//sizing should be same as in style.css
var basewidth = 250;
var baseheight = 160;
var margin = 5;
var maxrows = 200;
var columns = 0;
var mincolumns = 3;
var maxcolumns = 4;
//needed vars
var grid = null;
var posts = null;

//indexOf <IE8 fix
if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

//rounded borders not supported?
if (!Modernizr.borderradius) {
	$.getScript('js/jquery.corner.js', function() {
		$('header').corner('bottom');
		$('article.page').corner('bottom');
	});
}



function getXpitch(nr){
	return (nr) * (basewidth + 2 * margin);
}
function getYpitch(nr){
	return (nr) * (baseheight + 2 * margin);
}

function getFirstEmptyColumn(){
	for(var i=0; i<columns; i++){
		if(!grid[0][i]){
			var test = false;
			for(var j=0; j<maxrows; j++){
				if(grid[j][i]){
					test = true;
				}
			}
			if(!test){
				return i;
			}
		}
	}
	return 0;
}

function getWidth(){
	if($('#content').hasClass('page')){
		return 3 * basewidth + 2 * margin;
	}
	if(getFirstEmptyColumn() == 0){
		var adjusted_columns = columns;
	} else{
		var adjusted_columns = getFirstEmptyColumn();
	}
	return adjusted_columns * basewidth + ((adjusted_columns-2) * 2 * margin);
}

function setup(){
	columnwidth = basewidth + 2 * margin;
	rowheight = baseheight + 2 * margin;
	cleanGrid();
	posts = new Array();
	whenPage();
	if(is_touch_device()){
		$('.articlehover').removeClass('articlehover');
		// $('.invarticlehover').removeClass('invarticlehover');
	}
}

function whenPage(){
	if($('#content').hasClass('page')){
		$('#subtitle').hide();
		$('#wrapper').addClass('page');
		$('header').addClass('page');
	}
}

function cleanGrid(){
	grid = new Array(maxrows);
	for(var i=0; i<maxrows; i++) {
		grid[i] = new Array(columns);
		for(var j=0; j<columns; j++){
			grid[i][j]=false;
		}
	}
}
function Post(){
	this.id = '';
	this.rows = 0;
	this.columns = 0;
}

function parseClass(str){
	var patt=new RegExp("column[0-9]?","i");
	var parsed_columns = str.match(patt)[0];
	patt=new RegExp("row[0-9]?","i");
	var parsed_rows = str.match(patt)[0];
	return new Array(parsed_rows.replace( /^\D+/g, ''), parsed_columns.replace( /^\D+/g, ''));
}

function getAllPosts(){
	var posts = new Array();
	$("article").each(function(){
		var post = new Post();
		post.id = this.id;
		var size = parseClass($(this).attr("class"));
		post.rows = size[0];
		post.columns = size[1];
		posts.push(post);
	});
	return posts;
}

function checkIfPostExists(id){
	for(var i = 0; i < posts.length ; i++){
		if (posts[i].id == id){
			return true;
		}	
	}
	return false;
}

function getNewPosts(){
	var newposts = new Array();
	// var debug = '';
		$("article").each(function(){
		if(this.id != '' && !checkIfPostExists(this.id)){
			// debug = debug + this.id + ' -';
			var post = new Post();
			post.id = this.id;
			var size = parseClass($(this).attr("class"));
			post.rows = size[0];
			post.columns = size[1];
			newposts.push(post);
		}
		else{
			//exists
		}
	});
	// alert(debug);
	return newposts;
}

//function includes search in x and y 
function getFirstEmptyAfter(x,y){
	for(var i=x; i<maxrows; i++){
		for(var j=y; j<columns; j++){
			if(grid[i][j]== false){
				return new Array(i,j);
			}
		}
		y = 0;
	}
	return false;
}

function getFirstEmptyRow(){
	for(var i=0; i<maxrows; i++){
		if(!grid[i][0]){
			var test = false;
			for(var j=0; j<columns; j++){
				if(grid[i][j]){
					test = true;
				}
			}
			if(!test){
				return i;
			}
		}
	}
}

function fillEmpties(){
	var lastrow = getFirstEmptyRow();
	for(var i=0; i<lastrow; i++) {
		for(var j=0; j<columns; j++){
			if(!grid[i][j]){
				createDummyAt(j,i);
				grid[i][j] = 'dummy';
			}
		}
	}
}

function cleanDummies(){
	$("article").each(function(){
		if($(this).hasClass('dummy')){
			$(this).remove();
		}
	});
}

function createDummyAt(left,top){
	left = getXpitch(left);
	top = getYpitch(top);
	var html = '<article class="dummy color-bg-vlight column1 row1" style="position:absolute; top:'+ top +'px; left:'+ left +'px;"></article>';
	$('#content').append(html);
}

function getFit(rows, columns){
	var doit = true;
	pos = new Array(0,0);
	while(doit){
		var fits = true;
		pos = getFirstEmptyAfter(pos[0], pos[1]);
		if(!pos){
			break;
		}
		outside:
		for(var i=0; i<rows; i++){
			for(var j=0; j<columns; j++){
				if(grid[i+pos[0]][j+pos[1]]!= false){
					fits = false;
					break outside;
				}
			}
		}
		if(fits == true){
			doit = false;
			return pos;
		}
		else{
			// go 1 column further or next row
			if(pos[1]<columns-1){
				pos[1]++;
			} else if(pos[0]<maxrows-1){
				pos[0]++;
				pos[1] = 0;
			}
			else{
				return false;
			}
			
		}
	}
	return pos;
}

function calculateColumns(){
	// 80 is 2 * feed margin
	// var new_columns = Math.floor(($(window).width()-80)/columnwidth);
	if($('#content').length != 0 && $('#content').hasClass('feed')){
		var new_columns = Math.floor(($(window).width())/columnwidth);
		if(new_columns <=mincolumns){
			new_columns = mincolumns;
		} else if(new_columns >= maxcolumns){
			new_columns = maxcolumns;
		}
		if(new_columns != columns){
			columns = new_columns;
			reGrid();
		}
	}
}

function adjustHeaderWidth(){
	$("header").width(getWidth());
}
function adjustFooterLocation(){
	$('footer').css({ "position": "absolute", "top": getYpitch(getFirstEmptyRow()), "left": margin, "width": getWidth()+margin*2});
	
}

function reGrid(){
	setDebug();
	posts = getAllPosts();
	cleanGrid();
	cleanDummies();
	placePosts(posts);
	fillEmpties();
}

// function reGridNewPosts(newposts){
	// posts = posts.concat(newposts);
	// placePosts(newposts);
	// fillEmpties();
// }

function placePosts(posts){
	for(var i = 0; i < posts.length ; i++){
		var pos = getFit(posts[i].rows, posts[i].columns);
		if(pos){
			placePost(posts[i], pos);
		}
	}
}

function placePost(post, pos){
	for(var i=0; i<post.rows; i++){
		for(var j=0; j<post.columns; j++){
			grid[i+pos[0]][j+pos[1]] = post.id;
		}
	}
	$('#' + post.id).css({ "position": "absolute", "top": getYpitch(pos[0]), "left": getXpitch(pos[1])});
}

$(window).on('load', function() {
   $("#wrapper").hide();
});

$(window).load(function(){
	// $('#content').spin();
	setup();
	var update = $.ajax({
		type:"POST",
		url:"../php/process_background.php"
	});
	addInfoListener();
	addListenersTo(document);
	$('#more').click(function(event){
		event.preventDefault();
		loadMore();
	});
	if(!is_touch_device()){
		$("img.socialimg").fadeTo("fast",0.7);
		$("img.socialimg").hover(
			function(){$(this).fadeTo("fast",1);},
			function(){$(this).fadeTo("fast",0.7);}
		);
	}
	calculateColumns();
	adjustHeaderWidth();
	adjustFooterLocation();
	adjustWrapperLocation();
	hideHover();
	loadBgSizer();
	$("#wrapper").fadeIn('slow');
	// $('#content').spin(false);
});

function loadBgSizer(){
	var aspectRatio      = $("#bg").width() / $("#bg").height();		    		
	function resizeBg() {
		if ( ($(window).width() / $(window).height()) < aspectRatio ) {
		    $("#bg").removeClass().addClass('bgheight');
		} else {
		    $("#bg").removeClass().addClass('bgwidth');
		}
	}                			
	$(window).resize(resizeBg)// .trigger("resize");
}

function hideHover(){
	$('.articlehover').hide();
	$('.invarticlehover').show();
}
function hideHoverTo(posts){
	for(var i = 0; i < posts.length ; i++){
		$('#' + posts[i].id).find(' .articlehover').hide();
		$('#' + posts[i].id).find(' .invarticlehover').show();
	}
}

function addInfoListener(){
	$('article').mouseenter(function(event){
		$(this).find('.articlehover').show();
		$(this).find('.invarticlehover').hide();
		var pass = $(this);
		timer = setTimeout(function(event){
			pass.find('.delayedhide').hide();
		}, 2000);
		ws_img_setLeftRight($(this).attr('id'));
	});
		$('article').mouseleave(function(event){
		$(this).find('.articlehover').hide();
		$(this).find('.invarticlehover').show();
		clearTimeout(timer);
	});
}
function addInfoListenersTo(posts){
	for(var i = 0; i < posts.length ; i++){
		$('#' + posts[i].id).mouseenter(function(event){
			$(this).find('.articlehover').show();
			$(this).find('.invarticlehover').hide();
			var pass = $(this);
			timer = setTimeout(function(event){
				pass.find('.delayedhide').hide();
			}, 2000);
			ws_img_setLeftRight($(this).attr('id'));
		});
		$('#' + posts[i].id).mouseleave(function(event){
			$(this).find('.articlehover').hide();
			$(this).find('.invarticlehover').show();
			clearTimeout(timer);
		});
	}
}

function loadMore(){
	var href = $('#more').attr("href").split('=');
	href = href[1];
	history.replaceState({link: href}, null, '?before=' + href);
	$('#more').text('Loading...');
	// $('#more').spin();
	var load = $.post("./php/process_morePosts.php", {before: href}, function(data){
		$('footer').remove();
		$('#content').append(data);
		var newposts = getNewPosts();
		reGrid();
		// reGridNewPosts(newposts);
		addInfoListenersTo(newposts);
		adjustFooterLocation();
		hideHoverTo(newposts);
		// var debug = '';
		for(var i = 0; i < newposts.length ; i++){
			addListenersTo('#' + newposts[i].id);
			// debug = debug + newposts[i].id + '-----';
		}
		// alert(debug);
		$('#more').click(function(event){
			event.preventDefault();
			loadMore();
		});
	});
	// $('#more').stop();
}



function addListenersTo(element){
	$(element).find('a').hover(function(event){
		if($(this).hasClass('linkstyle1')){
			$(this).toggleClass('color-vlight color-flash1 color-bg-vdark color-bg-flash3');
			// $(this).closest('h3').toggleClass('color-bg-dark color-bg-flash3');
		} else{
			$(this).toggleClass('color-flash1 color-dol');
		}
	});
	ws_img_addListenersTo(element);
}


$(window).resize(function(){
	calculateColumns();
	adjustHeaderWidth();
	adjustFooterLocation();
	adjustWrapperLocation();
});

// window.addEventListener('popstate', function(event) {
	// if(event.state){
		// // alert(event.state.link);
	// }
// });

function setDebug(text){
	$('#debug').text(text);
}

function adjustWrapperLocation(){
	if($(window).width() > getWidth() + 40){
		var left = ($(window).width() - getWidth()-40)/2;
	}
	else{
		var left = 0;
	}
	$('#wrapper').css({ "position": "absolute", "left": left })
}

function is_touch_device() {
	return !!('ontouchstart' in window) // works on most browsers 
      // || !!('onmsgesturechange' in window); // works on ie10
	  // return false;
};

function formhash(form, password) {
   var p = document.createElement("input");
   form.appendChild(p);
   p.name = "p";
   p.type = "hidden"
   p.value = hex_sha512(password.value);
   password.value = "";
   form.submit();
}