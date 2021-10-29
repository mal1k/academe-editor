import axios from "axios";
import store from './vuex-store';
import helper from './helper';
import service from "./service";

axios.defaults.baseURL = window.wpApiSettings.root;
axios.defaults.headers.common["X-WP-Nonce"] = window.wpApiSettings.nonce;

export default {
    async initLoad () {
        let _this = this;
        let storage = store.state.LessonEditor;
        console.log('lesson load initialized');
        storage.loading = true;

        // We know if we loading saved lesson, first session already created:
        storage.first_session_created = true;

        // Load course data (for meta filling):
        axios.get('/ldlms/v1/sfwd-courses/'+storage.lesson_id).then(res => {
            const course_content = res.data;
            console.log("Course content:");
            console.log(course_content);

            if (course_content.status === 'auto-draft') {
                let params = new URLSearchParams(location.search);
                params.delete('lesson_id');
                window.location.href = `${location.pathname}?${params}`;
            } else {
                storage.movie_id = course_content.acf.movie_id ? course_content.acf.movie_id : null;
                storage.status = course_content.status;
                if (storage.status === 'private') {
                    storage.private = true;
                }
                storage.meta.accept = true;

                storage.meta.title = course_content.title.rendered;
                storage.meta.description = jQuery(course_content.content.rendered).text(); // strip html tags
                storage.meta.thumbnail = course_content.acf.cover_image_url;

                storage.meta.subjects = course_content.subject;
                storage.meta.topics = course_content.topic;
                storage.meta.grades = course_content.grade;
                storage.meta.tags = course_content.ptag;

                // Load tags names (to not show only ID):
                axios.get('/wp/v2/ptag?per_page=100&orderby=count&order=desc&include=' + storage.meta.tags.join(',') + '&_wpnonce=' + wpApiSettings.nonce).then(res => {
                    storage.meta_select_options.tags = res.data.map(term => ({
                        id: term.id,
                        name: term.name,
                        slug: term.slug,
                    }));
                });

                // Load course steps (lesson/slides, quizzes):
                axios.get('academe/v1/course-steps/'+storage.lesson_id).then(res => {
                //axios.get('/ldlms/v2/sfwd-courses/'+storage.lesson_id+'/steps').then(res => {
                    storage.course_steps = res.data;
                    console.log("Course steps:");
                    console.log(storage.course_steps);

                    let slides = storage.course_steps.t["sfwd-lessons"];

                    // Load all slides:
                    _this.loadSlides(slides).then(()=> {
                        storage.loading = false;
                    });

                });

                // Load preview link:
                axios.get('academe/v1/get-lesson-last-session-url?lesson='+storage.lesson_id).then(res => {
                    const element = jQuery("#previewButton");
                    element.attr("href", res.data);
                });
            }


        });



    },
    async loadSlides(slides) {
        // More efficient method (need to solve the problem with slides order before implementing):
        // await Promise.all(slides.map(async (slide) => {
        //     await this.loadSlide(slide);
        // }));
        for (const slide of slides) {
            await this.loadSlide(slide);
        }
    },
    async loadSlide(slide_id) {
        let storage = store.state.LessonEditor;
        await axios.get('/wp/v2/sfwd-lessons/'+slide_id).then(async res => {

            const slide_content = res.data;
            const slide_type = res.data.acf.slide_type;
            console.log("Slide content:");
            console.log(slide_content);

            let loaded_slide = {
                lesson_id: slide_id,
                slide_type: slide_content.acf.slide_type,
            };

            switch (slide_content.acf.slide_type) {
                case "meta":
                    loaded_slide.template = 'template0';
                    break;
                case "text_image":

                    //if (slide_content.acf.pre_defined_template === 'template1') {
                        loaded_slide.fields = {
                            template1_text1_fill_color: slide_content.acf.template1.template1_text1.template1_text1_fill_color ?? null,
                            template1_text1_font: slide_content.acf.template1.template1_text1.template1_text1_font ?? null,
                            template1_text1_font_size: slide_content.acf.template1.template1_text1.template1_text1_font_size ?? null,
                            template1_text1_font_weight: slide_content.acf.template1.template1_text1.template1_text1_font_weight ?? null,
                            template1_text1_text: slide_content.acf.template1.template1_text1.template1_text1_text ?? null,
                            template1_text1_text_align: slide_content.acf.template1.template1_text1.template1_text1_text_align ?? null,
                            template1_text1_text_color: slide_content.acf.template1.template1_text1.template1_text1_text_color ?? null,

                            template1_text2_fill_color: slide_content.acf.template1.template1_text2.template1_text2_fill_color ?? null,
                            template1_text2_font: slide_content.acf.template1.template1_text2.template1_text2_font ?? null,
                            template1_text2_font_size: slide_content.acf.template1.template1_text2.template1_text2_font_size ?? null,
                            template1_text2_font_weight: slide_content.acf.template1.template1_text2.template1_text2_font_weight ?? null,
                            template1_text2_text: slide_content.acf.template1.template1_text2.template1_text2_text ?? null,
                            template1_text2_text_align: slide_content.acf.template1.template1_text2.template1_text2_text_align ?? null,
                            template1_text2_text_color: slide_content.acf.template1.template1_text2.template1_text2_text_color ?? null,

                            template1_text3_fill_color: slide_content.acf.template1.template1_text3.template1_text3_fill_color ?? null,
                            template1_text3_font: slide_content.acf.template1.template1_text3.template1_text3_font ?? null,
                            template1_text3_font_size: slide_content.acf.template1.template1_text3.template1_text3_font_size ?? null,
                            template1_text3_font_weight: slide_content.acf.template1.template1_text3.template1_text3_font_weight ?? null,
                            template1_text3_text: slide_content.acf.template1.template1_text3.template1_text3_text ?? null,
                            template1_text3_text_align: slide_content.acf.template1.template1_text3.template1_text3_text_align ?? null,
                            template1_text3_text_color: slide_content.acf.template1.template1_text3.template1_text3_text_color ?? null,

                            template1_text4_fill_color: slide_content.acf.template1.template1_text4.template1_text4_fill_color ?? null,
                            template1_text4_font: slide_content.acf.template1.template1_text4.template1_text4_font ?? null,
                            template1_text4_font_size: slide_content.acf.template1.template1_text4.template1_text4_font_size ?? null,
                            template1_text4_font_weight: slide_content.acf.template1.template1_text4.template1_text4_font_weight ?? null,
                            template1_text4_text: slide_content.acf.template1.template1_text4.template1_text4_text ?? null,
                            template1_text4_text_align: slide_content.acf.template1.template1_text4.template1_text4_text_align ?? null,
                            template1_text4_text_color: slide_content.acf.template1.template1_text4.template1_text4_text_color ?? null,

                            template1_media1_image: slide_content.acf.template1.template1_media1.template1_media1_image ?? null,
                            template1_media2_image: slide_content.acf.template1.template1_media2.template1_media2_image ?? null,
                            template1_media3_image: slide_content.acf.template1.template1_media3.template1_media3_image ?? null,
                            template1_media4_image: slide_content.acf.template1.template1_media4.template1_media4_image ?? null,
                        };
                        loaded_slide.template = slide_content.acf.pre_defined_template;
                    //}

                    break;
                case "movie":
                    loaded_slide.fields = {
                        kaltura_id: slide_content.acf.movie_slide.kaltura_id,
                        play_from: helper.secondsToTimeString(slide_content.acf.movie_slide.play_from),
                        play_to: helper.secondsToTimeString(slide_content.acf.movie_slide.play_to),
                        post_type: (slide_content.acf.movie_slide.post_type !== "") ? slide_content.acf.movie_slide.post_type : 'movie',
                    };
                    loaded_slide.template = 'template4';
                    loaded_slide.movie_meta = await service.getMovieMetaByKalturaId(slide_content.acf.movie_slide.kaltura_id);
                    loaded_slide.movie_meta.post_type = (slide_content.acf.movie_slide.post_type !== "") ? slide_content.acf.movie_slide.post_type : 'movie';

                    // Load quizzes:
                    const quizzes_list = Object.keys(storage.course_steps.h['sfwd-lessons'][slide_id]['sfwd-quiz']);
                    if ( quizzes_list.length ) {
                        quizzes_list.forEach((quiz) => {
                            axios.get('/wp/v2/sfwd-quiz/' + quiz).then(quiz_response => {
                                console.log(quiz_response.data);

                                axios.get('/ldlms/v1/sfwd-questions/' + Object.keys(quiz_response.data.questions)[0]).then(question_response => {
                                    console.log(question_response.data);

                                    let answers = [];
                                    let score = question_response.data._points;
                                    let correct_index = 0;
                                    if (question_response.data._answerType === "single") {
                                        question_response.data._answerData.forEach((answer) => {

                                            // add question answer to array of answers:
                                            answers.push({
                                                text: answer._answer,
                                                value: answer._answer,
                                            });

                                            // set question points from correct answer:
                                            if (answer._correct) {
                                                correct_index = answers.length - 1;
                                            }

                                        });
                                    }



                                    let loaded_question = {
                                        kaltura_id: slide_content.acf.movie_slide.kaltura_id,
                                        idSlide: slide_id,
                                        quiz_id: quiz_response.data.id,
                                        question_id: Object.keys(quiz_response.data.questions)[0],
                                        questionIndex: 0,
                                        start_time: helper.secondsToTimeString(quiz_response.data.acf.show_at),
                                        type: this.convertQuestionTypeName(question_response.data._answerType),
                                        score: score,
                                        limit: '',
                                        answers: answers,
                                        checkedItems: [],
                                        correctAnswerIndex: correct_index,
                                        addQuestion: true,
                                        startTime: 0,
                                        place: 'movie',
                                        start_time_error: false,
                                        description: question_response.data._question,
                                    };

                                    storage.questions.push(loaded_question);
                                });
                            });

                        });
                    }

                    break;
                case "question":

                    loaded_slide.template = 'template5';

                    // Load quiz:
                    const slide_quiz = Object.keys(storage.course_steps.h['sfwd-lessons'][slide_id]['sfwd-quiz'])[0];

                    axios.get('/wp/v2/sfwd-quiz/' + slide_quiz).then(quiz_response => {
                        console.log(quiz_response.data);

                        axios.get('/ldlms/v1/sfwd-questions/' + Object.keys(quiz_response.data.questions)[0]).then(question_response => {
                            console.log(question_response.data);

                            let answers = [];
                            let score = question_response.data._points;
                            let correct_index = 0;
                            if (question_response.data._answerType === "single") {
                                question_response.data._answerData.forEach((answer) => {

                                    // add question answer to array of answers:
                                    answers.push({
                                        text: answer._answer,
                                        value: answer._answer,
                                    });

                                    // set question points from correct answer:
                                    if (answer._correct) {
                                        correct_index = answers.length - 1;
                                    }

                                });
                            }

                            let loaded_question = {
                                idSlide: slide_id,
                                quiz_id: quiz_response.data.id,
                                question_id: Object.keys(quiz_response.data.questions)[0],
                                questionIndex: 0,
                                type: this.convertQuestionTypeName(question_response.data._answerType),
                                score: score,
                                answers: answers,
                                checkedItems: [],
                                correctAnswerIndex: correct_index,
                                addQuestion: true,
                                startTime: 0,
                                place: 'slide',
                                description: question_response.data._question,
                            };

                            storage.questions.push(loaded_question);
                        });
                    });

                    break;
            }

            storage.slides.push(loaded_slide);

        });
    },
    convertQuestionTypeName(ld_name) {
        switch (ld_name) {
            case "single":
                return 'Single Choice';
                break;
            case "essay":
                return 'Open';
                break;
        }
    },
};
