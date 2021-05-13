var first = true;
var currentID;
var currentState;
var start = 51;
var playState;
var ytapi = 'ytv3.php';
var store = window.localStorage;

(function($) {
    $.fn.shuffle = function() {
        var allElems = this.get(),
            getRandom = function(max) {
                return Math.floor(Math.random() * max);
            },
            shuffled = $.map(allElems, function() {
                var random = getRandom(allElems.length),
                    randEl = $(allElems[random]).clone(true)[0];
                allElems.splice(random, 1);
                return randEl;
            });

        this.each(function(i) {
            $(this).replaceWith($(shuffled[i]));
        });

        return $(shuffled);
    };
})(jQuery);

// On Document Load
$(function() {
    $(document).ready(function () {
        if (window.playlist !== '') {
            $("#intro").toggle();
            getplaylist(window.playlist);
        } else if (store.getItem('currentPlaylist')) {
            $("#intro").toggle();
            getplaylist(store.getItem('currentPlaylist'));
        } else {
	        getplaylist(); // Create new playlist
        }
    });


    $('#controls').fadeIn();

    loadRedditPlaylist('futurebeats');

    $("#smallogo").tooltip({
        placement: 'bottom'
    });

    $("#sbtn001 img, #sbtn002 img, #btnGenEmail img, #newplaylist").tooltip({
        placement: 'top'
    });

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
        'chillmusic',
        'classicalmusic',
        'trance',
        'dnb',
        'mashups'
    ];

    $(subreddits).each(function(index) {
        $("#dropdownMenu").append('<li><a href="#" class="btnReddit" data-playlist="' + subreddits[index] + '">/r/' + subreddits[index] + '</a></li>');

    });

    $("#playlistcontent").sortable({
        placeholder: "ui-state-highlight",
        stop: shareit,
    });
    $("#playlistcontent").disableSelection();

    $("#dropdownMenu").on("click", ".btnReddit", function(e) {
        loadRedditPlaylist($(this).attr('data-playlist'));
    });


    $('#recent').on("click", "a", function(event) {
        $('#txtSearch').attr("value", $(this).attr('data-href'));
        $('#btnSearch').click();
    });

    $("#grid").on("click", ".loadmore", function(event) {
        $('.loadmoreimg').attr('src', '/assets/images/load.gif');
        scrolltothis = $('.loadmore').prev();
        loadwhat = $('.loadmore').find('a').attr('href');
        geturl = loadwhat + '&from=' + start;

        $.ajax({
            url: geturl,
            success: function(data) {
                $('.loadmore').detach();
                $('#grid').append(data);
                //$('#grid').scrollTo(scrolltothis, {duration: 700});
            }
        });

        start = start + 50;
        return false;
    });

    $(document).on("click", ".video", function(event) {
        var id = $(this).children("a").attr("href").match(/v=(.{11})/)[1];
        var title = $(this).children("a").text();
        var duration = $(this).children(".duration").text();
        $(this).children(".playoverlay").fadeOut('fast').fadeIn('fast');
        addtoplaylist(id, title, duration, true, true, true);
        return false;
    });

    $(document).on("click", ".playlistitem", function(event) {
        playid($(this).attr("data-id"));
        $("#playlistcontent li").removeClass('active');
        $(this).parent().addClass('active');
    });

    $(document).on("click", ".playlistremove", function(event) {
        $(this).parent().remove();
        shareit();
    });

    $('#grid').on("mouseenter", ".video",
        function(event) {
            $(this).children('.related').fadeIn(100);
            $(this).children('.playoverlay').fadeIn(100);
        });
    $('#grid').on("mouseleave", ".video",
        function(event) {
            $(this).children('.related').hide();
            $(this).children('.playoverlay').hide();
        });

    $('#playlistcontent').on("mouseenter", "li", function(e) {
        $(this).children('.playlistremove').css('display', 'block');
        $(this).children('.related').css('display', 'block');
    });
    $('#playlistcontent').on("mouseleave", "li", function(e) {
        $(this).children('.playlistremove').css('display', 'none');
        $(this).children('.related').css('display', 'none');
    });

    $('#grid,#playlistcontent').on("click", ".related",
        function() {
            var that = this;
            var htmlBefore = $(this).children("[data-href]").html();
            $(this).children("[data-href]").html('<img src="/assets/images/miniloader.gif" />');
            geturl = $(this).children("[data-href]").attr("data-href");

            $.ajax({
                url: geturl,
                success: function(data) {
                    $('#grid').html(data);
                    $('#grid').scrollTo($('#grid').children().first(), {
                        duration: 500
                    });
                    $(that).children("[data-href]").html(htmlBefore);
                }
            });


            return false;
        });

    $("#playlistcode").click(function() {
        $(this).select();
    });

    $("#btnSearch,#btnPlaylistId").click(function() {
        if ($('#txtSearch').val() == '') {
            $('#txtSearch').attr('placeholder', 'Enter song or artist…');
            $('#txtSearch').focus();
        } else {
            $("#txtSearch").addClass("loading");

            geturl = ytapi + '?action=search&q=' + encodeURIComponent( $('#txtSearch').val() );

            $.ajax({
                url: geturl,
                success: function(data) {
                    $('#grid').empty();
                    $('#grid').html(data);
                    $("#txtSearch").removeClass("loading");
                    // $('#grid').scrollTo($('#grid').children().first(), {duration: 500});
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

    $("#txtSearch").typeahead({
        source: function(request, response) {
            $.getJSON("https://suggestqueries.google.com/complete/search?callback=?", {
                "hl": "en", // Language
                "ds": "yt", // Restrict lookup to youtube
                "jsonp": "suggestCallBack", // jsonp callback function name
                "q": request, // query term
                "client": "youtube" // force youtube style response, i.e. jsonp
            });
            suggestCallBack = function(data) {
                var suggestions = [];
                suggestions.push($("#txtSearch").val());
                $.each(data[1], function(key, val) {
                    if (suggestions.indexOf(val[0]) === -1)
                        suggestions.push(val[0]);
                });
                response(suggestions);
            };
        },
        minLength: 1,
        items: 15,
        autoSelect: false,
        afterSelect: function (something) {
            $("#txtSearch").val(something);
            $("#btnSearch").click();
            },
    });

    $("#btnRelated").click(function() {
        $.ajax({
            url: ytapi + '?action=related&id=' + currentID,
            success: function(data) {
                $('#grid').empty();
                $('#grid').html(data);
                $("#txtSearch").removeClass("loading");
            }
        });
    });

    $('#pauseplay').click(function() {
        if (currentState == 1) {
            ytPlayer.pauseVideo()
        } else {
            ytPlayer.playVideo();
        }
    });

    $("#previous").click(function() {
        if ($('#playlistcontent li.active').length == 0) {
            $('#playlistcontent li').first().find("a").click();

        } else {
            $('#playlist').scrollTo($('#playlistcontent li.active').prev().find("a"), {
                duration: 100
            });
            $('#playlistcontent li.active').prev().find("a").click();
        }
    });

    $("#next").click(function() {
        if ($('#playlistcontent li.active').length == 0) {
            $('#playlistcontent li').first().find("a").click();

        } else {
            $('#playlist').scrollTo($('#playlistcontent li.active').next().find("a"), {
                duration: 100
            });
            $('#playlistcontent li.active').next().find("a").click();
        }
    });

    $('#shuffle').click(function() {
        $('#playlist ul li').shuffle();
    });

    $('#clear').click(function() {
        $('#playlist ul li').remove();
    });

    $('#txtSearch').focus();

    $('#copylink').click(function (e) {
        copyLink('playlistcode');
        e.preventDefault();
    })

    $("#newplaylist").click(function (e) {
        getplaylist(null, function () {
            location.reload();
        });
        e.preventDefault();
    });
});

function copyLink(inputId) {
    var copyText = document.getElementById(inputId);

    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */

    document.execCommand("copy");
}

function onPlayerReady(event) {
    event.target.playVideo();

    ytPlayer.addEventListener("onStateChange", "ytState");
}

function onPlayerStateChange(event) {
    if (event.data == 0) {
        if (($('#playlistcontent li').length == 0) || ($('#playlistcontent li').length == 1)) {
            // Don't do anything
        } else {
            if ($('#playlistcontent li.active').length == 0) {
                $('#playlistcontent li').first().find("a").click();

            } else {
                $('#playlist').scrollTo($('#playlistcontent li.active').next().find("a"), {
                    duration: 500
                });
                $('#playlistcontent li.active').next().find("a").click();
            }

        }
    }
    currentState = event.data;

    if (event.data == 1) {
        ytPlayer.setPlaybackQuality('hd720');
    }
}


$(document).on("click", "#btnFavorites", function(event) {
    if ($('#txtSearch').val() == '') {
        $('#txtSearch').attr('placeholder', 'Enter username…');
        $('#txtSearch').focus();
    } else {
        $("#txtSearch").addClass("loading");

        $.ajax({
            url: ytapi + '?action=userfavorites&user=' + $('#txtSearch').val(),
            success: function(data) {
                $('#grid').empty();
                $('#grid').html(data);
                $("#txtSearch").removeClass("loading");
                $('#grid').scrollTo($('#grid').children().first(), {
                    duration: 500
                });
            }
        });

    }

});

$(document).on("click", "#btnUploads", function(event) {

    if ($('#txtSearch').val() == '') {
        $('#txtSearch').attr('placeholder', 'Enter username…');
        $('#txtSearch').focus();
    } else {
        $("#txtSearch").addClass("loading");

        if (first == true) {
            $('#player').show();
            $('#playlist').show();
            $('#intro').fadeOut('fast');
        }

        $.ajax({
            url: ytapi + '?action=useruploads&user=' + $('#txtSearch').val(),
            success: function(data) {
                $('#grid').empty();
                $('#grid').html(data);
                $("#txtSearch").removeClass("loading");
                $('#grid').scrollTo($('#grid').children().first(), {
                    duration: 500
                });
            }
        });
    }
});


$('li').click(function(event) {
    event.stopPropagation();
});

function playid(id) {
    currentID = id;

    if (first === true) {
        $("body").removeClass('first-time');

        if ((typeof YT !== 'object') || (typeof YT.Player !== 'function')) {
            setTimeout(function () { playid(id) }, 500);
            return;
        }

        first = false;

        ytPlayer = new YT.Player('player', {
            height: '390px',
            width: '250px',
            playerVars: {
                'autoplay': 1,
                'autohide': 1,
                'theme': 'light',
                'color': 'red',
                'iv_load_policy': '3',
                'showinfo': '0'
            },
            videoId: id,
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });

        $('#playlistcontrols').fadeIn('fast');
    } else {
        if (typeof ytPlayer !== 'function') {
            setTimeout(function () { playid(id) }, 500);
            return;
        }

        ytPlayer.loadVideoById(id, 0, 'large');
    }

    $('li.active').removeClass("active");
    $('#' + id).addClass('active');

    setCookie('item', id);
}

/*
 -1 (unstarted)
 0 (ended)
 1 (playing)
 2 (paused)
 3 (buffering)
 5 (video cued).
 */

function addtoplaylist(id, title, duration, share, animate, playfirst) {
    try {
        playState = ytPlayer.getPlayerState();
    } catch (e) {
        playState = 9;
    }

    var item = $(
        '<li id="' + id + '" title="' + title + ' (' + duration + ')">' +
            '<button class="btn btn-xs btn-default related" style="display:none;">' +
                '<span data-href="ytv3.php?action=related&id=' + id + '">' +
                '<i class="glyphicon glyphicon-search"></i> Related</span>' +
            '</button>' +
            '<button class="btn btn-xs btn-danger playlistremove">' +
                '<i class="glyphicon glyphicon-remove"></i>' +
            '</button>' +
            '<a class="playlistitem" href="#" data-id="' + id + '">' +
                '<img width="40" height="30" class="playlistimg" src="https://i.ytimg.com/vi/' + id + '/1.jpg" />' +
                '' + title + ' <span class="duration">(' + duration + ')</span>' +
            '</a>' +
        '</li>'
    );

    if (animate) {
        $('#playlistcontent').append(item.hide().fadeIn());
    } else {
        $('#playlistcontent').append(item);
    }

    if (share) {
        shareit();
    }

    var shouldPlay = playfirst
        && playState !== 1
        && (playState === -1 || playState === 0 || playState === 2 || playState === 9);

    if (shouldPlay) {
        playid(id, title);
    }
}

function shareit() {
    playlistarray = {};

    $(".playlistitem").each(function(index) {
        playlistarray[$(this).attr('data-id')] = $(this).text();
    });

    var playlistJSON = JSON.stringify(playlistarray);

    var query = ''
    var playlistId = store.getItem('currentPlaylist');
    var playlistPassword = store.getItem('password_' + playlistId);
    if (playlistId && playlistPassword) {
        query = '&playlist_id=' + encodeURIComponent(playlistId) + '&playlist_password='
            + encodeURIComponent(playlistPassword);
    }

    $.ajax({
        type: 'POST',
        url: '/store.php?action=store' + query,
        data: 'playlistdata=' + escape(playlistJSON),
        success: function(data) {
            updateCurrentPlaylist(data.playlist_id, data.password);
        }
    });
}

function updateCurrentPlaylist(playlistId, playlistPassword)
{
    updateShareLinks(playlistId);

    // Update current URL, if user came from a shared URL
    history.pushState(null, document.title, '/' + playlistId);

    store.setItem('currentPlaylist', playlistId);
    if (playlistPassword) {
        store.setItem('password_' + playlistId, playlistPassword);
    }
}

function updateShareLinks(playlistId) {
    var shareUrl = "https://instadj.com/" + playlistId;

    $("#playlistcode").attr("value", shareUrl);

    $("#sbtn001 a").attr("href",
        "https://facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl));

    $("#sbtn002 a").attr("href",
        "https://www.twitter.com/intent/tweet?text=" +
        "Check%20out%20my%20InstaDJ%20playlist." +
        "&url=" + encodeURIComponent(shareUrl));

    $("#btnGenEmail").attr("href",
        "mailto:?subject=Check out my playlist" +
        "&body=Hi, I made a playlist and thou" +
        "ght you might like it: " + encodeURIComponent(shareUrl));
}

function loadRedditPlaylist(subreddit) {
    $("#txtSearch").addClass("loading");
    $.ajax({
        url: 'reddit/redditrss.php?reddit=' + subreddit,
        success: function(data) {
            $('#grid').empty();
            var videoIds = [];
            $(data).find('item').each(function() {
                var id = $(this).children('a').text();
                videoIds.push(id)
            });

            geturl = ytapi + '?action=videoids&ids=' + videoIds.join(',');

            $.ajax({
                url: geturl,
                success: function(data) {
                    $('#grid').empty();
                    $('#grid').html(data);
                    $("#txtSearch").removeClass("loading");
                    // $('#grid').scrollTo($('#grid').children().first(), {duration: 500});
                }
            });
        }
    });
}

function getplaylist(id, callback) {
    $.ajax({
        dataType: 'json',
        url: 'store.php?action=get&id=' + (id ? id : ''),
        success: function(data) {
            if (data.error) {
                alert('Error when loading playlist "' + id + '": ' + data.code + '.'+
                      '\nPlease contact simon@fredsted.me or @fredsted on Twitter for assistance.');
            } else {
                $("#playlistcode").attr("value", 'https://instadj.com/' + id);
                $("#intro").toggle();

                for (var videoId in data.videos) {
                    if (data.videos.hasOwnProperty(videoId)) {
                        addtoplaylist(
                            videoId,
                            data.videos[videoId]['title'],
                            data.videos[videoId]['duration'],
                            false,
                            false,
                            false
                        );
                    }
                }

                var cookieId = getCookie('item');
                var playlistIds = Object.keys(data.videos);

                if (cookieId && playlistIds.indexOf(cookieId) !== -1) {
                    playid(cookieId);
                } else if (playlistIds.length > 0) {
                    playid(playlistIds[0]);
                }

                updateCurrentPlaylist(data.playlist_id, data.password);

                if (callback) {
                    callback(data.playlist_id);
                }
            }
        }
    });
}

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