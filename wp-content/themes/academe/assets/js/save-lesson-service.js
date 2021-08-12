import axios from "axios";
import store from './vuex-store';

axios.defaults.baseURL = window.wpApiSettings.root;
axios.defaults.headers.common["X-WP-Nonce"] = window.wpApiSettings.nonce;

export default {
    initSave() {

        alert('abc');

        let storage = store.state.LessonEditor;

        console.log('lesson save initialized');
        console.log(store.state.LessonEditor);

        const saveCourse = this.saveCourseMeta();
        // Update ACF fields:
        saveCourse.then(function (results) {
            axios.post("/acf/v3/sfwd-courses/"+storage.lesson_id, {
                fields: {
                    featured_image_url: storage.meta.thumbnail,
                }
            });
        });

        if(storage.slides.length) {
            storage.slides.forEach((slide) => {
                this.saveSlide(slide);
            });
        }


    },
    async saveCourseMeta() {
        let storage = store.state.LessonEditor;

        try {
            const response = await axios.post("/ldlms/v1/sfwd-courses/"+storage.lesson_id, {
                course_disable_lesson_progression: true,
                title: storage.meta.title,
                content: storage.meta.description,

                faculty: storage.meta.faculties,
                grade: storage.meta.grades,
                subject: storage.meta.subjects,
                topic: storage.meta.topics,
                ld_course_tag: storage.meta.tags,

                status: 'publish',

            });
            return response.data;
        } catch (err) {
            console.error(`Failed to save course`, err);
        }
    },
    saveSlide(slide) {
        let storage = store.state.LessonEditor;

        var acf_fields = {
            slide_type: slide.slide_type,
        };

        switch (slide.slide_type) {
            case "text_image":

                acf_fields.pre_defined_template = slide.template;

                if (slide.template === 'template1') {
                    acf_fields.template1 = {
                        template1_text1: {
                            template1_text1_fill_color: slide.fields.template1_text1_fill_color,
                            template1_text1_font: slide.fields.template1_text1_font,
                            template1_text1_font_size: slide.fields.template1_text1_font_size,
                            template1_text1_font_weight: slide.fields.template1_text1_font_weight,
                            template1_text1_text: slide.fields.template1_text1_text,
                            template1_text1_text_align: slide.fields.template1_text1_text_align,
                            template1_text1_text_color: slide.fields.template1_text1_text_color,
                        },
                        template1_text2: {
                            template1_text2_fill_color: slide.fields.template1_text2_fill_color,
                            template1_text2_font: slide.fields.template1_text2_font,
                            template1_text2_font_size: slide.fields.template1_text2_font_size,
                            template1_text2_font_weight: slide.fields.template1_text2_font_weight,
                            template1_text2_text: slide.fields.template1_text2_text,
                            template1_text2_text_align: slide.fields.template1_text2_text_align,
                            template1_text2_text_color: slide.fields.template1_text2_text_color,

                        },
                        template1_media1: {
                            template1_media1_image: slide.fields.template1_media1_image,
                        }
                    };
                }

                axios.post("/acf/v3/sfwd-lessons/"+slide.lesson_id, {
                    fields: acf_fields
                });

                // for (const [key, value] of Object.entries(slide.fields)) {
                //     acf_fields['pre_defined_template_0_'+key] = value;
                // }

                break;
            case "movie":
                acf_fields.movie_slide = {
                    kaltura_id: slide.fields.kaltura_id,
                    play_from: slide.fields.play_from,
                    play_to: slide.fields.play_from,
                };

                axios.post("/acf/v3/sfwd-lessons/"+slide.lesson_id, {
                    fields: acf_fields
                }).then( response => {
                    // If slide has at least one question:
                    // const current_slide_questions = storage.questions.filter(question => question.idSlide === slide.lesson_id);
                    // if (current_slide_questions.length) {
                    //
                    //     current_slide_questions.forEach((question) => {
                    //
                    //     });
                    //
                    //     console.log('slide:'+slide.lesson_id);
                    //     console.log('questions:'+JSON.stringify(current_slide_questions));
                    // }
                });

                break;
            case "question":
                const current_slide_questions = storage.questions.filter(question => question.idSlide === slide.lesson_id);
                const newQuestion = current_slide_questions[0];
                console.log(current_slide_questions);
                console.log(newQuestion);

                // Prepare question data to save:
                var answer_data = [];
                var answer_type = "";
                switch (newQuestion.type) {
                    case "Single Choice":
                        answer_type = 'single';
                        newQuestion.answers.forEach((answer, index) => {
                            let answer_params = {
                                _answer: answer.text,
                                _correct: index === newQuestion.correctAnswerIndex,
                                _graded: "1",
                                _gradedType: "text",
                                _gradingProgression: "not-graded-none",
                                _html: false,
                                _points: (index === newQuestion.correctAnswerIndex) ? parseInt(newQuestion.score) : 0,
                                _sortString: "",
                                _sortStringHtml: false,
                                _type: "answer",
                            };
                            answer_data.push(answer_params);
                        });
                        break;
                    case "Open":
                        answer_type = 'essay';
                        let answer_params = {
                            _answer: "",
                            _correct: false,
                            _graded: "1",
                            _gradedType: "text",
                            _gradingProgression: "not-graded-none",
                            _html: false,
                            _points: parseInt(newQuestion.score),
                            _sortString: "",
                            _sortStringHtml: false,
                            _type: "answer",
                        };
                        answer_data.push(answer_params);
                        break;
                }

                // Save question data (and answers)
                axios.post("/ldlms/v1/sfwd-questions/"+newQuestion.question_id, {
                    _quizId: newQuestion.quiz_id,
                    _question: newQuestion.description,
                    _answerData: answer_data,
                    _answerType: answer_type,
                    _points: parseInt(newQuestion.score),
                });

                // Update ACF fields (show at):
                axios.post("/acf/v3/sfwd-quiz/"+newQuestion.quiz_id, {
                    fields: {
                        show_at: newQuestion.start_time,
                    }
                });

                break;
        }

    }

};

