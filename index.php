<!DOCTYPE html>
<html>
<head>
	<title>Create &amp; Share YouTube Playlists with InstaDJ</title>
	<script src="js/jquery-1.7.2.min.js"></script>

	<script type="text/javascript">
	   var loadplaylist = '<?php echo (isset($_GET['id']) ? preg_replace("/[^a-zA-Z0-9\s]/", "", $_GET['id']) : '') ?>';  		
	</script>
	<script src="js/jquery.dragsort-0.5.1.min.js"></script>
	<script src="js/instadj.js"></script>
	<meta name="description" content="With InstaDJ you can create and share playlists with ease. It's great for parties or just queueing up videos." />
	<meta charset="utf-8" />
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="instadj.css">

	<style type="text/css">
		.hidden { display: none; }
	</style>
</head>
<body>
	<script type="text/javascript">
		$('body').hide();
		$('body').fadeIn(500);
	</script>

	<div id="main">
		<div id="intro">
			<img src="instadj-big.png" width="447" height="148">

			<h1>Create and share YouTube playlists.</h1>


		</div>
		
		<div id="player" class="hidden">
			<div id="videoDiv">Click video to play</div>
		</div>
		
		<div id="share" class="hidden">
			
			<button class="btn btn-success" id="btnGenerate" disabled="disabled"><i class="icon icon-envelope icon-white"></i> Share/Save</button>
	   		<div id="sharingoptions" style="display:none;">
	   			<div id="sharethisbuttons" style="float:left;">	
					 <div id="stbtn1">
						 <a href="" id="btnGenFacebook" target="_blank">
						 	<img rel="tooltip" src="/facebook_32.png" width="32" height="32" title="Share on Facebook"  />
						 </a>
					 </div>
					 <div id="stbtn2">
						 <a href="#" id="btnGenTwitter" target="_blank">
						 	<img rel="tooltip" src="/twitter_32.png" width="32" height="32" title="Share on Twitter"  />
						 </a>						 
					 </div>
					 <div id="stbtn3" title="Email this playlist.">
						 <a href="#" id="btnGenEmail">
							<img rel="tooltip" src="/email_32.png" width="32" height="32" title="Share via e-mail" />
						 </a>
					 </div>
				</div>  
	   			<input rel="tooltip" type="text" class="input" id="playlistcode" readonly="readonly" value="" title="Playlist link. Share it!"></input>				 		
		    </div>
		</div>
		
		<div id="playlist" class="well hidden">
			<ul class="nav nav-pills nav-stacked" id="playlistcontent"></ul>
		</div>

		<div id="playlistcontrols" style="display:none;">
			<div class="btn-group" style="margin-left: 25px;">
				<button class="btn" id="previous"><i class="icon icon-fast-backward"></i> Previous</button>
				<button class="btn" id="pauseplay"><i class="icon icon-pause"></i> Pause/Play</button>
				<button class="btn" id="next"><i class="icon icon-fast-forward"></i> Next</button>
				<button class="btn" id="shuffle"><i class="icon icon-random"></i> Shuffle</button>
			</div>
		</div>
		
		<div id="controls">
			<a href="./" class="hidden logo-sm"><img src="instadj.png" id="smallogo" title="Create &amp; Share YouTube Playlists with InstaDJ" /></a>

			<input type="text" id="txtSearch" class="input search-query" placeholder="YouTube Search…" />
			
			<div id="btngrpSearch" class="btn-group" style="float:left;">
				 <button class="btn btn-primary" id="btnSearch"><i class="icon  icon-search icon-white"></i> Search</button>
				 <button class="btn dropdown-toggle" id="drpdownSearchMenu" data-toggle="dropdown">
				   <span class="caret"></span>
				 </button>
				 <ul class="dropdown-menu" id="dropdownMenu">
				   <li><a href="#" id="btnFavorites">User Favorites</a></li>
				   <li><a href="#" id="btnUploads">User Uploads</a></li>
				   <li class="divider"></li>
				 </ul> 
			</div> 

			
			<div id="viewoptions" style="float:right;" class="hidden">
				<button class="btn" id="togglelight"    data-toggle="button"><i class="icon icon-adjust"></i> Theme</button>
				<button class="btn" id="togglelistview" data-toggle="button"><i class="icon icon-resize-small"></i> Grid</button>
			</div>
		</div>
		
		<div id="recent">
		<?php include('recentsearches.inc.php'); ?>
		<div id="facebook">
				<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Finstadjdotcom&amp;width&amp;layout=box_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:49px; margin-left: 10px; height:65px;" allowTransparency="true"></iframe>
		</div>

		</div>

	</div>
	<div id="grid" class="hidden"></div>
	
	<script src="js/json2.js"></script>

	<!-- Facebook -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

	<!-- Analytics -->
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-31010422-1']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>

	<script src="js/jquery.scrollTo-1.4.2-min.js"></script>
	<script src="bootstrap/js/bootstrap-tooltip.js"></script>
	<script src="bootstrap/js/bootstrap-button.js"></script>
	<script src="bootstrap/js/bootstrap-dropdown.js"></script>
	<script src="bootstrap/js/bootstrap-typeahead.js"></script>

</body>
</html>
