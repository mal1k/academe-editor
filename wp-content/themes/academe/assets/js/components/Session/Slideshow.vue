<template>
    <main class="dark-wrap">
        <div class="slide-content" :class="user_role">
            <h1 v-if="store.course_content && user_role === 'student'" class="lesson-title">{{store.course_content.title.rendered}}</h1>
            <div v-if="store.course_content" class="aspect-ratio-box" :style="'background-image: url('+store.course_content.default_cover+'); background-size: cover;'">
                <div class="aspect-ratio-box-inside">
                    <!-- Meta type: -->
                    <template v-if="store.slide_type === 'meta'">
                        <template v-if="user_role === 'teacher' || (user_role === 'student' && student_movie_pc)">
                            <div class="slide-meta-preview">
                                <img :src="store.course_content.acf.cover_image_url" class="cover-image" />
                                <div class="cover-gradient"></div>
                                <div class="meta-details">
                                    <div v-if="user_role === 'student' && !store.teacher_online" class="waiting-screen">
                                        <template v-if="lesson_started()">
                                            <div class="message">THE LESSON WILL START SOON</div>
                                        </template>
                                        <template v-else>
                                            <div class="message">THE LESSON WILL START AT</div>
                                            <div class="message">{{lesson_will_start_at}}</div>
                                        </template>
                                    </div>
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
                                    <p class="lesson-terms movies-list" v-if="store.course_content && store.course_content.acf.participant_movies">
                                        <svg class="movies-icon" width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.125 0.5C1.09684 0.5 0.25 1.34684 0.25 2.375V12.375C0.25 13.4032 1.09684 14.25 2.125 14.25H15.875C16.9032 14.25 17.75 13.4032 17.75 12.375V2.375C17.75 1.34684 16.9032 0.5 15.875 0.5H2.125ZM2.125 1.75H15.875C16.2268 1.75 16.5 2.02316 16.5 2.375V12.375C16.5 12.7268 16.2268 13 15.875 13H2.125C1.77316 13 1.5 12.7268 1.5 12.375V2.375C1.5 2.02316 1.77316 1.75 2.125 1.75ZM2.75 3V4.25H4V3H2.75ZM14 3V4.25H15.25V3H14ZM2.75 5.5V6.75H4V5.5H2.75ZM14 5.5V6.75H15.25V5.5H14ZM2.75 8V9.25H4V8H2.75ZM14 8V9.25H15.25V8H14ZM2.75 10.5V11.75H4V10.5H2.75ZM14 10.5V11.75H15.25V10.5H14Z" fill="white"/>
                                        </svg>
                                        {{store.course_content.acf.participant_movies}}
                                    </p>
                                    <p class="lesson-terms tags-list" v-if="store.course_meta && store.course_meta.tags.length">
                                        <span class="tags-icon">#</span>
                                        {{store.course_meta.tags.slice(0,3).join(' ')}}
                                        <template v-if="store.course_meta.tags.length > 3">...</template>
                                    </p>
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
                            <template v-if="device === 'desktop'">
                                <video-player ref="videoPlayer"
                                              v-if="user_role === 'teacher' || (user_role === 'student' && student_movie_pc)"
                                              class="player-full"
                                              :movieData="playerObject"
                                              :showControls="user_role === 'teacher'"
                                              @played="sendMessage({'player_event' : 'play'})"
                                              @paused="checkForNextQuestion"
                                              @seeked="checkQuestionAfterSeek"
                                              @duration_received="setMovieDuration">
                                    <div v-if="quizzes && user_role === 'teacher'" class="questions-timeline">
                                        <div v-if="store.active_movie_duration" class="question-marker"
                                             :style="'left:'+questionPosition(question.time)+'%'"
                                             v-for="(question, index) in quizzes"
                                             :key="question.id">
                                            <div>{{index + 1}}</div>
                                        </div>
                                    </div>
                                    <div class="selected-timeline" v-if="user_role === 'teacher'">
                                        <template v-if="store.active_movie_duration">
                                            <div class="before-selected-part" :style="'width:'+questionPosition(parseInt(store.slide_content.acf.movie_slide.play_from))+'%'"></div>
                                            <div class="selected-part" :style="'width:'+selectedWidth()+'%'"></div>
                                            <div class="after-selected-part"></div>
                                        </template>
                                    </div>
                                    <div class="quiz-content-movie" :class="{ 'with-content' : this.movieQuizShowing }"></div>
                                    <div v-if="this.movieQuizShowing && user_role === 'teacher'" @click="loadNextSegment" class="next-segment" :class="{ 'with-content' : this.movieQuizShowing }">
                                        <div class="next-segment-btn">Continue</div>
                                    </div>
                                    <div v-if="user_role === 'student'" class="mute-unmute-btn" @click="playerMuteUnmute()">
                                        <template v-if="studentPlayerMuted">
                                            <svg  xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="40" height="40" x="0" y="0" viewBox="0 0 511.999 511.999" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                                <path d="M458.217,276.92l49.389-49.389c5.858-5.858,5.858-15.354,0-21.211c-5.857-5.858-15.354-5.858-21.211,0l-49.39,49.39    l-49.39-49.39c-5.857-5.858-15.354-5.858-21.211,0c-5.858,5.858-5.858,15.354,0,21.211l49.389,49.389l-49.389,49.389    c-5.858,5.858-5.858,15.354,0,21.211c5.857,5.857,15.354,5.858,21.211,0l49.39-49.39l49.39,49.39    c5.857,5.857,15.354,5.858,21.211,0c5.858-5.858,5.858-15.354,0-21.211L458.217,276.92z" fill="#51acfd" data-original="#51acfd" style="" class=""></path>
                                                <path d="M304.064,4.321c-15.982-7.486-34.333-5.118-47.891,6.181L129.559,116.013H44.997C20.185,116.013,0,136.198,0,161.01    v189.98c0,24.811,20.185,44.997,44.997,44.997h84.563l126.614,105.511c13.572,11.31,31.925,13.66,47.892,6.182    c15.982-7.486,25.91-23.1,25.91-40.749V45.069C329.975,27.42,320.047,11.807,304.064,4.321z M119.991,366.188H44.997    c-8.27,0-14.999-6.928-14.999-15.199V161.01c0-8.27,6.729-14.999,14.999-14.999h74.994V366.188z M299.978,466.93    c0,8.849-6.61,12.634-8.637,13.582c-2.026,0.95-9.165,3.606-15.964-2.061L149.989,373.962V138.037l125.388-104.49    c6.799-5.666,13.939-3.01,15.964-2.061c2.026,0.949,8.637,4.733,8.637,13.583V466.93z" fill="#51acfd" data-original="#51acfd" style="" class=""></path>
                                            </svg>
                                        </template>
                                        <template v-else>
                                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="40" height="40" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                                <path d="M303.462,4.82c-15.95-7.472-34.265-5.107-47.797,6.168L129.303,116.29H44.908C20.146,116.29,0,136.436,0,161.198v189.604    c0,24.762,20.146,44.908,44.908,44.908h84.395l126.363,105.302c13.545,11.288,31.861,13.633,47.798,6.169    c15.95-7.472,25.859-23.055,25.859-40.668V45.487C329.322,27.873,319.413,12.29,303.462,4.82z M119.753,365.97H44.908    c-8.254,0-14.969-6.915-14.969-15.169V161.198c0-8.254,6.715-14.969,14.969-14.969h74.846V365.97z M299.384,466.512    c0,8.832-6.597,12.609-8.62,13.555c-2.022,0.948-9.147,3.599-15.932-2.057L149.692,373.728V138.271L274.831,33.988    c6.785-5.654,13.91-3.004,15.932-2.057c2.022,0.947,8.62,4.723,8.62,13.556V466.512z" fill="#51acfd" data-original="#51acfd" style="" class=""></path>
                                                <path d="M375.505,200.174c-4.59-6.877-13.884-8.731-20.76-4.141c-6.877,4.59-8.731,13.884-4.141,20.76    c15.488,23.208,15.482,53.214-0.016,76.444c-4.589,6.877-2.732,16.172,4.144,20.76c6.879,4.59,16.173,2.732,20.76-4.144    C397.725,276.527,397.73,233.476,375.505,200.174z" fill="#51acfd" data-original="#51acfd" style="" class=""></path>
                                                <path d="M475.166,133.758l-6.71-10.072c-4.584-6.881-13.876-8.742-20.757-4.158c-6.881,4.584-8.742,13.877-4.158,20.757    l6.714,10.078c42.393,63.562,42.387,145.718-0.017,209.303l-6.695,10.042c-4.586,6.879-2.726,16.173,4.152,20.758    c6.878,4.585,16.172,2.727,20.758-4.152l6.694-10.041C524.279,302.601,524.284,207.405,475.166,133.758z" fill="#51acfd" data-original="#51acfd" style="" class=""></path>
                                                <path d="M425.335,166.956l-6.717-10.075c-4.586-6.879-13.88-8.737-20.758-4.151c-6.879,4.586-8.737,13.879-4.151,20.758    l6.716,10.075c28.93,43.396,28.93,99.476,0,142.873l-6.717,10.075c-4.587,6.879-2.727,16.173,4.151,20.758    c6.88,4.587,16.174,2.726,20.758-4.151l6.717-10.075C460.992,289.558,460.992,220.44,425.335,166.956z" fill="#51acfd" data-original="#51acfd" style="" class=""></path>
                                            </svg>
                                        </template>
                                    </div>

                                </video-player>
                            </template>
                            <template v-if="device === 'mobile' || (device === 'desktop' && user_role === 'student' && !student_movie_pc)">
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
                        <template v-if="user_role === 'teacher' || (user_role === 'student' && student_movie_pc)">
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template1'">
                                <div class="slide-template-preview flex-builder">
                                    <div class="row h-30">
                                        <div class="col w-100 text-title"
                                             :style="{/*backgroundColor: predefined_template.template1_text1.template1_text1_fill_color*/}">

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
                                             :style="{/*backgroundColor: predefined_template.template1_text2.template1_text2_fill_color*/}">

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
                                        <div class="col max-w-60 m-s text-title"
                                            :style="{ /*backgroundColor: predefined_template.template1_text1.template1_text1_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text1.template1_text1_font,
                                  fontSize: predefined_template.template1_text1.template1_text1_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text1.template1_text1_text_color,
                                  fontWeight: predefined_template.template1_text1.template1_text1_font_weight,
                                  textAlign: predefined_template.template1_text1.template1_text1_text_align,}"
                                                    v-html="predefined_template.template1_text1.template1_text1_text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row h-70">
                                        <div class="col m-s w-50 free-text"
                                            :style="{/*backgroundColor: predefined_template.template1_text2.template1_text2_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text2.template1_text2_font,
                                  fontSize: predefined_template.template1_text2.template1_text2_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text2.template1_text2_text_color,
                                  fontWeight: predefined_template.template1_text2.template1_text2_font_weight,
                                  textAlign: predefined_template.template1_text2.template1_text2_text_align,}"
                                                    v-html="predefined_template.template1_text2.template1_text2_text"
                                            ></div>
                                        </div>
                                        <div class="w-50">
                                            <div class="row h-50">
                                                <div class="col w-50 media">
                                                    <template>
                                                        <img
                                                                v-if="predefined_template.template1_media1.template1_media1_image"
                                                                :src="predefined_template.template1_media1.template1_media1_image"
                                                                style="width: 100%; height: 100%; object-fit: cover"
                                                        />
                                                    </template>
                                                </div>
                                                <div class="col w-50 media">
                                                    <template>
                                                        <img
                                                                v-if="predefined_template.template1_media2.template1_media2_image"
                                                                :src="predefined_template.template1_media2.template1_media2_image"
                                                                style="width: 100%; height: 100%; object-fit: cover"
                                                        />
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="row h-50">
                                                <div class="col w-50 media">
                                                    <template>
                                                        <img
                                                                v-if="predefined_template.template1_media3.template1_media3_image"
                                                                :src="predefined_template.template1_media3.template1_media3_image"
                                                                style="width: 100%; height: 100%; object-fit: cover"
                                                        />
                                                    </template>
                                                </div>
                                                <div class="col w-50 media">
                                                    <template>
                                                        <img
                                                                v-if="predefined_template.template1_media4.template1_media4_image"
                                                                :src="predefined_template.template1_media4.template1_media4_image"
                                                                style="width: 100%; height: 100%; object-fit: cover"
                                                        />
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template3'">
                                <div class="slide-template-preview flex-builder">
                                    <div class="row h-30">
                                        <div class="col max-w-60 m-s text-title"
                                            :style="{ /*backgroundColor: predefined_template.template1_text1.template1_text1_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text1.template1_text1_font,
                                  fontSize: predefined_template.template1_text1.template1_text1_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text1.template1_text1_text_color,
                                  fontWeight: predefined_template.template1_text1.template1_text1_font_weight,
                                  textAlign: predefined_template.template1_text1.template1_text1_text_align,}"
                                                    v-html="predefined_template.template1_text1.template1_text1_text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row h-70">
                                        <div class="col m-s w-100 free-text"
                                            :style="{/*backgroundColor: predefined_template.template1_text2.template1_text2_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text2.template1_text2_font,
                                  fontSize: predefined_template.template1_text2.template1_text2_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text2.template1_text2_text_color,
                                  fontWeight: predefined_template.template1_text2.template1_text2_font_weight,
                                  textAlign: predefined_template.template1_text2.template1_text2_text_align,}"
                                                    v-html="predefined_template.template1_text2.template1_text2_text"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template6'">
                                <div class="slide-template-preview flex-builder">
                                    <div class="row h-30">
                                        <div class="col w-100 m-s text-title"
                                            :style="{ /*backgroundColor: predefined_template.template1_text1.template1_text1_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text1.template1_text1_font,
                                  fontSize: predefined_template.template1_text1.template1_text1_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text1.template1_text1_text_color,
                                  fontWeight: predefined_template.template1_text1.template1_text1_font_weight,
                                  textAlign: predefined_template.template1_text1.template1_text1_text_align,}"
                                                    v-html="predefined_template.template1_text1.template1_text1_text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row h-70">
                                        <div class="col m-s w-50 free-text"
                                            :style="{/*backgroundColor: predefined_template.template1_text2.template1_text2_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text2.template1_text2_font,
                                  fontSize: predefined_template.template1_text2.template1_text2_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text2.template1_text2_text_color,
                                  fontWeight: predefined_template.template1_text2.template1_text2_font_weight,
                                  textAlign: predefined_template.template1_text2.template1_text2_text_align,}"
                                                    v-html="predefined_template.template1_text2.template1_text2_text"
                                            ></div>
                                        </div>
                                        <div class="w-50">
                                            <div class="row h-50">
                                                <div class="col w-100 free-text"
                                                    :style="{/*backgroundColor: predefined_template.template1_text3.template1_text3_fill_color,*/}">
                                                    <div class="formatted-text"
                                                        :style="{ fontFamily: predefined_template.template1_text3.template1_text3_font,
                                        fontSize: predefined_template.template1_text3.template1_text3_font_size,
                                        lineHeight: '1.1em',
                                        color: predefined_template.template1_text3.template1_text3_text_color,
                                        fontWeight: predefined_template.template1_text3.template1_text3_font_weight,
                                        textAlign: predefined_template.template1_text3.template1_text3_text_align,}"
                                                            v-html="predefined_template.template1_text3.template1_text3_text"
                                                    ></div>
                                                </div>
                                            </div>
                                            <div class="row h-50">
                                                <div class="col w-100 free-text"
                                                    :style="{/*backgroundColor: predefined_template.template1_text4.template1_text4_fill_color,*/}">
                                                    <div class="formatted-text"
                                                        :style="{ fontFamily: predefined_template.template1_text4.template1_text4_font,
                                        fontSize: predefined_template.template1_text4.template1_text4_font_size,
                                        lineHeight: '1.1em',
                                        color: predefined_template.template1_text4.template1_text4_text_color,
                                        fontWeight: predefined_template.template1_text4.template1_text4_font_weight,
                                        textAlign: predefined_template.template1_text4.template1_text4_text_align,}"
                                                            v-html="predefined_template.template1_text4.template1_text4_text"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="this.store.slide_content.acf.pre_defined_template === 'template7'">
                                <div class="slide-template-preview flex-builder">
                                    <div class="row h-30">
                                        <div class="col w-100 m-s text-title"
                                            :style="{ /*backgroundColor: predefined_template.template1_text1.template1_text1_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text1.template1_text1_font,
                                  fontSize: predefined_template.template1_text1.template1_text1_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text1.template1_text1_text_color,
                                  fontWeight: predefined_template.template1_text1.template1_text1_font_weight,
                                  textAlign: predefined_template.template1_text1.template1_text1_text_align,}"
                                                    v-html="predefined_template.template1_text1.template1_text1_text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row h-30">
                                        <div class="col w-50 m-s free-text"
                                            :style="{/*backgroundColor: predefined_template.template1_text3.template1_text3_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text3.template1_text3_font,
                                  fontSize: predefined_template.template1_text3.template1_text3_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text3.template1_text3_text_color,
                                  fontWeight: predefined_template.template1_text3.template1_text3_font_weight,
                                  textAlign: predefined_template.template1_text3.template1_text3_text_align,}"
                                                    v-html="predefined_template.template1_text3.template1_text3_text"
                                            ></div>
                                        </div>
                                        <div class="col w-50 m-s free-text"
                                            :style="{/*backgroundColor: predefined_template.template1_text4.template1_text4_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text4.template1_text4_font,
                                  fontSize: predefined_template.template1_text4.template1_text4_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text4.template1_text4_text_color,
                                  fontWeight: predefined_template.template1_text4.template1_text4_font_weight,
                                  textAlign: predefined_template.template1_text4.template1_text4_text_align,}"
                                                    v-html="predefined_template.template1_text4.template1_text4_text"
                                            ></div>
                                        </div>
                                    </div>
                                    <div class="row h-40">
                                        <div class="col m-s w-50 free-text"
                                            :style="{/*backgroundColor: predefined_template.template1_text2.template1_text2_fill_color,*/}">
                                            <div class="formatted-text"
                                                :style="{ fontFamily: predefined_template.template1_text2.template1_text2_font,
                                  fontSize: predefined_template.template1_text2.template1_text2_font_size,
                                  lineHeight: '1.1em',
                                  color: predefined_template.template1_text2.template1_text2_text_color,
                                  fontWeight: predefined_template.template1_text2.template1_text2_font_weight,
                                  textAlign: predefined_template.template1_text2.template1_text2_text_align,}"
                                                    v-html="predefined_template.template1_text2.template1_text2_text"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </template>
                        <template v-else>
                            <div class="slide-message">
                                <span>Please raise your eyes up to the screen</span>
                                <img src="/wp-content/themes/academe/assets/img/presentation.svg" />
                            </div>
                        </template>
                    </template>
                    <template v-if="store.slide_type === 'end'">
                        <div class="slide-meta-preview">
                            <img :src="store.course_content.acf.cover_image_url" class="cover-image" />
                            <div class="cover-gradient"></div>
                            <div class="meta-details">
                                <h1 class="lesson-name">{{store.course_content.title.rendered}}</h1>
                                <p v-if="store.course_content.title.rendered" class="lesson-created-by">Lesson Created By: <span class="lesson-created-by-name">{{author}}</span></p>
                            </div>
                            <div class="content-wrap">
                                <span class="lesson-end-text">Lesson end</span>
                                <span class="back-to-academe-btn" @click="requestLeaveSesson()">Back to AcadeMe+</span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Controls available only for teachers -->
                <div class="slide-nav" v-if="total_slides && current_slide_number">
                    <div v-if="user_role === 'teacher'" class="slide-nav-btn prev-slide" :class="{'not-active': !store.prev_slide}" @click="goToSlide(store.prev_slide)">
                        <svg width="20" height="29" viewBox="0 0 20 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.252687 14.6825L19.603 0.841474L19.603 28.5234L0.252687 14.6825Z" fill="#F8FCFF"/>
                        </svg>
                    </div>
                    <span class="slide-number">{{current_slide_number}}/{{total_slides}}</span>
                    <template v-if="user_role === 'teacher'">
                        <div v-if="current_slide_number < total_slides" class="slide-nav-btn next-slide" @click="goToSlide(store.next_slide)">
                            <svg width="20" height="29" viewBox="0 0 20 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7473 14.5285L0.396996 28.3695L0.396996 0.6875L19.7473 14.5285Z" fill="#F8FCFF"/>
                            </svg>
                        </div>
                        <div v-else class="slide-nav-btn next-slide" @click="endLesson()">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="25" height="25" x="0" y="0" viewBox="0 0 373.008 373.008" style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                                <path d="M25.307,2.588C28.295,0.864,31.627,0,34.963,0c3.338,0,6.663,0.864,9.655,2.588l200.384,167.2    c5.957,3.445,9.65,9.823,9.65,16.719c0,6.895-3.683,13.272-9.65,16.713L44.618,370.427c-5.969,3.441-13.333,3.441-19.306,0    c-5.973-3.453-9.655-9.833-9.655-16.724V19.305C15.658,12.413,19.335,6.036,25.307,2.588z M278.204,7.924v357.167    c0,4.263,3.46,7.722,7.723,7.722h63.697c4.268,0,7.727-3.459,7.727-7.722V7.924c0-4.269-3.459-7.727-7.727-7.727h-63.697    C281.664,0.197,278.204,3.655,278.204,7.924z" fill="#FFFFFF" data-original="#FFFFFF" style="" class=""></path>
                            </svg>
                        </div>
                    </template>
                </div>
                <div class="question-number" v-if="movieQuizShowing || store.slide_type === 'question'">Question {{questionsCompleted.length}} of {{total_questions}}</div>
                <div class="exit-lesson" @click="requestLeaveSesson()">
                    <svg width="54" height="54" viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_1071:6710)">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.5618 28.6875L48.9374 28.6875C49.8687 28.6875 50.6243 27.9315 50.6243 27C50.6243 26.0685 49.8687 25.3125 48.9374 25.3125L18.5618 25.3125C17.6305 25.3125 16.875 26.0685 16.875 27C16.875 27.9315 17.6305 28.6875 18.5618 28.6875Z" fill="#51ACFD"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M20.9487 27.0002L28.1931 34.2446C28.8521 34.9028 28.8521 35.9726 28.1931 36.6308C27.535 37.2897 26.4651 37.2897 25.807 36.6308L17.3695 28.1933C16.7105 27.5343 16.7105 26.4661 17.3695 25.8071L25.807 17.3696C26.4651 16.7107 27.535 16.7107 28.1931 17.3696C28.8521 18.0278 28.8521 19.0976 28.1931 19.7558L20.9487 27.0002Z" fill="#51ACFD"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M43.875 20.25L43.875 18.5625C43.875 12.0428 38.5822 6.75 32.0625 6.75L15.1875 6.75001C8.66784 6.75001 3.37499 12.0429 3.37499 18.5625L3.375 35.4375C3.375 41.9572 8.66784 47.25 15.1875 47.25L32.0625 47.25C38.5822 47.25 43.875 41.9572 43.875 35.4375L43.875 27C43.875 26.0685 43.119 25.3125 42.1875 25.3125C41.256 25.3125 40.5 26.0685 40.5 27L40.5 35.4375C40.5 40.0942 36.7192 43.875 32.0625 43.875L15.1875 43.875C10.5308 43.875 6.75 40.0942 6.75 35.4375L6.74999 18.5625C6.74999 13.9058 10.5308 10.125 15.1875 10.125L32.0625 10.125C36.7192 10.125 40.5 13.9058 40.5 18.5625L40.5 20.25C40.5 21.1815 41.256 21.9375 42.1875 21.9375C43.119 21.9375 43.875 21.1815 43.875 20.25Z" fill="#51ACFD"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_1071:6710">
                                <rect width="54" height="54" fill="white" transform="translate(54 54) rotate(180)"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>

            </div>
        </div>
    </main>
