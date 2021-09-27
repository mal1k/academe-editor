
<template>
    <main class="dark-wrap">
        <div class="slide-content" :class="user_role">
            <h1 v-if="store.course_content && user_role === 'student'" class="lesson-title">{{store.course_content.title.rendered}}</h1>

            <!-- Meta type: -->
            <template v-if="store.slide_type === 'meta'">
                <template v-if="user_role === 'teacher'">
                    <div class="aspect-ratio-box">
                        <div class="aspect-ratio-box-inside">
                            <div class="slide-meta-preview">
                                <img :src="store.course_content.acf.cover_image_url" class="cover-image" />
                                <div class="meta-details">
                                    <h1 class="lesson-name">{{store.course_content.title.rendered}}</h1>
                                    <p v-if="store.course_content.title.rendered" class="lesson-created-by">Lesson Created By: <span class="lesson-created-by-name">{{author}}</span></p>
                                    <p class="lesson-description" v-html="store.course_content.content.rendered"></p>
                                    <p class="lesson-terms" v-if="store.course_meta && store.course_meta.subjects.length">
                                        Subject: {{store.course_meta.subjects.slice(0,3).join(', ')}}
                                        <template v-if="store.course_meta.subjects.length > 3">...</template>
                                    </p>
                                    <p class="lesson-terms" v-if="store.course_meta && store.course_meta.topics.length">
                                        Topic: {{store.course_meta.topics.slice(0,3).join(', ')}}
                                        <template v-if="store.course_meta.topics.length > 3">...</template>
                                    </p>
                                    <p class="lesson-terms" v-if="store.course_meta && store.course_meta.grades.length">
                                        Grade: {{store.course_meta.grades.slice(0,3).join(', ')}}
                                        <template v-if="store.course_meta.grades.length > 3">...</template>
                                    </p>
                                    <p class="lesson-terms tags-list" v-if="store.course_meta && store.course_meta.tags.length">
                                        {{store.course_meta.tags.slice(0,3).join(' ')}}
                                        <template v-if="store.course_meta.tags.length > 3">...</template>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="slide-message">
                        <span>Please raise your eyes up to the screen</span>
                        <img src="/wp-content/themes/academe/assets/img/presentation.svg" />
                    </div>
                </template>
            </template>

            <!-- Quiz type: -->
            <div class="quiz-content" v-show="store.slide_type === 'question'"></div>

            <!-- Movie type: -->
            <template v-if="store.slide_type === 'movie'">
                <template v-if="playerObject">
                    <template v-if="user_role === 'teacher'">
                        <video-player ref="videoPlayer"
                                      class="player-full"
                                      :movieData="playerObject"
                                      @paused="checkForNextQuestion"
                                      @duration_received="setMovieDuration">
                            <div v-if="quizzes" class="questions-timeline">
                                <div v-if="store.active_movie_duration" class="question-marker"
                                     :style="'left:'+questionPosition(question.time)+'%'"
                                     v-for="(question, index) in quizzes"
                                     :key="question.id">
                                    <div>{{index + 1}}</div>
                                </div>
                            </div>
                            <div class="quiz-content-movie" :class="{ 'with-content' : this.movieQuizShowing }"></div>
                            <div v-if="this.movieQuizShowing && user_role === 'teacher'" @click="loadNextSegment" class="next-segment" :class="{ 'with-content' : this.movieQuizShowing }">
                                <div class="next-segment-btn">Continue watching</div>
                            </div>

                        </video-player>
                    </template>
                    <template v-if="user_role === 'student'">
                        <div v-show="movieQuizShowing" class="quiz-content-movie"></div>
                        <div v-show="!movieQuizShowing" class="slide-message">
                            <span>Please raise your eyes up to the screen</span>
                            <img src="/wp-content/themes/academe/assets/img/presentation.svg" />
                        </div>
                    </template>

                </template>
            </template>


            <!-- Text/Image type: -->
            <template v-if="store.slide_type === 'text_image'">
                <template v-if="user_role === 'teacher'">
                    <div class="aspect-ratio-box">
                        <div class="aspect-ratio-box-inside">
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template1'">
                                <div class="slide-template-preview flex-builder">
                                    <div class="row h-30">
                                        <div class="col w-100 text-title"
                                             :style="{backgroundColor: predefined_template.template1_text1.template1_text1_fill_color}">

                                            <div class="formatted-text" :style="{
                                                fontFamily: predefined_template.template1_text1.template1_text1_font,
                                                fontSize: predefined_template.template1_text1.template1_text1_font_size,
                                                lineHeight: '1.1em',
                                                color: predefined_template.template1_text1.template1_text1_text_color,
                                                fontWeight: predefined_template.template1_text1.template1_text1_font_weight,
                                                textAlign: predefined_template.template1_text1.template1_text1_text_align,
                                            }" v-html="predefined_template.template1_text1.template1_text1_text">
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row h-70">
                                        <div class="col w-50 free-text"
                                             :style="{backgroundColor: predefined_template.template1_text2.template1_text2_fill_color}">

                                            <div class="formatted-text" :style="{
                                                fontFamily: predefined_template.template1_text2.template1_text2_font,
                                                fontSize: predefined_template.template1_text2.template1_text2_font_size,
                                                lineHeight: '1.1em',
                                                color: predefined_template.template1_text2.template1_text2_text_color,
                                                fontWeight: predefined_template.template1_text2.template1_text2_font_weight,
                                                textAlign: predefined_template.template1_text2.template1_text2_text_align,
                                            }" v-html="predefined_template.template1_text2.template1_text2_text">

                                            </div>

                                        </div>
                                        <div class="col w-50 media">
                                            <img v-if="predefined_template.template1_media1.template1_media1_image"
                                                 :src="predefined_template.template1_media1.template1_media1_image"
                                                 style="width: 100%; height: 100%; object-fit: cover;"/>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template2'">
                                <div class="slide-template-preview flex-builder">
                                    <div class="row h-30">
                                        <div class="col max-w-50"></div>
                                    </div>
                                    <div class="row h-70">
                                        <div class="col w-100"></div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template3'">
                                <div class="slide-template-preview flex-builder">

                                </div>
                            </template>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="slide-message">
                        <span>Please raise your eyes up to the screen</span>
                        <img src="/wp-content/themes/academe/assets/img/presentation.svg" />
                    </div>
                </template>
            </template>


        </div>

        <!-- Controls available only for teachers -->
        <template v-if="user_role === 'teacher'">
            <div class="slide-nav prev-slide" :class="{'not-active': !store.prev_slide}" @click="goToSlide(store.prev_slide)">❮</div>
            <div class="slide-nav next-slide" :class="{'not-active': !store.next_slide}" @click="goToSlide(store.next_slide)">❯</div>
        </template>
    </main>
