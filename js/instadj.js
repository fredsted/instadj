	var first = true;
	var currentID;
	var currentState;
	var gpfirst = true;
	var done = false;
	var startfrom = 51;
	var listview = false;
	var tempPlayerState;

	// YouTube Api
	var tag = document.createElement('script');
	tag.src = "http://www.youtube.com/player_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

	(function($){
	 
	    $.fn.shuffle = function() {
	 
	        var allElems = this.get(),
	            getRandom = function(max) {
	                return Math.floor(Math.random() * max);
	            },
	            shuffled = $.map(allElems, function(){
	                var random = getRandom(allElems.length),
	                    randEl = $(allElems[random]).clone(true)[0];
	                allElems.splice(random, 1);
	                return randEl;
	           });
	 
	        this.each(function(i){
	            $(this).replaceWith($(shuffled[i]));
	        });
	 
	        return $(shuffled);
	 
	    };
	 
	})(jQuery);
	
	// On Document Load
	$(function() {
		//$.get('recentsearches.inc.php', function(data){
		//	$("#recent").html(data);
		//});
		
		$('#recent').on("click", "a", function(event){
			$('#txtSearch').attr("value", $(this).attr('data-href') );
			$('#btnSearch').click();
		});

		$("#smallogo").tooltip({placement: 'bottom'});
		
		var subreddits = [
			'futuregarage',
			'futurebeats',
			'futurefunkairlines',
			'dubstep',
			'realdubstep',
			'electronicmusic',
			'idm',
			'music',
			'purplemusic',
			'housemusic',
			'listentothis',
			'metal',
			'indierock',
			'jazz',
			'hiphopheads',
			'classicalmusic',
			'trance',
			'dnb',
			'mashups'
		];
		
		$(subreddits).each(function(index){
			$("#dropdownMenu").append('<li><a href="#" class="btnReddit" data-playlist="'+subreddits[index]+'">/r/'+subreddits[index]+'</a></li>');

		});
		
		$("#dropdownMenu").on("click", ".btnReddit", function(e){
			firstactions();
			loadRedditPlaylist($(this).attr('data-playlist'));
		});
		
		
		$("#playlistcontent").dragsort({ 
			dragSelector: "li", 
			dragEnd: function() { }, 
			dragBetween: false, 
			placeHolderTemplate: "<li><b><a>[place video here]</a></b></li>", 
			scrollContainer: "#playlist", 
			scrollSpeed: "10" 
		}); 				
					
		if (loadplaylist != "") {
			$('#controls').hide();
			$('#main').fadeIn();
			$('#intro').html('<h1>Loading playlist…</h1><br /><img src="load.gif" />');
			setTimeout("getplaylist(loadplaylist); $('#controls').fadeIn();", 2000);
		} 
			
		$("#grid").on("click", ".loadmore", function(event){
			$('.loadmoreimg').attr('src','load.gif');
			scrolltothis = $('.loadmore').prev();
			loadwhat = $('.loadmore').find('a').attr('href');
			geturl = loadwhat+'&from='+startfrom;
			if (listview === true) { geturl = geturl + '&class=videolist'}
			
			$.ajax({
			  url: geturl,
			  success: function(data) {
			   	$('.loadmore').detach();
			    $('#grid').append(data);
				$('#grid').scrollTo(scrolltothis, {duration:700}); 
			  }
			});
			
			startfrom = startfrom + 50;
			return false;
		});
			
		$(document).on("click", ".video", function(event) {
		  	var video_id = $(this).children("a").attr("href").match(/v=(.{11})/)[1];
		  	var video_title = $(this).children("a").text();
		  	$(this).children("a").fadeOut('fast').fadeIn('fast');
			addtoplaylist(video_id, video_title);
		  	return false;
		});
		
		$(document).on("click", ".playlistitem", function(event){
			playid($(this).attr("data-id"), $(this).text());
			//console.log($(this).attr("data-id"));
			$("#playlistcontent li").removeClass('active');
			$(this).parent().addClass('active');
		});
		
		$(document).on("click", ".playlistremove", function(event){
			$(this).parent().remove();
		});
		
		$('#grid').on("mouseenter", ".video", 
			function(event){ 
				$(this).children('.related').fadeIn(100);
			});
		$('#grid').on("mouseleave", ".video", 
			function(event){ 
				$(this).children('.related').hide(); 
			});
			
		$('#grid').on("click", ".related", 
			function() {
			$(this).children("a").html('<img src="miniloader.gif" />');
			geturl = $(this).children("a").attr("data-href");
			if (listview === true) { geturl = geturl + '&class=videolist'}
			
			$.ajax({
			  url: geturl,
			  success: function(data) {
			    $('#grid').html(data);
			    $('#grid').scrollTo($('#grid').children().first(), {duration: 500});
			  }
			});
			
			
			
			return false;
		});
			
		$("#playlistcode").click(function(){
			$(this).select();
		});
		
		$("#btnSearch").click(function(){
		
			if ($('#txtSearch').attr('value') == '') {
				$('#txtSearch').attr('placeholder', 'Enter song or artist…');
				$('#txtSearch').focus();
			} else {
		
			firstactions();
		
			$("#txtSearch").addClass("loading");
			
			geturl = 'yt.php?action=search&q='+$('#txtSearch').attr('value');
			
			if (listview === true) { geturl = geturl + '&class=videolist'}
			
			$.ajax({
			  url: geturl,
			  success: function(data) {
				$('#grid').empty();
			    $('#grid').html(data);
			    $("#txtSearch").removeClass("loading");
			    $('#grid').scrollTo($('#grid').children().first(), {duration: 500});
			  }
			});
			
			}
		});
			
		$("#txtSearch").keypress(function(e) {
		 //Enter key
			if (e.which == 13) {
				$("#btnSearch").click();
				 return false;
			}
		});
			
		$("#btnRelated").click(function(){
			$.ajax({
			  url: 'yt.php?action=related&id='+currentID,
			  success: function(data) {
				$('#grid').empty();
			    $('#grid').html(data);
			    $("#txtSearch").removeClass("loading");
			  }
			});
		});
			
		$('#txtSearch').click(function(){
			$(this).select();
		});

		$('#pauseplay').click(function(){
			if (currentState == 1) {
				ytPlayer.pauseVideo() 
			} 
			else {
				ytPlayer.playVideo();
			}
		});

		$("#previous").click(function(){
				if ($('#playlistcontent li.active').length == 0){
					$('#playlistcontent li').first().find("a").click();
				
				} else {
					$('#playlist').scrollTo($('#playlistcontent li.active').prev().find("a"), {duration: 100});
					$('#playlistcontent li.active').prev().find("a").click();
				}
		});

		$("#next").click(function(){
				if ($('#playlistcontent li.active').length == 0){
					$('#playlistcontent li').first().find("a").click();
				
				} else {
					$('#playlist').scrollTo($('#playlistcontent li.active').next().find("a"), {duration: 100});
					$('#playlistcontent li.active').next().find("a").click();
				}
		});

		$('#shuffle').click(function(){
			$('#playlist ul li').shuffle();
		});	
			
		$("#btnGenerate").click(function(){ 
			$("#playlistcode, #smallogo").tooltip({placement: 'top'});
			playlistarray = {};
			
			$(".playlistitem").each(function(index){
				playlistarray[$(this).attr('data-id')] = $(this).text();
			});
			
			playlistJSON = JSON.stringify(playlistarray);
							
			$('#stbtn1, #stbtn2, #stbtn3').empty();
			
			$.ajax({
				type: 'POST',
				url: 'store.php?action=store',
				data: 'playlistdata='+escape(playlistJSON),
				success: function(data){
					$url = "http://instadj.com/"+data;
					
					$("#playlistcode").attr("value", $url);
												
			         stWidget.addEntry({
			                 "service":"facebook",
			                 "element":document.getElementById('stbtn1'),
			                 "url":$url,
			                 "title":"Check out my InstaDJ playlist.",
			                 "type":"large",
			                 "text":""
			         });
			         
			         stWidget.addEntry({
			                 "service":"twitter",
			                 "element":document.getElementById('stbtn2'),
			                 "url":$url,
			                 "title":"Check out my InstaDJ playlist.",
			                 "type":"large",
			                 "text":""
			         });		
			         
			         stWidget.addEntry({
			                 "service":"email",
			                 "element":document.getElementById('stbtn3'),
			                 "url":$url,
			                 "title":"Check out my InstaDJ playlist.",
			                 "type":"large",
			                 "text":""
			         });
				
				}
				
			});
			

			$(this).attr("disabled","disabled");
			$("#sharingoptions").fadeIn();
		});


		
		$('#txtSearch').focus();

	});
	
	function onPlayerReady(event) {
		event.target.playVideo();
		
		ytPlayer.addEventListener("onStateChange", "ytState");
	}
	  
	function onPlayerStateChange(event) {
		if (event.data == 0) {
			if (($('#playlistcontent li').length == 0) || ($('#playlistcontent li').length == 1)) {
				// Don't do anything
			} else {
				if ($('#playlistcontent li.active').length == 0){
					$('#playlistcontent li').first().find("a").click();
				
				} else {
					$('#playlist').scrollTo($('#playlistcontent li.active').next().find("a"), {duration: 500});
					$('#playlistcontent li.active').next().find("a").click();
				}
			
			}
		}
		currentState = event.data;
		
		if (event.data == 1){
			ytPlayer.setPlaybackQuality('hd720');
		}
	}	
	
	$(document).on("click", "#togglelistview", function(event){
		$('.video').toggleClass('videolist');	
		if (listview === false) {
			listview = true;
		} else {
			listview = false;
		}
	});
				
		
	$(document).on("click", "#btnFavorites", function(event){
		if ($('#txtSearch').attr('value') == '') {
			$('#txtSearch').attr('placeholder', 'Enter username…');
			$('#txtSearch').focus();
		} else {
		
			firstactions();
		
			$("#txtSearch").addClass("loading");
			
			$.ajax({
			  url: 'yt.php?action=userfavorites&user='+$('#txtSearch').attr('value'),
			  success: function(data) {
				$('#grid').empty();
			    $('#grid').html(data);
			    $("#txtSearch").removeClass("loading");
			    $('#grid').scrollTo($('#grid').children().first(), {duration: 500});
			  }
			});
			
		}
		
	});
		
	$(document).on("click", "#btnUploads", function(event){
			
			if ($('#txtSearch').attr('value') == '') {
				$('#txtSearch').attr('placeholder', 'Enter username…');
				$('#txtSearch').focus();
			} else {
				firstactions();
			
				$("#txtSearch").addClass("loading");
				
				if (first == true) {
					$('#player').show();
					$('#playlist').show();
					$('#intro').fadeOut('fast');
				}
				
				$.ajax({
				  url: 'yt.php?action=useruploads&user='+$('#txtSearch').attr('value'),
				  success: function(data) {
					$('#grid').empty();
				    $('#grid').html(data);
				    $("#txtSearch").removeClass("loading");
				    $('#grid').scrollTo($('#grid').children().first(), {duration: 500});
				  }
				});
		}
	});

		
	$('li').click(function(event){
		event.stopPropagation();	
	});
		
	function playid(id, title) {
		currentID = id;
		
		if (first === true) {		
			first = false;	
					
	        ytPlayer = new YT.Player('player', {
			  height: '390px',
			  width: '250px',
			  playerVars: { 
        		'autoplay': 1, 
        		'autohide': 1,
        		'theme':'light',
        		'color':'red',
        		'iv_load_policy':'3',
        		'showinfo':'0'
        	  }, 
			  videoId: id,
			  events: {
				'onReady': onPlayerReady,
				'onStateChange': onPlayerStateChange
			  }
			});
			$('#playlistcontrols').fadeIn('fast');
		  	
		  	
	  	} else {
	  	  	if(ytPlayer) {
				ytPlayer.loadVideoById(id, 0, 'large');
			}
			
	  	}
	}
	
	/*
	 	-1 (unstarted)
		0 (ended)
		1 (playing)
		2 (paused)
		3 (buffering)
		5 (video cued).
	*/
		
	function addtoplaylist(id, title, noscroll) {
		activehtml = '';
		if (first == true) {
			activehtml = ' class="active"';
		}
		// Play first added item
		
		try {
			tempPlayerState = ytPlayer.getPlayerState();
		} catch(e) {
			tempPlayerState = 9;
		}
		
		if (($('#playlistcontent').children().length == 0) || (tempPlayerState == -1) || (tempPlayerState == 0)) {
			playid(id, title);
			$('li.active').removeClass("active");
			activehtml = ' active';
		};
		
		
		$('#btnGenerate').show().removeAttr('disabled');
		
		$('#playlistcontent').append($('<li class="'+activehtml+'"><a class="playlistitem" href="#" data-id="'+id+'">'+title+'</a><button class="btn btn-mini playlistremove"><i class="icon-minus"></i></button></li>').hide().fadeIn());
		
		if (!(noscroll == true)) {
			$('#playlist').scrollTo($('#playlist ul').children().last(), {duration: 500});
		}
	}
		
	function firstactions() {
		if (first == true) {

			$('#controls').css({
				'position': 'absolute', 
				'right': '10px', 
				'top': '10px', 
				'left':'415px', 
				'margin':'0px', 
				'width':'auto'
			});

			$('#txtSearch').css({
				'width':'190px'
			});

			$('#txtSearch').select();

			$('#recent, #intro').hide();

			$('.hidden').show();
		}
	}
	

	
	function loadRedditPlaylist(subreddit) {
	$("#txtSearch").addClass("loading");
		$.ajax({
			url: 'reddit/redditrss.php?reddit='+subreddit,
			success: function(data){
				$('#grid').empty();
				$(data).find('item').each(function(){
					var id = $(this).children('a').text();
					var title = $(this).children('title').text();
					$("#txtSearch").removeClass("loading");
					var classListview = '';
					if (listview === true) { classListview = ' videolist'; }
					item = $("#grid").append('<div class="video '+classListview+'" style="background-image:url(http://i.ytimg.com/vi/'+id+'/1.jpg);"><a href="http://www.youtube.com/watch?v='+id+'" class="title"> '+title+'</a><span class="related" style="display:none;"><a href="#" data-href="yt.php?action=related&id='+id+'">Related</a></span></div>');
					
				});
			}
		});
	}
		
	function getplaylist(id) {
		firstactions();
	
		$.ajax({
			url: 'store.php?action=get&id='+id,
			success: function(data){
				if (data.indexOf("_instadjerr_") == -1) {
					var jsonobject = JSON.parse(data, function(key, value){
						if (typeof(value)=='string') {
							addtoplaylist(key, value, true);
						}
						
					});
				} else {
					alert("error loading playlist, please contact instadj@fredsted.me");
				}
			}
		});

	}
		