</template>

<script>
    import Template1 from "../LessonEditor/Shared/SlideTemplates/Template1";
    import VideoPlayer from "../LessonEditor/Shared/VideoPlayer";
    import axios from "axios";
    import moment from 'moment';

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
            student_movie_pc: String,
            session_fields: Object,
        },
        computed: {
            store() {
                return this.$store.state.LessonSlideshow;
            },
            predefined_template() {
              return this.store.slide_content.acf.template1;
            },
            total_slides() {
                if (this.course_steps) {
                    return this.course_steps.t["sfwd-lessons"].length;
                }
            },
            current_slide_number() {
                let _this = this;
                if (this.course_steps && this.store.slide_content) {
                    let slides = this.course_steps.t["sfwd-lessons"];
                    return slides.findIndex( slide => slide === _this.store.slide_content.id ) + 1;
                }
            },
            total_questions() {
                if (this.course_steps) {
                    return this.course_steps.t["sfwd-quiz"].length;
                }
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
                studentPlayerMuted: true,
                questionsCompleted: [],
                lesson_will_start_at: moment(this.session_fields.session_starts).format('HH:mm, D MMM'),
            }
        },
        mounted() {
            this.openFullscreen();

            jQuery('body').toggleClass('fullscreen-presentation');
            this.updateLessonViewsCount();

            // Keyboard arrows navigation:
            window.addEventListener("keyup", e => {
                if (e.key === "ArrowLeft") {
                    this.goToSlide(this.store.prev_slide);
                    return false;
                } else if (e.key === "ArrowRight") {
                    this.goToSlide(this.store.next_slide);
                    return false;
                }
            });

            /* SESSION WEBSOCKETS CODE */
            var _this =this;
            window.conn = new WebSocket(_this.websocket_url);

            window.conn.onopen = function(e) {
                console.log("Connection established!");
                _this.subscribe();
            };

            window.conn.onmessage = function(e) {
                var data = JSON.parse(e.data);
                if ('load_slide' in data.message) {
                    _this.movieQuizShowing = false;
                    _this.getSlideContent(data.message.load_slide);
                } else if ('load_quiz' in data.message) {
                    _this.getQuiz(data.message.load_quiz);
                    _this.movieQuizShowing = true;
                    _this.$refs.videoPlayer.playerCloseFullscreen();
                    _this.$refs.videoPlayer.playerPause();
                } else if ('hide_quiz' in data.message) {
                    _this.movieQuizShowing = false;
                    jQuery('.quiz-content-movie').html('');
                    //_this.$refs.videoPlayer.playerOpenFullscreen();
                    _this.$refs.videoPlayer.playerPlay();
                } else if ('player_event' in data.message) {
                    switch (data.message.player_event) {
                        case 'play':
                            _this.$refs.videoPlayer.playerPlay();
                            break;
                        case 'pause':
                            _this.$refs.videoPlayer.playerPause();
                            break;
                    }
                } else if ('player_seek' in data.message) {
                    _this.$refs.videoPlayer.playerDoSeek(parseInt(data.message.player_seek));
                } else if ('thank_you_screen' in data.message) {
                    _this.endLesson();
                } else if ('end_lesson' in data.message) {
                    window.location.href = '/';
                } else if ('teacher_online' in data.message) {
                    _this.store.teacher_online = Boolean(data.message.teacher_online);
                }

                //alert(data.message);
            };
            /* SESSION WEBSOCKETS CODE END */

            // Get course
            axios.get('/academe/v1/sfwd-courses/'+this.course_id).then(res => {
                this.store.course_content = res.data;

                axios.get('/academe/v1/get-lesson-meta-terms?lesson_id='+this.course_id).then(res => {
                    _this.store.course_meta = res.data
                });

            });

            // Get course slides
            axios.get('academe/v1/course-steps/'+this.course_id).then(res => {
            //axios.get('/ldlms/v2/sfwd-courses/'+this.course_id+'/steps').then(res => {
                console.log(res.data);
                this.course_steps = res.data;
                if (this.current_slide) {
                    this.getSlideContent(this.current_slide).then(res => {
                        this.calculateCompletedQuestions(); // based on course steps
                    });
                } else {
                    let slides = res.data.t["sfwd-lessons"];
                    this.getSlideContent(slides[0]);
                }
            });
        },
        methods: {
            lesson_started() {
                let lesson_starts = new Date(this.session_fields.session_starts);
                let lesson_timestamp = Math.floor(lesson_starts / 1000);
                let now_timestamp = Math.floor(Date.now() / 1000);

                return now_timestamp > lesson_timestamp;
            },
            openFullscreen() {
                setTimeout(() => {
                    let elem = document.documentElement;
                    elem.requestFullscreen({ navigationUI: "show" }).then(() => {}).catch(err => {
                        console.log(`An error occurred while trying to switch into full-screen mode: ${err.message} (${err.name})`);
                    });
                }, 1000);
            },
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
            async getSlideContent(slide_id) {
                if (!slide_id)
                    return;
                jQuery('.quiz-content').html('');
                jQuery('.quiz-content-movie').html('');
                // get current slide
                const url_base = this.user_role === 'teacher' ? '/wp/v2/sfwd-lessons/' : '/academe/v1/sfwd-lessons/';
                await axios.get(url_base + slide_id).then(res => {
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

                                if(this.user_role === 'teacher') {
                                    // Set first question (quiz) point as mediaPlayTo
                                    if (tempPlayerObject.play_to != 0) {
                                        tempPlayerObject.play_to = this.quizzes[0].time;
                                    }
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
                            if(this.questionsCompleted.indexOf(quiz_id) === -1) {
                                this.questionsCompleted.push(quiz_id); //add loaded quiz to list of completed quizzes
                            }
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
                if (this.user_role === 'teacher') {
                    // Player last time paused:
                    if (parseInt(this.store.slide_content.acf.movie_slide.play_to) === time) {
                        console.log("Player last time paused");
                        this.sendMessage({'player_event' : 'pause'})
                        return;
                    }

                    // Check if paused exactly on quiz time & load current quiz:
                    if ( parseInt(this.quizzes[this.nextQuizIndex].time) === time) { //
                        this.sendMessage({"load_quiz" : this.quizzes[this.nextQuizIndex].id});
                        this.getQuiz(this.quizzes[this.nextQuizIndex].id);
                        if(this.questionsCompleted.indexOf(this.quizzes[this.nextQuizIndex].id) === -1) {
                            this.questionsCompleted.push(this.quizzes[this.nextQuizIndex].id); //add loaded quiz to list of completed quizzes
                        }

                        this.$refs.videoPlayer.playerCloseFullscreen();

                        // Update next quiz
                        if (this.quizzes.length > this.nextQuizIndex+1) { // if next quiz exists (not last quiz already loaded)
                            this.nextQuizIndex++;
                            this.quizzes[this.nextQuizIndex].id;
                            //this.$refs.videoPlayer.nextSegment(this.quizzes[this.nextQuizIndex].time)
                        } else { // last question
                            this.lastQuestionLoaded = true;
                        }
                    } else {
                        this.sendMessage({'player_event' : 'pause'})
                    }
                }
            },
            checkQuestionAfterSeek(seeked_time) {
                let _this = this;
                const quizzes_length = this.quizzes.length;
                let next_index = quizzes_length - 1;

                let i = 0;
                while (i < quizzes_length) {
                    if (this.quizzes[i].time > seeked_time) {
                        next_index = i;
                        break;
                    }
                    i++;
                }
                this.nextQuizIndex = next_index;

                let next_stop = (seeked_time >= this.quizzes[quizzes_length-1].time)
                    ? this.store.slide_content.acf.movie_slide.play_to
                    : this.quizzes[this.nextQuizIndex].time;

                this.$refs.videoPlayer.nextSegment(next_stop, seeked_time);
                if (this.user_role === 'teacher') {
                    this.sendMessage({"player_seek": seeked_time}); // seek for student too
                }
                this.quizzes.forEach((quiz, index) => {
                    if (quiz.time > seeked_time) {
                        next_index = index;
                    }
                });
            },
            loadNextSegment() {
                this.movieQuizShowing = false; // hide overlap & control buttons
                let next_stop = this.lastQuestionLoaded
                    ? this.store.slide_content.acf.movie_slide.play_to
                    : this.quizzes[this.nextQuizIndex].time;
                this.$refs.videoPlayer.nextSegment(next_stop); // change next stop in player
                //this.$refs.videoPlayer.playerOpenFullscreen(); // go back to full screen view
                jQuery('.quiz-content-movie').html(''); // remove completed quiz content

                this.sendMessage({"hide_quiz" : true}); // close previous completed quiz for students
            },
            questionPosition(show_at) {
                return parseInt(show_at) / this.store.active_movie_duration * 100;
            },
            selectedWidth() {
                const play_from = parseInt(this.store.slide_content.acf.movie_slide.play_from);
                const play_to = parseInt(this.store.slide_content.acf.movie_slide.play_to)
                    ? parseInt(this.store.slide_content.acf.movie_slide.play_to)
                    : parseInt(this.store.active_movie_duration);
                return this.questionPosition(play_to - play_from);
            },
            setMovieDuration(duration) {
                this.store.active_movie_duration = duration;
            },
            playerMuteUnmute() {
                if (this.studentPlayerMuted) {
                    this.$refs.videoPlayer.playerUnmute();
                } else {
                    this.$refs.videoPlayer.playerMute();
                }
                this.studentPlayerMuted = !this.studentPlayerMuted;
            },
            updateLessonViewsCount() {
                var request = {
                    action: 'pvc-check-post',
                    pvc_nonce: pvcArgsFrontend.nonce,
                    id: this.course_id,
                };
                jQuery.ajax( {
                    url: pvcArgsFrontend.requestURL,
                    type: 'post',
                    async: true,
                    cache: false,
                    data: request
                } ).done( function( response ) {
                    // trigger pvcCheckPost event
                    jQuery.event.trigger( {
                        type: 'pvcCheckPost',
                        detail: response
                    } );
                } );
            },
            requestLeaveSesson() {
                if (this.user_role === 'teacher') {
                    this.sendMessage({"end_lesson": true}); // end for students too
                }
                window.location.href = '/';
            },
            calculateCompletedQuestions() {
                let _this = this;
                let current_achieved = false;
                this.course_steps.t['sfwd-lessons'].forEach((slide, slide_index) => {
                    if (parseInt(slide) === _this.store.slide_content.id) {
                        current_achieved = true;
                    }
                    if (!current_achieved) {
                        if (Object.keys(_this.course_steps.h['sfwd-lessons'][slide]['sfwd-quiz'])) {
                            Object.keys(_this.course_steps.h['sfwd-lessons'][slide]['sfwd-quiz']).forEach((quiz, quiz_index) => {
                                _this.$set( _this.questionsCompleted,  _this.questionsCompleted.length, parseInt(quiz))
                                //_this.questionsCompleted.push(parseInt(quiz));
                            });
                        }
                    }
                });
            },
            endLesson() {
                this.playerObject = null;
                this.movieQuizShowing = false;
                this.lastQuestionLoaded = false;
                this.store.active_movie_duration = null;
                this.store.slide_content = null;
                this.store.slide_type = 'end';
                jQuery('body').toggleClass('fullscreen-presentation');

                if (this.user_role === 'teacher') {
                    this.sendMessage({"thank_you_screen": true});
                }
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
        /*padding: 40px 80px;*/
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }
    .slide-nav {
        position: absolute;
        top: 30px;
        right: 50px;
        display: flex;
        align-items: center;
    }
    .slide-nav-btn {
        height: 55px;
        width: 55px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #51acfd;
        border-radius: 18px;
        cursor: pointer;
        user-select: none;
        transition: .2s;
    }
    .slide-nav-btn.not-active {
        opacity: .6;
        cursor: default;
    }
    .slide-nav-btn:not(.not-active):hover {
        transform: scale(1.05);
    }
    .slide-nav-btn.prev-slide {
        margin-right: 15px;
    }
    .slide-nav-btn.next-slide {
        margin-left: 15px;
    }
    .slide-nav-btn.prev-slide svg {
        margin-right: 4px;
    }
    .slide-nav-btn.next-slide svg {
        margin-left: 4px;
    }
    .slide-nav .slide-number {
        display: block;
        font-size: 34px;
        line-height: 41px;
        font-weight: 500;
        text-shadow: 0px 3px 6px rgba(0, 0, 0, 0.5);
    }
    .question-number {
        position: absolute;
        top: 47px;
        left: 40%;
        font-size: 36px;
        font-weight: 600;
    }
    .exit-lesson {
        position: absolute;
        top: 30px;
        left: 50px;
        cursor: pointer;
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
        /*width: 100%;*/
        /*height: 100%;*/
        display: flex;
        align-items: center;
        justify-content: center;
        height: 1080px;
        width: 1920px;
        max-width: 100%;
        max-height: 100%;
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
        /*background: rgba(196, 196, 196, 0.64);*/
    }
    .flex-builder .col.m-s {
        margin: 1.5%;
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
    .selected-timeline {
        width: 100%;
        height: 8px;
        background: #8e8e8e;
        display: flex;
        justify-content: flex-start;
    }
    .selected-timeline .selected-part {
        height: 100%;
        background: #f8da00;
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
    .cover-gradient {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0) 100%);
    }
    .slide-meta-preview {
        position: relative;
        height: 100%;
        width: 100%;
    }
    .slide-meta-preview .content-wrap {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    .slide-meta-preview .content-wrap .lesson-end-text {
        font-size: 96px;
        font-weight: 700;
        line-height: 117px;
        text-transform: uppercase;
        text-shadow: 0px 1px 4px rgba(0, 0, 0, 0.3);
        margin: 100px 0;
    }
    .slide-meta-preview .content-wrap .back-to-academe-btn {
        font-weight: 600;
        font-size: 27px;
        line-height: 27px;
        display: block;
        background: #51ACFD;
        border-radius: 18px;
        padding: 15px 20px;
        cursor: pointer;
    }
    .meta-details {
        position: absolute;
        bottom: 0;
        left: 0;
        padding-left: 40px;
        padding-bottom: 60px;
        width: 100%;
    }
    .meta-details * {
        text-shadow: 0px 0px 15px black;
    }
    .lesson-name {
        font-size: 39px;
        line-height: 39px;
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
    .lesson-terms.movies-list {
        margin-top: 20px;
    }
    .lesson-terms.tags-list, .lesson-terms.movies-list {
        color: #51ACFD;
        display: flex;
        align-items: center;
    }
    .lesson-terms.tags-list .tags-icon {
        font-size: 20px;
        color: #FFFFFF;
        margin-right: 8px;
    }
    .lesson-terms.movies-list .movies-icon {
        margin-right: 5px;
    }
    .slide-content .slide-message img {
        max-width: 100%;
        height: 250px;
        margin-top: 20px;
    }
    .mute-unmute-btn {
        position: absolute;
        top: 37px;
        left: 120px;
        border-radius: 3px;
        cursor: pointer;
    }
    h1.lesson-title {
        display: none;
    }
    .formatted-text {
        width: 100%;
        max-height: 100%;
        overflow: hidden;
        white-space: pre-line;
        word-break: break-word;
    }
    .waiting-screen {
        text-align: center;
        padding: 20px 40px;
    }
    .waiting-screen .message {
        font-weight: 600;
        font-size: 80px;
        line-height: 98px;
        letter-spacing: 0.175px;
        text-transform: uppercase;
        color: #FFFFFF;
        text-shadow: 0px 1px 4px rgba(0, 0, 0, 0.3);
    }
    @media screen and (max-width: 767px) {
        .slide-content {
            /*padding: 30px;*/
            /*padding-top: 50px;*/
        }
        .slide-content .slide-message {
            font-size: 24px;
            line-height: 28px;
            padding: 40px 0;
        }
        .slide-content .slide-message img {
            max-width: 100%;
            height: 120px;
            margin-top: 20px;
        }
        h1.lesson-title {
            position: absolute;
            display: block;
            top: 20px;
            padding: 0 30px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 500;
        }
        .meta-details {
            position: relative;
            padding-left: 0;
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .aspect-ratio-box {
            height: 100%;
            aspect-ratio: auto;
            background: rgba(0,0,0,0);
            overflow-y: auto;
            padding: 20px;
        }
        .aspect-ratio-box-inside {
            display: block;
            height: auto;
            margin: 40px 0;
        }
        .cover-gradient {
            display: none;
        }
        .meta-details .lesson-name {
            font-size: 18px;
        }
        .meta-details .lesson-created-by,
        .meta-details .lesson-created-by-name,
        .meta-details .lesson-description,
        .meta-details  .lesson-terms {
            font-size: 16px;
        }
        .flex-builder .row {
            flex-wrap: wrap;
        }
        .flex-builder .row .col {
            width: 100%;
            min-width: 100%;
            flex: 1 1 100%;
            margin-left: 0;
            margin-right: 0;
        }
        .aspect-ratio-box .slide-template-preview {
            padding: 0;
            height: unset;
        }
        .flex-builder .h-30, .flex-builder .h-40, .flex-builder .h-50, .flex-builder .h-70 {
            height: auto;
        }
        .slide-nav {
            top: 20px;
            right: 20px;
        }
        .exit-lesson {
            top: 15px;
            left: 5px;
        }
        .slide-nav .slide-number {
            font-size: 18px;
            line-height: 18px;
        }
        .exit-lesson svg {
            height: 30px;
        }
        .meta-details * {
            text-shadow: none;
        }
        .question-number {
            top: 20px;
            font-size: 16px;
            left: 35%;
        }
        .slide-meta-preview .content-wrap {
            position: relative;
        }
        .slide-meta-preview .content-wrap .lesson-end-text {
            margin: 0;
            margin-bottom: 40px;
            font-size: 34px;
            line-height: 38px;
        }
        .slide-meta-preview .content-wrap .back-to-academe-btn {
            font-size: 16px;
            line-height: 16px;
        }
    }
</style>

<style>
    input[type="radio"].wpProQuiz_questionInput {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        /*width: 9999px !important;*/
        /*max-width: 9999px !important;*/
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
        box-sizing: border-box;
        border-radius: 18px;
        font-size: 27px;
        font-weight: 600;
        margin: 3px;
        padding: 10px 20px;
    }
    .wpProQuiz_content .wpProQuiz_questionListItem {
        padding: 3px 0 !important;
    }
    .wpProQuiz_content .wpProQuiz_questionListItem, .wpProQuiz_content .wpProQuiz_questionListItem input[type='radio'] {
        cursor: pointer;
    }
    .wpProQuiz_content .wpProQuiz_question_text p {
        font-size: 36px;
        font-weight: 600;
        line-height: 150%;
        color: #F0F0F0;
    }
    .quiz-content .learndash-wrapper textarea,
    .quiz-content-movie .learndash-wrapper textarea {
        font-size: 36px;
        font-weight: 600;
        line-height: 100%;
        color: #F0F0F0;
        max-height: 250px;
    }
    .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
        position: relative;
        overflow: hidden;
        border: none;
        padding: 0;
        padding-left: 40px;
    }
    .learndash-wrapper .quiz_continue_link {
        display: none !important;
    }
    .learndash-wrapper .wpProQuiz_graded_points, .learndash-wrapper .wpProQuiz_points {
        background: rgba(0,0,0,0);
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
        background: rgba(0, 0, 0, .6);
    }
    .quiz-content-movie.with-content > div.learndash {
        width: 80%;
        padding: 40px;
        /*background: #131212;*/
        max-height: 80%;
        overflow-y: scroll;
        margin-bottom: 80px;
    }
    .slide-content.teacher input[name="next"].wpProQuiz_QuestionButton {
        display: none !important;
    }
    .slide-content .quiz-content {
        width: 100%;
    }
    .slide-content.teacher .quiz-content > div.learndash {
        /*padding: 40px 10%;*/
        /*background: #2F2F2F;*/
    }
    .quiz-content-movie.with-content > div.learndash::-webkit-scrollbar-thumb,
    .learndash-wrapper textarea::-webkit-scrollbar-thumb {
        border: 5px solid rgba(0, 0, 0, 0);
        background-clip: padding-box;
        -webkit-border-radius: 7px;
        height: 30px;
    }
    .quiz-content-movie.with-content > div.learndash::-webkit-scrollbar,
    .learndash-wrapper textarea::-webkit-scrollbar {
        width: 14px;
        height: 18px;
    }
    .quiz-content-movie.with-content > div.learndash::-webkit-scrollbar-thumb,
    .learndash-wrapper textarea::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
    }
    .quiz-content .learndash-wrapper textarea,
    .quiz-content-movie .learndash-wrapper textarea {
        background: rgba(0, 104, 178, 0.9);
        border: none !important;
        border-radius: 12px;
    }
    .quiz-content .learndash-wrapper textarea::placeholder,
    .quiz-content-movie .learndash-wrapper textarea::placeholder {
        color: #c9c9c9;
        font-size: 36px;
    }
    .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
        font-weight: 600;
        font-size: 36px;
        line-height: 150%;
    }
    .slide-content .learndash-wrapper .wpProQuiz_content [data-type="single"].wpProQuiz_questionList {
        background: rgba(0, 104, 178, 0.9);
        border-radius: 12px;
        padding: 60px !important;
    }
    .learndash-wrapper .wpProQuiz_content {
        max-width: 80%;
        width: 700px;
        margin: auto;
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
        background: #51ACFD;
        border-radius: 18px;
        padding: 15px;
        display: flex;
        align-items: center;
        cursor: pointer;
        transition: .2s;
        color: #FFFFFF;
        font-weight: 600;
        font-size: 27px;
    }
    #slide-movie:hover .questions-timeline {
        opacity: 1;
    }
    body.fullscreen-presentation header,
    body.fullscreen-presentation .header-placeholder {
        display:none;
    }
    body.fullscreen-presentation #app.main {
        height: 100%;
    }
    .slide-content.teacher .player-full #kalturaPlayer {
        height: calc(100% - 8px);
    }
    .learndash-wrapper .wpProQuiz_content .graded-disclaimer {
        font-size: 18px;
    }
    .learndash-wrapper .wpProQuiz_quiz_time {
        color: #FFFFFF;
    }
    .slide-content .learndash-wrapper .ld-alert-warning {
        border: none;
        background: rgba(0, 104, 178, 0.9);
    }
    .slide-content .learndash-wrapper .ld-alert-messages {
        color: #FFFFFF;
    }
    .learndash-wrapper .wpProQuiz_content ul.wpProQuiz_questionList input.wpProQuiz_questionInput {
        min-width: 25px;
    }
    @media screen and (max-width: 767px) {
        .quiz-content-movie {
            width: 100%;
        }
        .quiz-content-movie.with-content > div.learndash {
            max-height: none;
            margin-bottom: 0;
        }
        .learndash-wrapper input[name="next"].wpProQuiz_QuestionButton {
            border-radius: 5px;
            font-size: 16px;
        }
        .wpProQuiz_content .wpProQuiz_question_text p {
            font-size: 16px;
        }
        .quiz-content .learndash-wrapper textarea,
        .quiz-content-movie .learndash-wrapper textarea {
            font-size: 16px;
        }
        .quiz-content .learndash-wrapper textarea::placeholder,
        .quiz-content-movie .learndash-wrapper textarea::placeholder {
            color: #c9c9c9;
            font-size: 16px;
        }
        .quiz-content-movie .learndash-wrapper, aspect-ratio-box .slide-template-preview {
            padding-bottom: 40px;
        }
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
            font-size: 16px;
        }
        .slide-content .aspect-ratio-box .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionList {
            padding: 20px !important;
        }
        .learndash-wrapper .wpProQuiz_content {
            max-width: 100%;
        }
        .learndash-wrapper .wpProQuiz_content .graded-disclaimer {
            font-size: 16px;
        }
        .learndash-wrapper .wpProQuiz_content ul.wpProQuiz_questionList input.wpProQuiz_questionInput {
            min-width: 15px;
        }
        .learndash-wrapper .wpProQuiz_content .wpProQuiz_questionListItem label {
            padding-left: 30px;
        }
    }
</style>