</template>

<script>
    import Template1 from "../LessonEditor/Shared/SlideTemplates/Template1";
    import VideoPlayer from "../LessonEditor/Shared/VideoPlayer";

    export default {
        name: "Slideshow",
        components: {
            Template1,
            VideoPlayer,
        },
        props: {
            websocket_url: String,
            course_id: Number,
            session_id: Number,
            session_code: String,
            author: String,
            user_role: String,
            device: String,
            current_slide: Number,
        },
        computed: {
            store() {
                return this.$store.state.LessonSlideshow;
            },
            predefined_template() {
              return this.store.slide_content.acf[this.store.slide_content.acf.pre_defined_template];
            },
        },
        data() {
            return {
                course_steps: null,
                quizzes: null,
                playerObject: null,
                nextQuizIndex: null,
                movieQuizShowing: false,
                lastQuestionLoaded: false,
            }
        },
        mounted() {

            /* SESSION WEBSOCKETS CODE */
            var _this =this;
            window.conn = new WebSocket(_this.websocket_url);

            window.conn.onopen = function(e) {
                console.log("Connection established!");
                _this.subscribe(_this.session_id);
            };

            window.conn.onmessage = function(e) {
                var data = JSON.parse(e.data);
                if (data.message.load_slide) {
                    _this.movieQuizShowing = false;
                    _this.getSlideContent(data.message.load_slide);
                } else if (data.message.load_quiz) {
                    _this.getQuiz(data.message.load_quiz);
                    _this.movieQuizShowing = true;
                } else if (data.message.hide_quiz) {
                    _this.movieQuizShowing = false;
                }

                //alert(data.message);
            };
            /* SESSION WEBSOCKETS CODE END */

            // Get course
            axios.get('/ldlms/v1/sfwd-courses/'+this.course_id).then(res => {
                this.store.course_content = res.data;

                axios.get('/academe/v1/get-lesson-meta-terms?lesson_id='+this.course_id).then(res => {
                    _this.store.course_meta = res.data
                });

            });

            // Get course slides
            axios.get('/ldlms/v2/sfwd-courses/'+this.course_id+'/steps').then(res => {
                console.log(res.data);
                this.course_steps = res.data;
                if (this.current_slide) {
                    this.getSlideContent(this.current_slide);
                } else {
                    let slides = res.data.t["sfwd-lessons"];
                    this.getSlideContent(slides[0]);
                }
            });

        },
        methods: {
            subscribe(channel) {
                window.conn.send(JSON.stringify({command: "subscribe", channel: channel}));
            },
            sendMessage(msg) {
                window.conn.send(JSON.stringify({command: "message", message: msg}));
            },
            goToSlide(slide_id) {
                // Nullify before load next slide:
                this.playerObject = null;
                this.movieQuizShowing = false;
                this.lastQuestionLoaded = false;
                this.store.active_movie_duration = null;

                this.getSlideContent(slide_id);
                this.sendMessage({"load_slide" : slide_id});
                jQuery.get({
                    url: ajaxurl,
                    data: {
                        action: 'update_session_current_slide',
                        session_id: this.session_id,
                        slide_id: slide_id,
                    },
                });
            },
            getSlideContent(slide_id) {
                if (!slide_id)
                    return;
                jQuery('.quiz-content').html('');
                jQuery('.quiz-content-movie').html('');
                // get current slide
                axios.get('/ldlms/v1/sfwd-lessons/'+slide_id).then(res => {
                    console.log("Slide content:");
                    console.log(res.data);

                    this.store.slide_content = res.data;
                    this.store.slide_type = res.data.acf.slide_type;
                    this.store.next_slide = res.data.next_lesson;
                    this.store.prev_slide = res.data.prev_lesson;

                    switch (this.store.slide_type) {
                        case "text_image":
                            break;
                        case "movie":
                            axios.get('/academe/v1/get-lesson-quizzes?course='+this.course_id+'&lesson='+slide_id).then(res => {
                                console.log(res.data);
                                this.quizzes = res.data;

                                // Sort quizzes in ASC order by showing time
                                this.quizzes.sort(function (a, b) {
                                    if (a.time > b.time) { return 1; }
                                    if (a.time < b.time) { return -1; }
                                    return 0;
                                });


                                // Trick to create not updatable copy of store object
                                var tempPlayerObject = JSON.parse(JSON.stringify(this.store.slide_content.acf.movie_slide));
                                // Set first question (quiz) point as mediaPlayTo
                                if (tempPlayerObject.play_to != 0) {
                                    tempPlayerObject.play_to = this.quizzes[0].time;
                                }
                                this.playerObject = tempPlayerObject;

                                // Set next quiz index
                                this.nextQuizIndex = 0;
                            });


                            break;
                        case "question":
                            var lesson = this.course_steps.h['sfwd-lessons'][slide_id];
                            var quiz_id = Object.keys(lesson['sfwd-quiz'])[0];
                            this.getQuiz(quiz_id);
                            break;
                    }
                });
            },
            getQuiz(quiz_id) {
                var _this = this;
                jQuery.get({
                    url: ajaxurl,
                    data: {
                        action: 'render_quiz_content',
                        quiz_id: quiz_id,
                    }
                }).then(res => {
                    if (_this.store.slide_type === 'movie') {
                        this.movieQuizShowing = true;
                        jQuery('.quiz-content-movie').html(res);
                    } else if (_this.store.slide_type === 'question') {
                        jQuery('.quiz-content').html(res);
                    }

                });
            },
            checkForNextQuestion(time) {
                // Player last time paused:
                if (parseInt(this.store.slide_content.acf.movie_slide.play_to) === time) {
                    console.log("Player last time paused");
                    return;
                }

                // Check if paused exactly on quiz time & load current quiz:
                console.log('player paused at ' + time + ' / curr quiz at ' + this.quizzes[this.nextQuizIndex].time);
                if ( parseInt(this.quizzes[this.nextQuizIndex].time) === time) { //
                    this.sendMessage({"load_quiz" : this.quizzes[this.nextQuizIndex].id});
                    this.getQuiz(this.quizzes[this.nextQuizIndex].id);
                    this.$refs.videoPlayer.playerCloseFullscreen();

                    // Update next quiz
                    if (this.quizzes.length > this.nextQuizIndex+1) { // if next quiz exists (not last quiz already loaded)
                        this.nextQuizIndex++;
                        this.quizzes[this.nextQuizIndex].id;
                        //this.$refs.videoPlayer.nextSegment(this.quizzes[this.nextQuizIndex].time)
                    } else { // last question
                        this.lastQuestionLoaded = true;
                    }
                }

            },
            loadNextSegment() {
                this.movieQuizShowing = false; // hide overlap & control buttons
                let next_stop = this.lastQuestionLoaded
                    ? this.store.slide_content.acf.movie_slide.play_to
                    : this.quizzes[this.nextQuizIndex].time;
                this.$refs.videoPlayer.nextSegment(next_stop); // change next stop in player
                jQuery('.quiz-content-movie').html(''); // remove completed quiz content

                this.sendMessage({"hide_quiz" : true}); // close previous completed quiz for students
            },
            questionPosition(show_at) {
                return parseInt(show_at) / this.store.active_movie_duration * 100;
            },
            setMovieDuration(duration) {
                this.store.active_movie_duration = duration;
            }
        }
    }
