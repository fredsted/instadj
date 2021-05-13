<?php include('lib.php') ?><!DOCTYPE html>
<html>
<head>
	<?php if (getPlaylistId() !== '') { ?>
        <title>Check out my playlist on InstaDJ!</title>
	<?php } else { ?>
        <title>InstaDJ - Create &amp; Share YouTube Playlists</title>
	<?php } ?>

    <script type="text/javascript">window.playlist = '<?=getPlaylistId()?>';</script>
    <script src="https://www.youtube.com/player_api"></script>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="/instadj.js?version=<?= getversion('instadj.js') ?>"></script>
    <link rel="stylesheet" href="https://bootswatch.com/3/cyborg/bootstrap.css">
    <link rel="stylesheet" href="instadj.css?version=<?= getversion('instadj.css') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicons/favicon-16x16.png">

    <meta name="description"
          content="With InstaDJ you can create and share playlists with ease. It's great for parties or just queueing up videos."/>
    <meta charset="utf-8"/>
</head>
<body class="first-time">

<div id="main">

    <div id="intro"
         style="display: none; position: absolute; width: 300px; height: 20px; left: 50%; z-index: 999; top: -5px">
        <div style="position: relative; left: -50%; text-align: center; background: rgba(161, 161, 161, 0.8); padding: 10px; border-radius: 5px;">
            Loading playlist <img src="assets/images/miniloader.gif">
        </div>
    </div>

    <div id="player">
        <div id="videoDiv">Click a video to play</div>
    </div>

    <div id="share">
        <div id="sharingoptions">
            <div id="playlistmanagement">
                <form class="form-inline">
                    <button class="btn btn-default btn-sm" id="newplaylist" title="Create a new playlist">
                        <i class="glyphicon glyphicon-plus"></i> New
                    </button>
                    <div class="input-group">
                        <input type="text" class="form-control input-sm"
                               id="playlistcode" readonly="readonly"
                               title="Playlist link. Share it!"
                               value="<?php echo(isset($_GET['id']) ? 'https://instadj.com/' . preg_replace("/[^a-zA-Z0-9\s]/", "", $_GET['id']) : '') ?>"
                        />
                        <span class="input-group-btn">
                        <button class="btn btn-default btn-sm" id="copylink"><i class="glyphicon glyphicon-copy"></i> Copy</button>
                    </span>
                    </div>
                </form>
            </div>
            <div id="sharethisbuttons">
                <div id="sbtn001">
                    <a href="#" target="_blank">
                        <img rel="tooltip" src="/assets/images/shr_01.png"
                             title="Share this playlist via Facebook"/>
                    </a>
                </div>
                <div id="sbtn002">
                    <a href="#" target="_blank">
                        <img rel="tooltip" src="/assets/images/shr_02.png"
                             title="Share this playlist via Twitter"/>
                    </a>
                </div>
                <div id="stbtn3" title="Share this playlist!">
                    <a href="#" id="btnGenEmail">
                        <img rel="tooltip" src="/assets/images/shr_00.png"
                             title="Send an email with a link to this playlist"/>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="playlist" class="well">
        <ul id="playlistcontent"></ul>
    </div>

    <div id="playlistcontrols">
        <div class="btn-group" role="group">
            <button class="btn btn-default" id="previous" style="padding:8px 10px">
                <i class="glyphicon glyphicon-fast-backward"></i> Prev
            </button>
            <button class="btn btn-default" id="pauseplay" style="padding:8px 11px">
                <i class="glyphicon glyphicon-pause"></i> Pause/Play
            </button>
            <button class="btn btn-default" id="next" style="padding:8px 10px">
                <i class="glyphicon glyphicon-fast-forward"></i> Next
            </button>
            <button class="btn btn-default" id="shuffle" style="padding:8px 7px">
                <i class="glyphicon glyphicon-random"></i> Mix
            </button>
            <button class="btn btn-default" id="clear" style="padding:8px 7px">
                <i class="glyphicon glyphicon-erase"></i> Clear
            </button>
        </div>
    </div>

    <div id="controls">
        <a href="./" class="logo-sm"><img src="assets/images/instadj-black.png" id="smallogo" height="27"
                                          alt="InstaDJ"
                                          title="Create &amp; Share YouTube Playlists with InstaDJ"/></a>

        <div id="txtSearchwrapper">
            <input type="search" id="txtSearch" class="search-query form-control"
                   autofocus="autofocus" autocomplete="off"
                   title="Search and add videos to playlist" placeholder="YouTube keywords or playlist URL"/>

        </div>

        <div id="btngrpSearch" class="btn-group">
            <button class="btn btn-primary" id="btnSearch"><i class="glyphicon  glyphicon-search glyphicon-white"></i>
                Search
            </button>
            <button class="btn btn-default dropdown-toggle" id="drpdownSearchMenu" data-toggle="dropdown">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right" id="dropdownMenu">
                <li><a href="#" id="btnPlaylistId">YouTube Playlist URL</a></li>
                <li><a href="#" id="btnFavorites">User Favorites</a></li>
                <li><a href="#" id="btnUploads">User Uploads</a></li>
                <li class="divider"></li>
            </ul>
        </div>

        <div id="facebook">
            <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Finstadjdotcom&amp;width&amp;layout=button_count&amp;action=like&amp;show_faces=false&amp;share=false&amp;height=21"
                    scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;"
                    allowTransparency="true"></iframe>
        </div>
    </div>

</div>

<div id="grid"></div>

<!-- Analytics -->
<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-31010422-1']);
    _gaq.push(['_trackPageview']);

    (function () {
        var ga = document.createElement('script');
        ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'https://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
    })();
</script>
<script src="assets/js/json2.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/js/bootstrap3-typeahead-4.0.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
