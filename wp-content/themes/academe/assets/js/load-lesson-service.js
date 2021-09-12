import axios from "axios";
import store from './vuex-store';

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
                axios.get('/ldlms/v2/sfwd-courses/'+storage.lesson_id+'/steps').then(res => {
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
        for (const slide of slides) {
            await this.loadSlide(slide);
        }
    },
    async loadSlide(slide_id) {
        let storage = store.state.LessonEditor;
        await axios.get('/ldlms/v1/sfwd-lessons/'+slide_id).then(res => {

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

                    if (slide_content.acf.pre_defined_template === 'template1') {
                        loaded_slide.fields = {
                            template1_text1_fill_color: slide_content.acf.template1.template1_text1.template1_text1_fill_color,
                            template1_text1_font: slide_content.acf.template1.template1_text1.template1_text1_font,
                            template1_text1_font_size: slide_content.acf.template1.template1_text1.template1_text1_font_size,
                            template1_text1_font_weight: slide_content.acf.template1.template1_text1.template1_text1_font_weight,
                            template1_text1_text: slide_content.acf.template1.template1_text1.template1_text1_text,
                            template1_text1_text_align: slide_content.acf.template1.template1_text1.template1_text1_text_align,
                            template1_text1_text_color: slide_content.acf.template1.template1_text1.template1_text1_text_color,
                            template1_text2_fill_color: slide_content.acf.template1.template1_text2.template1_text2_fill_color,
                            template1_text2_font: slide_content.acf.template1.template1_text2.template1_text2_font,
                            template1_text2_font_size: slide_content.acf.template1.template1_text2.template1_text2_font_size,
                            template1_text2_font_weight: slide_content.acf.template1.template1_text2.template1_text2_font_weight,
                            template1_text2_text: slide_content.acf.template1.template1_text2.template1_text2_text,
                            template1_text2_text_align: slide_content.acf.template1.template1_text2.template1_text2_text_align,
                            template1_text2_text_color: slide_content.acf.template1.template1_text2.template1_text2_text_color,
                            template1_media1_image: slide_content.acf.template1.template1_media1.template1_media1_image
                        };
                        loaded_slide.template = 'template1';
                    }

                    break;
                case "movie":
                    loaded_slide.fields = {
                        kaltura_id: slide_content.acf.movie_slide.kaltura_id,
                        play_from: slide_content.acf.movie_slide.play_from,
                        play_to: slide_content.acf.movie_slide.play_to,
                    };
                    loaded_slide.template = 'template4';

                    // Load quizzes:
                    const quizzes_list = Object.keys(storage.course_steps.h['sfwd-lessons'][slide_id]['sfwd-quiz']);
                    if ( quizzes_list.length ) {
                        quizzes_list.forEach((quiz) => {
                            axios.get('/ldlms/v1/sfwd-quiz/' + quiz).then(quiz_response => {
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
                                        start_time: quiz_response.data.acf.show_at,
                                        type: this.convertQuestionTypeName(question_response.data._answerType),
                                        score: score,
                                        limit: '',
                                        answers: answers,
                                        checkedItems: [],
                                        correctAnswerIndex: correct_index,
                                        addQuestion: true,
                                        startTime: 0,
                                        place: 'movie',
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

                    axios.get('/ldlms/v1/sfwd-quiz/' + slide_quiz).then(quiz_response => {
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