</script>

<style scoped>
    .dark-wrap {
        color: #FFFFFF;
        position: relative;
    }
    .dark-wrap, .slide-content {
        height: 100%;
    }
    .slide-content {
        padding: 40px 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }
    h1.lesson-title {
        position: absolute;
        top: 20px;
        padding: 0 30px;
        text-align: center;
        text-transform: uppercase;
        font-weight: 500;
    }
    .slide-nav {
        position: absolute;
        height: 40px;
        width: 40px;
        top: calc(50% - 20px);
        display: flex;
        justify-content: center;
        align-items: center;
        background: #51acfd;
        border-radius: 3px;
        cursor: pointer;
        user-select: none;
        transition: .2s;
    }
    .slide-nav.not-active {
        opacity: .4;
        cursor: default;
    }
    .slide-nav:not(.not-active):hover {
        transform: scale(1.1);
    }
    .slide-nav.prev-slide {
        left: 20px;
    }
    .slide-nav.next-slide {
        right: 20px;
    }
    .slide-content .slide-message,
    .slide-content .slide-message span {
        text-align: center;
        font-size: 30px;
        line-height: 34px;
        color: #51acfd;
        display: block;
    }

    /* Text/Image slide */
    .aspect-ratio-box {
        /*height: 0;*/
        /*padding-top: 56%;*/
        overflow: hidden;
        background: #2F2F2F;
        position: relative;
        box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.8);
        border-radius: 2px;
        aspect-ratio: 16/9;
        max-height: 100%;
        margin: auto;
        min-width: 80%;
        max-width: 100%;
    }
    .aspect-ratio-box-inside {
        /*position: absolute;*/
        /*top: 0;*/
        /*left: 0;*/
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .aspect-ratio-box .slide-template-preview {
        height: 100%;
        width: 100%;
        border: none;
        margin-bottom: 0;
        cursor: default;
    }
    .aspect-ratio-box .slide-template-preview .col {
        padding: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }
    .aspect-ratio-box .slide-template-preview .col.free-text {
        align-items: flex-start;
    }
    .aspect-ratio-box .slide-template-preview .col.media {
        padding: 0;
    }
    .aspect-ratio-box .slide-template-preview .col .content-type {
        height: 100%;
        display: flex;
        align-items: center;
    }
    .flex-builder .row {
        display: flex;
        justify-content: space-between;
        flex-direction: row;
        width: 100%
    }
    .flex-builder .col {
        position: relative;
        display: flex;
        flex: 1;
        margin: 3%;
        background: rgba(196, 196, 196, 0.64);
    }
    .flex-builder .w-100 {
        width: 100%;
    }
    .flex-builder .w-50 {
        flex: 1 1 50%;
    }
    .flex-builder .max-w-50 {
        max-width: 50%;
    }
    .flex-builder .h-30 {
        height: 30%;
    }
    .flex-builder .h-40 {
        height: 40%;
    }
    .flex-builder .h-50 {
        height: 50%;
    }
    .flex-builder .h-70 {
        height: 70%;
    }
    /* Text/Image slide end */

    .player-full {
        height: 100%;
        width: 100%;
    }

    .questions-timeline {
        position: absolute;
        bottom: 55px;
        width: 100%;
        opacity: 0;
        transition: .5s;
    }
    .question-marker {
        height: 16px;
        width: 16px;
        background: #51acfd;
        border-radius: 10px;
        display: flex;
        align-items: center;
        margin-top: 0px;
        justify-content: center;
        position: absolute;
        top: 0;
        cursor: pointer;
        box-shadow: 0px 0px 5px 0px black;
    }
    .question-marker:after {
        content: '';
        position: absolute;
        left: 2px;
        top: 14px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 7px solid #51acfd;
        clear: both;
    }
    .question-marker > div {
        font-size: 10px;
        margin-top: 3px;
    }

    .cover-preview {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        height: 100%;
        font-weight: 600;
    }
    .cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .slide-meta-preview {
        position: relative;
        height: 100%;
        width: 100%;
    }
    .meta-details {
        position: absolute;
        bottom: 0;
        left: 0;
        padding-left: 40px;
        padding-bottom: 60px;
    }
    .meta-details * {
        text-shadow: 0px 0px 15px black;
    }
    .lesson-name {
        font-size: 39px;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 15px;
    }
    .lesson-created-by {
        font-size: 18px;
        font-weight: 500;
        margin-bottom: 20px;
    }
    .lesson-created-by-name {
        font-size: 18px;
        font-weight: 600;
        color: #51ACFD;
        text-shadow: none !important;
    }
    .lesson-description {
        color: #F2F2F2;
        line-height: 24px;
        max-width: 400px;
        text-shadow: 0px 0px 15px black;
        margin-bottom: 20px;
    }
    .lesson-terms {
        margin-bottom: 5px;
        font-size: 15px;
    }
    .lesson-terms.tags-list {
        color: #51ACFD;
        margin-top: 10px;
    }
    .slide-content .slide-message img {
        max-width: 100%;
        height: 250px;
        margin-top: 20px;
    }
    @media screen and (max-width: 767px) {
        .slide-content {
            padding: 30px;
        }
        .slide-content .slide-message {
            font-size: 24px;
            line-height: 28px;
        }
        .slide-content .slide-message img {
            max-width: 100%;
            height: 120px;
            margin-top: 20px;
        }
    }
</style>

<style>
    input[type="radio"].wpProQuiz_questionInput {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 9999px !important;
        max-width: 9999px !important;
    }
    .wpProQuiz_questionInput:checked:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: #51ACFD;
        z-index: -1;
    }
    .wpProQuiz_results, .wpProQuiz_sending {
        text-align: center;
    }
    .wpProQuiz_results::before {
        content: url(/wp-content/themes/academe/assets/img/positive-comment.svg);
    }
    .wpProQuiz_results::after {
        content: url(/wp-content/themes/academe/assets/img/people-likes.svg);
    }
    .wpProQuiz_results .wpProQuiz_header,
    .wpProQuiz_sending .wpProQuiz_header {

        color: #51ACFD;
        font-size: 20px;
    }

    .learndash-wrapper .wpProQuiz_graded_points, .learndash-wrapper .wpProQuiz_points {
        border: 2px solid #51acfd !important;
        color: #ffffff !important;
        font-weight: 600;
    }
    .learndash-wrapper input[name="next"].wpProQuiz_QuestionButton {
        background: #51ACFD;
        border: 1px solid #FFFFFF;
        box-sizing: border-box;
        border-radius: 5px;
        width: 100%;
        font-size: 16px;
        font-weight: 600;
        text-transform: lowercase;
        margin: 3px;
    }
    .wpProQuiz_content .wpProQuiz_questionListItem {
        padding: 3px 0 !important;
    }
    .wpProQuiz_content .wpProQuiz_questionListItem, .wpProQuiz_content .wpProQuiz_questionListItem input[type='radio'] {
        cursor: pointer;
    }
    .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
        position: relative;
        overflow: hidden;
    }
    .learndash-wrapper .quiz_continue_link {
        display: none !important;
    }
    .learndash-wrapper .wpProQuiz_graded_points, .learndash-wrapper .wpProQuiz_points {
        background: #212121;
    }
    .learndash-wrapper .ld-quiz-actions {
        border: none;
    }
    input[name="startQuiz"] {

    }
    html, body {
        height: 100%;
    }
    footer {
        padding: 0;
    }
    #app {
        height: calc(100% - 82px);
    }
    .quiz-content-movie.with-content {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .quiz-content-movie.with-content > div.learndash {
        width: 80%;
        padding: 40px;
        background: #131212;
        max-height: 80%;
        overflow-y: scroll;
        margin-bottom: 80px;
    }
    .slide-content.teacher input[name="next"].wpProQuiz_QuestionButton {
        display: none !important;
    }
    .slide-content.teacher .quiz-content {
        width: 100%;
    }
    .slide-content.teacher .quiz-content > div.learndash {
        padding: 40px 10%;
        background: #2F2F2F;
    }
    .quiz-content-movie.with-content > div.learndash::-webkit-scrollbar-thumb {
        border: 5px solid rgba(0, 0, 0, 0);
        background-clip: padding-box;
        -webkit-border-radius: 7px;
        height: 30px;
    }
    .quiz-content-movie.with-content > div.learndash::-webkit-scrollbar {
        width: 14px;
        height: 18px;
    }
    .quiz-content-movie.with-content > div.learndash::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
    }
    .next-segment {
        position: absolute;
        bottom: 80px;
        left: 0px;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .next-segment-btn {
        border: 1px solid #51ACFD;
        border-radius: 3px;
        padding: 8px 15px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: .2s;
        color: #51ACFD;
        font-weight: 600;
    }
    #slide-movie:hover .questions-timeline {
        opacity: 1;
    }
    @media screen and (max-width: 767px) {
        .quiz-content-movie {
            width: 100%;
        }
        .quiz-content-movie.with-content > div.learndash {
            max-height: none;
            margin-bottom: 0;
        }
    }
</style>