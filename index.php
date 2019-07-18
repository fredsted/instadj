<?php include('lib.php') ?><!DOCTYPE html>
<html>
<head>
    <?php if (isset($_GET['id'])) { ?>
        <title>Check out my playlist on InstaDJ!</title>
    <?php } else { ?>
        <title>InstaDJ - Create &amp; Share YouTube Playlists</title>
    <?php } ?>

    <script type="text/javascript">
        window.playlist = '<?php echo(isset($_GET['id']) ? preg_replace("/[^a-zA-Z0-9\s]/", "", $_GET['id']) : '') ?>';
    </script>
    <script src="https://www.youtube.com/player_api"></script>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="assets/js/instadj.js?version=<?=getversion()?>"></script>
    <link rel="stylesheet" href="https://bootswatch.com/3/cyborg/bootstrap.css">
    <link rel="stylesheet" href="assets/css/instadj.css?version=<?=getversion()?>">

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
            <div id="sharethisbuttons" style="float:left;">
                <div id="sbtn001">
                    <a href="" target="_blank">
                        <img rel="tooltip" src="/assets/images/s001.png" width="32" height="32"
                             title="Share this playlist!"/>
                    </a>
                </div>
                <div id="sbtn002">
                    <a href="#" target="_blank">
                        <img rel="tooltip" src="/assets/images/s002.png" width="32" height="32"
                             title="Share this playlist!"/>
                    </a>
                </div>
                <div id="stbtn3" title="Share this playlist!">
                    <a href="#" id="btnGenEmail">
                        <img rel="tooltip" src="/assets/images/email_32.png" width="32" height="34"
                             title="Share this playlist!"/>
                    </a>
                </div>
            </div>
            <span id="playlistlinkText">Playlist link</span>
            <input rel="tooltip" type="text" class="form-control"
                   id="playlistcode" readonly="readonly"
                   value="<?php echo(isset($_GET['id']) ? 'https://instadj.com/' . preg_replace("/[^a-zA-Z0-9\s]/", "", $_GET['id']) : '') ?>"
                   title="Playlist link. Share it!"/>

        </div>
    </div>

    <div id="playlist" class="well">
        <ul id="playlistcontent"></ul>
    </div>

    <div id="playlistcontrols">
        <div class="btn-group" role="group">
            <button class="btn btn-default" id="previous"><i class="glyphicon glyphicon-fast-backward"></i> Previous
            </button>
            <button class="btn btn-default" id="pauseplay"><i class="glyphicon glyphicon-pause"></i> Pause/Play</button>
            <button class="btn btn-default" id="next"><i class="glyphicon glyphicon-fast-forward"></i> Next</button>
            <button class="btn btn-default" id="shuffle"><i class="glyphicon glyphicon-random"></i> Shuffle</button>
        </div>
    </div>

    <div id="controls">
        <a href="./" class="logo-sm"><img src="assets/images/instadj-black.png" id="smallogo" height="27"
                                          title="Create &amp; Share YouTube Playlists with InstaDJ"/></a>

        <div id="txtSearchwrapper">
            <input type="search" id="txtSearch" class="search-query form-control"
                   autofocus="autofocus" autocomplete="off"
                   title="Search and add videos to playlist" placeholder="YouTube Search…"/>

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

<script src="assets/js/json2.js"></script>

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

<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/js/bootstrap3-typeahead-4.0.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>


<script>
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    if (typeof window.localStorage.currentPlaylist === 'undefined'
        && getCookie('currentPlaylist') !== null) {
        window.localStorage.currentPlaylist = getCookie('currentPlaylist');
    }

    if (typeof window.localStorage.currentPlaylist === 'string'
        && getCookie('currentPlaylist') === null) {
        setCookie('currentPlaylist', window.localStorage.currentPlaylist);
    }
</script>

</body>
</html>
