<template>
    <main class="dark-wrap">
        <template v-if="device === 'desktop' && (user_role === 'teacher' || (user_role === 'student' && student_movie_pc))">
            <video-player ref="videoPlayer"
                          v-if="playerObject"
                          class="player-full"
                          :movieData="playerObject"
                          :showControls="user_role === 'teacher'"
                          @first_play="$refs.videoPlayer.playerOpenFullscreen()"
                          @played="sendMessage({'player_event' : 'play'})"
                          @paused="sendMessage({'player_event' : 'pause'})"
                          @seeked="seeked_time => sendMessage({'player_seek': seeked_time})" >
                <div v-if="user_role === 'student'" class="mute-unmute-btn" @click="playerMuteUnmute()">
                    <template v-if="studentPlayerMuted">Unmute</template>
                    <template v-else>Mute</template>
                </div>
            </video-player>
        </template>
        <template v-else>
            <div class="slide-message">
                <span>Please raise your eyes up to the screen</span>
                <img src="/wp-content/themes/academe/assets/img/presentation.svg" />
            </div>
        </template>
    </main>
</template>

<script>
    import VideoPlayer from "../LessonEditor/Shared/VideoPlayer";
    import axios from "axios";
    import helper from "../../helper";

    export default {
        name: "MoviePresentation",
        components: {
            VideoPlayer,
        },
        props: {
            movie_id: Number,
            session_id: Number,
            post_type: String,
            session_code: String,
            user_role: String,
            device: String,
            websocket_url: String,
            student_movie_pc: String,
        },
        computed: {
            store() {
                return this.$store.state.LessonSlideshow;
            },
        },
        data() {
            return {
                studentPlayerMuted: true,
                playerObject: null,
            }
        },
        mounted() {
            /* SESSION WEBSOCKETS CODE */
            var _this =this;
            window.conn = new WebSocket(_this.websocket_url);

            window.conn.onopen = function(e) {
                console.log("Connection established!");
                _this.subscribe();
            };

            window.conn.onmessage = function(e) {
                var data = JSON.parse(e.data);
                if (data.message.player_event) {
                    switch (data.message.player_event) {
                        case 'play':
                            _this.$refs.videoPlayer.playerPlay();
                            break;
                        case 'pause':
                            _this.$refs.videoPlayer.playerPause();
                            break;
                    }
                } else if (data.message.player_seek) {
                    _this.$refs.videoPlayer.playerDoSeek(parseInt(data.message.player_seek));
                }

                //alert(data.message);
            };
            /* SESSION WEBSOCKETS CODE END */

            // Get movie data:
            axios.get('/wp/v2/'+this.post_type+'/'+this.movie_id).then(res1 => {
                if (_this.post_type === 'movie') {
                    _this.playerObject = {
                        'kaltura_id': res1.data.acf.kaltura_id,
                        'play_from': 0,
                        'play_to': 0
                    }
                } else {
                    axios.get('/wp/v2/movie/'+res1.data.acf.movie_id.ID).then(res2 => {
                        _this.playerObject = {
                            'kaltura_id': res2.data.acf.kaltura_id,
                            'play_from': helper.timeStringToSeconds(res1.data.acf.play_from),
                            'play_to': helper.timeStringToSeconds(res1.data.acf.play_to)
                        }
                    });
                }
            });
        },
        methods: {
            subscribe() {
                window.conn.send(JSON.stringify({
                    command: "subscribe",
                    message: {
                        channel: this.session_id,
                        role: this.user_role,
                    }
                }));
            },
            sendMessage(msg) {
                if (this.user_role === 'teacher') {
                    window.conn.send(JSON.stringify({command: "message", message: msg}));
                }
            },
            playerMuteUnmute() {
                if (this.studentPlayerMuted) {
                    this.$refs.videoPlayer.playerUnmute();
                } else {
                    this.$refs.videoPlayer.playerMute();
                }
                this.studentPlayerMuted = !this.studentPlayerMuted;
            },
        },
    }
</script>

<style scoped>
    .dark-wrap {
        color: #FFFFFF;
        position: relative;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .video-body {
        height: 100%;
        width: 100%;
    }
    .mute-unmute-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        border: 1px solid #FFFFFF;
        padding: 3px 5px;
        border-radius: 3px;
        cursor: pointer;
    }
    .slide-message,
    .slide-message span {
        text-align: center;
        font-size: 30px;
        line-height: 34px;
        color: #51acfd;
        display: block;
    }
    .slide-message img {
        max-width: 100%;
        height: 250px;
        margin-top: 20px;
    }
    @media screen and (max-width: 767px) {
        .slide-message {
            font-size: 24px;
            line-height: 28px;
            padding: 20px;
        }

        .slide-message img {
            max-width: 100%;
            height: 120px;
            margin-top: 20px;
        }
    }
</style>