jQuery(document).ready(function($) {

    var query_string = new URLSearchParams(window.location.search);

    if (query_string.has("session_id")) {
        window.conn = new WebSocket('ws://localhost:8080');

        window.conn.onopen = function(e) {
            console.log("Connection established!");

            let session_id = query_string.get("session_id");

            subscribe(session_id);
        };

        window.conn.onmessage = function(e) {
            console.log(e.data);

            var data = JSON.parse(e.data);

            alert(data.message);
        };

        function subscribe(channel) {
            window.conn.send(JSON.stringify({command: "subscribe", channel: channel}));
        }

        function sendMessage(msg) {
            window.conn.send(JSON.stringify({command: "message", message: msg}));
        }

        $('header').click(function(){
            sendMessage('test session: ' + query_string.get("session_id"));
        });
    }

    initDropdown($('.ui.dropdown'));
    if ($('#bigSlider').length) {
        $('.swiper-large-home').each(function () {
            initMovieSlider($(this)[0]);
        });
    }
    if($('.slider-strip').length) {
        $('.swiper-strip').each(function () {
            initSliderStrip($(this)[0]);
        });
    }

    function initMovieSlider(el) {
        new Swiper(el, {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
            },
            speed: 1000,
            grabCursor: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }

    function initSliderStrip(el) {
        new Swiper(el, {
            slidesPerView: 'auto',
            spaceBetween: 15,
            grabCursor: true,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: '.swiper-strip .swiper-button-next',
                prevEl: '.swiper-strip .swiper-button-prev',
            },
        });
    }

    function initDropdown(el) {
        el.dropdown({
            transition :'scale',
            onChange: function(selectedTerm) {
                if ($(this).hasClass('strip-filter')) {
                    let postType = $(this).data('post-type');
                    let taxonomy = $(this).data('taxonomy');
                    let action = $(this).data('action');
                    let _this = $(this);
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType : 'html',
                        data: {
                            'action': action,
                            'post_type': postType,
                            'taxonomy': taxonomy,
                            'term': selectedTerm,
                            'section_title': _this.closest('.strip-top').children('.strip-heading').text()
                        },
                        success: function (response) {
                            let newSectionEl = $(response);
                            _this.closest('.slider-strip').replaceWith(newSectionEl);
                            initDropdown(newSectionEl.find('.dropdown.ui')); //init ajax loaded filter dropdown
                            initSliderStrip(newSectionEl.find('.swiper-strip')[0]); //init ajax loaded swiper slider
                        }
                    });
                }
                if ($(this).hasClass('my-movies-sort')) {
                    let query_params = $(".my-movies-sort [value!=''][type='hidden']").serialize();
                    updateQueryParams(query_params);
                }
            },
        });
    }

    function updateQueryParams(query_params, reload = true) {
        let new_url;
        if (query_params) {
            new_url = [location.protocol, '//', location.host, location.pathname, '?', decodeURI(decodeURI(query_params))].join('');
        } else {
            new_url = [location.protocol, '//', location.host, location.pathname].join('');
        }

        if (reload) {
            location.href = new_url;
        } else {
            window.history.pushState('', 'Search', new_url);
        }
    }
    $('.filter-btn .icon, .filter-btn .cancel-filters').on('click', function () {
        $('.filter-btn > .menu').transition('scale');
        $('.filter-btn').toggleClass('active');
    });
    $('.filter-btn .reset-filters').on('click', function () {
        $('.filter-btn .ui.dropdown').dropdown('clear');
    });
    $('.filter-btn .apply-filters, .filters-section form.free-search .icon').on('click', function () {
        applyFilters();
    });

    function applyFilters(with_reload = true) {
        $('.filter-btn .filters-applied-count').html($(".movies-filter > [value!=''][type='hidden']").length);

        let query_params = $(".filters-section [value!=''][type='hidden']").serialize();

        if ($('.filters-section .free-search').length && $('.free-search input').val()) {
            query_params += (query_params) ? '&s=' : 's=';
            query_params += $('.filters-section .free-search input').val();
        }

        updateQueryParams(query_params, with_reload);
    }

    $('#movieInfo .movie-info .readmore').on('click', function () {
        $(this).hide();
        $('.readmore-hidden').fadeIn(200);
    });

    $('.tags .tags-more.clickable').on('click', function () {
        $('.tags .tags-list .tag-hidden').fadeIn(200);
        $(this).hide();
    });

    $('.create-session-btn').on('click', function () {
        $('.modal.ui.start-session#'+$(this).data('modal-id')).modal('show');
        let id = $(this).attr('data-modal-id');
        $('#'+id).find('.start-now').click();
        
        $('#'+$(this).data('modal-id')+' .datetime-selector').calendar({
                type: 'datetime',
                ampm: false,
                text: {
                    days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    today: 'Today',
                    now: 'Now',
                },
                firstDayOfWeek: 1,
                formatter: {
                    date: function (date, settings) {
                        if (!date) return '';
                        var day = date.getDate() + '';
                        if (day.length < 2) {
                            day = '0' + day;
                        }
                        var month = (date.getMonth() + 1) + '';
                        if (month.length < 2) {
                            month = '0' + month;
                        }
                        var year = date.getFullYear();
                        return day + '/' + month + '/' + year;
                    }
                }
            })
        ;
    });

    $('.delete-session-btn').on('click', function () {
        var result = confirm("Are you sure you want to remove this lesson?");
        if (result) {

            let id = $(this).attr('data-lesson-id');

            var data = {
                action: 'delete_session',
                id: id
            };
    
            jQuery.post( ajaxurl, data, function( response ){
                showToast('Lesson deleted!', 'Lesson successfully removed');
                $('#lesson_id_'+id).hide();
            } );

        }
    });

    $('.create-session-btn-schedule').on('click', function () {
        $('.modal.ui.start-session#'+$(this).data('modal-id')).modal('show');
        
        let id = $(this).attr('data-modal-id');
        $('#'+id).find('.nextScreen').click();
        $('#'+id).find('.play-now').hide();

        $('#'+$(this).data('modal-id')+' .datetime-selector').calendar({
            type: 'datetime',
            ampm: false,
            text: {
                days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                today: 'Today',
                now: 'Now',
            },
            firstDayOfWeek: 1,
            formatter: {
                date: function (date, settings) {
                    if (!date) return '';
                    var day = date.getDate() + '';
                    if (day.length < 2) {
                        day = '0' + day;
                    }
                    var month = (date.getMonth() + 1) + '';
                    if (month.length < 2) {
                        month = '0' + month;
                    }
                    var year = date.getFullYear();
                    return day + '/' + month + '/' + year;
                }
            }
        });
    });

    
    $('.start-session .cancel').on('click', function () {
        $('.modal.ui.start-session').modal('hide');
    });
    $('.start-session .schedule-now').on('click', function () {
        let _this = $(this);
        let modal = getCreateSessionModalId(_this);

        const months= ["January","February","March","April","May","June","July",
            "August","September","October","November","December"];
        const days= ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

        const form = _this.closest('.sessionForm');
        const basicDate = form.find("[name=schedule]").val();
        const [date, time] =  basicDate.split(' ');
        const [dd, mm, yy] =  date.split('/');
        const duration = parseInt(form.find("[name=access_duration]").val());

        let  dateFrom = new Date(mm +" "+ dd + " " + yy + " " + time);
        let  formatDateFrom = days[dateFrom.getDay()] +', '+months[dateFrom.getMonth()] +' '+ dateFrom.getDate();
        let  dateUntil = addHoursToDate(dateFrom, duration);
        let  formatDateUntil = days[dateUntil.getDay()] +', '+months[dateUntil.getMonth()] +' '+ dateUntil.getDate();

        _this.removeClass('schedule-now');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: $('#'+modal+' #sessionForm').serialize() + "&action=create_session",
            success: function (response) {
                if (!response.error) {
                    showToast('Session created!', 'Session link successfully generated.');
                    $('#'+modal+'.start-session.modal .session-url .url').text(response.success);
                    str = response.success
                    newstr = str.split('/')
                    sessionCode = newstr[4]
                    //$('#'+modal+'.start-session.modal .session-url .copy').removeClass('hidden');
                    $('#'+modal+'.start-session.modal .session-code .code').text(sessionCode);
                    $('#'+modal+'.start-session.modal .sessionForm__code').removeClass('hidden');
                    $('#'+modal+'.start-session.modal .sessionForm__description').addClass('hidden');
                    $('#'+modal+'.start-session.modal .sessionShare .shareList').removeClass('hidden');             
                    $('#'+modal+'.start-session.modal .start-now').addClass('hidden');                
                    $('#'+modal+'.start-session.modal .sessionTime').removeClass('hidden').html(
                        'Content Available from ' + formatDateFrom  + ' until ' + formatDateUntil
                    );                
                    _this.attr('href', response.success).text('Go to session');
                    copySessionLink(modal);
                    //$('.modal.ui.start-session').modal('hide');
                } else {
                    _this.addClass('schedule-now');
                    showToast('Error', response.error);
                }

            }
        });
    });
    function addHoursToDate(date, hours) {
        console.log(typeof (date.getHours()));
        return new Date(new Date(date).setHours(date.getHours() + hours));
      }
    $('.start-now').on('click', function () {
        let _this = $(this);
        _this.closest('form').find('.nextScreen').css('display', 'none');
        let modal = getCreateSessionModalId(_this);
        $("#scheduleDate").hide();
        $("#schedule").val(formatDate(new Date()));
        _this.removeClass('start-now');
        _this.addClass('play-now');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: $(_this.closest('form')).serialize() + "&action=create_session",
            success: function (response) {
                if (!response.error) {
                    console.log(response.success);
                    let str = response.success
                    let newstr = str.split('/')
                    let sessionCode = newstr[4]

                    _this.closest('form').find('.url').text(str);
                    _this.closest('form').find('.lessonCode').text(sessionCode);
                    _this.closest('form').find('.play-now').attr('href', str);
                } else {
                    _this.addClass('start-now');
                    showToast('Error', response.error);
                }

            }
        });
    });

    const formatDate = (input) => {
        const day = input.getDate();
        const month = input.getMonth() + 1;
        const year = input.getFullYear();
        const minutes = input.getMinutes();
        const hours = input.getHours();
        return `${day}/${month.toString().length === 1 ? "0" + month : month}/${year} ${hours}:${minutes}`
    };

    $("input[name=session_type]").on('change', function () {
        let modal = getCreateSessionModalId($(this));
        console.log($(this));
        $('#'+modal+'.start-session .condition').toggle();
    });

    $('.start-session .copy').on('click', function () {
        let modal = getCreateSessionModalId($(this));
        copySessionLink(modal)
    });

    function copySessionLink(modal) {
        let temp = $("<input>");
        $("body").append(temp);
        temp.val($('#'+modal+'.start-session .session-url .url').text()).select();
        document.execCommand("copy");
        temp.remove();

        showToast('Copied!', 'The session link was successfully copied to your clipboard.');
    }

    function getCreateSessionModalId(el) {
        return el.closest('.modal.start-session').attr('id');
    }

    function showToast(title, message) {
        $('body').toast({
            title: title,
            message: message,
            displayTime: 3000,
            position: 'bottom right',
            class : 'dark',
            className: {
                toast: 'ui toast'
            }
        });
    }

    $('.actions-more, .account-btn').dropdown({
        transition :'scale',
        action: 'hide',
    });
    $('#bigSlider .actions-more, .movie-block .actions-more').dropdown({
        direction: 'upward'
    });

    $('.segment.questions .movie-segment .start-movie-preview').on('click', function () {
        $('.modal.movie-player .movie-questions-content').html($(this).closest('.movie-segment').find('.text-content').clone());
        $('.modal.movie-player .start-movie-preview').removeClass('start-movie-preview').unbind('click'); //make timeline in modal non-clickable
        $('.modal.movie-player .movie-questions').show();
    });
    $('.modal.movie-player .hide-answers span').on('click', function () {
        $(this).closest('.movie-questions').find('.question-content .answer').slideToggle(300);
    });

    $('body').on('click', '.start-movie-preview', function () {

        if ($(this).data('mode') !== 'advanced') {
            $('#kalturaPlayer').empty(); //clear if we are not on movie page with time tracker
        }

        $('.modal.movie-player').modal({
            className : { scrolling : '' },
            onHide() {
                window.kdp.sendNotification('doPause');
                if ($('.modal.movie-player .movie-questions').length) {
                    $('.modal.movie-player .movie-questions').hide();
                }
            }
        });

        $('.modal.movie-player').modal('show');

        if (!$('#kalturaPlayer').data('loaded') || $(this).data('mode') !== 'advanced') {
            requestPlayerWithMovieModal($(this));
        }

    });

    function requestPlayerWithMovieModal(data_obj) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: {
                'action': 'request_kaltura_movie',
                'movie_id': data_obj.data('movie-id'), //use only one of params
                'kaltura_id': data_obj.data('kaltura-id'), //will be overwritten by movie_id if both of params passed
            },
            success: function (data) {

                let mediaPlayTo;
                let mediaPlayFrom;
                if (data_obj.data('mode') === 'advanced') {
                    mediaPlayFrom = data.start_from ? data.start_from : 0;
                    mediaPlayTo = 0;
                } else {
                    mediaPlayFrom = data_obj.data('play-from') ? data_obj.data('play-from') : 0;
                    mediaPlayTo = data_obj.data('play-to') ? data_obj.data('play-to') : 0;
                }

                kWidget.embed({
                    targetId: "kalturaPlayer",
                    wid: "_" + data.wid,
                    uiconf_id: data.uiconf_id,
                    entry_id: data.kaltura_id,
                    flashvars: {
                        "ks": data.ks,
                        "applicationName": "mediaspace",
                        "playbackContext": "",
                        "disableAlerts": "false",
                        "externalInterfaceDisabled": "false",
                        //"autoPlay": "true",
                        //"autoMute": false,
                        "streamerType": "auto",
                        "localizationCode": "en_GB",
                        "leadWithHTML5": "true",
                        "sideBarContainer": {"plugin": "true", "position": "left", "clickToClose": "true"},
                        "chapters": {"plugin": "true", "layout": "vertical", "thumbnailRotator": "false"},
                        "streamSelector": {"plugin": "true"},
                        // continue watching:
                        "mediaProxy.mediaPlayFrom": mediaPlayFrom,
                        "mediaProxy.mediaPlayTo": mediaPlayTo,
                        "Kaltura.UseAppleAdaptive": true
                    },
                    'readyCallback': function( playerId ){
                        $('#kalturaPlayer').data('loaded', true);
                        window.kdp = document.getElementById( playerId );

                        setTimeout(function () {
                            window.kdp.sendNotification('doPlay');
                        }, 300);

                        if (data_obj.data('mode') === 'advanced') {
                            window.kdp.kBind("doPause", function(){
                                throttle(updateContinueWatchingList, 1000);
                            });
                            kdp.kBind ( "playerUpdatePlayhead.fullMovie" , function ( data , id ) {
                                if(parseInt(data) % 60 === 0) { //call only if current player time multiple of 60
                                    throttle(updateContinueWatchingList, 1000);
                                }
                            });
                            window.kdp.kBind("playerPlayEnd", function(){

                            });
                            $(window).on('beforeunload', function(){
                                throttle(updateContinueWatchingList, 1000);
                                return undefined;
                            });
                        }

                        // $('.modal.movie-player .close').on('click', function () {
                        //     window.kdp.sendNotification('doPause');
                        // });

                    }
                });
            }
        });
    }

    /*
    $('.load-movie-inline').on('click', function () {
        let _this = $(this);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: {
                'action': 'request_kaltura_movie',
                'movie_id': _this.data('movie-id'),
            },
            success: function (data) {
                kWidget.embed({
                    targetId: _this.closest('.media-content').attr('id'),
                    wid: "_" + data.wid,
                    uiconf_id: data.uiconf_id,
                    entry_id: data.kaltura_id,
                    flashvars: {
                        "ks": data.ks,
                        "applicationName": "mediaspace",
                        "playbackContext": "",
                        "disableAlerts": "false",
                        "externalInterfaceDisabled": "false",
                        "autoPlay": "true",
                        "streamerType": "auto",
                        "localizationCode": "en_GB",
                        "leadWithHTML5": "true",
                        "sideBarContainer": {"plugin": "true", "position": "left", "clickToClose": "true"},
                        "chapters": {"plugin": "true", "layout": "vertical", "thumbnailRotator": "false"},
                        "streamSelector": {"plugin": "true"},
                    },
                    'readyCallback': function( playerId ){
                        $('#kalturaPlayer').data('loaded', true);
                        window.kdp = document.getElementById( playerId );
                    }
                });
            }
        });
    });
    */

    function updateContinueWatchingList() {
        let curr_time = parseInt(window.kdp.evaluate("{video.player.currentTime}"));
        let duration = parseInt(window.kdp.evaluate("{duration}"));
        let wp_action = (curr_time < duration) ? 'update_continue_watching' : 'remove_from_continue_watching';

        if (curr_time > 59) { //Removing does not depend on the current time && Track time only after 1 minute
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType : 'json',
                data: {
                    'action': wp_action,
                    'post_id': $('.single-movie-page').data('movie-id'),
                    'time': curr_time,
                },
                success: function (response) {
                    //updated
                }
            });
        }
    }

    $('.my-list-button').on('click', function (e) {
        e.preventDefault();
        let _this = $(this);
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: {
                'action': 'update_my_list',
                'post_id': _this.data('movie-id'),
                'button_type': _this.data('button-type'),
            },
            success: function (response) {
                _this.html(response);
            }
        });
    });

    var waiting_cw_update = false;
    function throttle (func, ms) {
        if (!waiting_cw_update) {
            func.apply(this, arguments);
            waiting_cw_update = true;
            setTimeout(function () {
                waiting_cw_update = false;
            }, ms);
        }
    }

    $('.tabs-group .ui.menu .item').tab();

    $('.load-more-sessions').on('click', function () {

        $.ajax({
            url: ajaxurl,
            type: 'GET',
            dataType : 'json', //html
            data: {
                'action': 'load_more_sessions',
                'offset': $(this).attr('data-offset')
            },
            success: function (response) {
                $('.lesson-rows').append(response.sessions);
                if (response.offset) {
                    $('.load-more-sessions').attr('data-offset', response.offset)
                } else {
                    $('.load-more-sessions').remove();
                }

            }
        });

    });

    $('.irp-image.change-photo .avatar').on('click', function () {
        $( '.irp-image #avatar-manager-upload' ).trigger('click');
    });

    ( function() {
        var button, input, avatarManager = $( '.irp-image.change-photo form' );
        if ( ! avatarManager.length ) {
            return;
        }
        input  = avatarManager.find( 'input[type="file"]' );
        function toggleUploadButton() {
            var fileSelected = '' !== input.map( function() {
                return $( this ).val();
            } ).get().join( '' );
            if (fileSelected) {
                $('.irp-image form input[type="submit"]').trigger('click');
            }
        }
        toggleUploadButton();
        input.on( 'change', toggleUploadButton );
    } )();
    
    $('.change-profile-info').on('click', function() {
        $('.modal.change-info').modal('show');
    });


    $( '.change-info' ).on(
        'click',
        '.ir-profile-add-input',
        function(){
            var list  = $( this ).siblings( '.ir-profile-list' );
            var lastInput = list.find( '.ir-profile-input' ).last();
            var newInput = lastInput.clone();
            newInput.find('.ir-profile-input-list').val('');
            newInput.insertAfter(lastInput);
        }
    );

    $( '.change-info' ).on(
        'click',
        '.ir-profile-remove-input',
        function() {
            var input    = $( this ).parent();
            var container = $( this ).parents( '.ir-profile-list-container' );
            if ( container.find( '.ir-profile-input' ).length > 1 ) {
                input.remove();
            }
        }
    );

    $('#editProfileForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType : 'json',
            data: $('#editProfileForm').serialize(),
            success: function (response) {
               if (response.success) {
                   location.reload();
               }
            }
        });

    });

    function fetchSearchResults() {
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            //dataType : 'json', //html
            data: location.search.substr(1) + '&action=fetch_search_results',
            beforeSend: function() {
                $('.loading').fadeIn(300);
                //$('.loading').dimmer('show');
            },
            success: function (response) {
                $('.loading').fadeOut(300);
                $('#searchResults').html( response );
                if($('.slider-strip').length) {
                    $('.swiper-strip').each(function () {
                        initSliderStrip($(this)[0]);
                    });
                }
            }
        });
    }

    $(window).on('load', function () {
        if ($('#searchResults').length) {
            fetchSearchResults();
        }
    });
    $('.search .free-search').on('submit', function (e) {
        e.preventDefault();
        applyFilters(false);
        fetchSearchResults();
    });
    $('.search-btn').on('click', function () {
        $(this).closest('form').submit();
    });
    $('.search .filter-btn .apply-filters-async').on('click', function () {
        applyFilters(false);
        fetchSearchResults();
        $('.filter-btn > .menu').transition('scale');
        $('.filter-btn').toggleClass('active');
    });

    $('#searchResults').on('click', '.show-meta', function () {
        let id = $(this).closest('.movie-block').data('movie-id');
        $('.post-meta-highlighted[data-movie-id='+id+']').modal('show');

        $('.post-meta-highlighted .close').off('click').on('click', function () {
            $(this).closest('.modal').modal('hide');
        });

        $('.tags .tags-more.clickable').off('click').on('click', function () {
            $(this).siblings('.tag-hidden').fadeIn(200);
            $(this).hide();
        });
        $('.subtitles-more.clickable').off('click').on('click', function () {
            $(this).siblings('.subtitle-hidden').fadeIn(200);
            $(this).hide();
        });
    });

    $('.menu-btn').on('click', function () {
        $('.ui.modal.mobile-menu').modal('show');
    });

    $('#enterSessionForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            dataType : 'json',
            data: {
                action: 'get_session_link',
                code: $('input[name="session_code"]').val(),
            },
            beforeSend: function() {
                $('.btn-blue-animated').attr('disabled', true);
                $('.session-code-error').html('');
            },
            complete: function() {
                $('.btn-blue-animated').removeAttr('disabled');
            },
            success: function (response) {
                if (!response.error) {
                    window.location.href = response.success;
                } else {
                    $('.session-code-error').html(response.error);
                }
            }
        });
    });

    $('#lessonsList .sessions-count').on('click', function () {
        $('.modal.ui.sessions-list').modal('show');
        $('.modal.ui.sessions-list .lesson-title').text($(this).siblings('.session-info').children('.name').text().trim());
        $.ajax({
            url: ajaxurl,
            type: 'GET',
            dataType : 'html',
            data: {
                action: 'get_sessions_list',
                lesson: $(this).attr('data-lesson-id'),
            },
            beforeSend: function() {

            },
            success: function (response) {
                console.log(response);
                $('.sessions-list.modal .sessions-list-wrap').html(response);
            }
        });
    });

});
