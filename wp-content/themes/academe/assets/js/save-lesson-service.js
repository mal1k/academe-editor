import axios from "axios";
import store from './vuex-store';
import { Notification } from 'element-ui';
import helper from './helper';

axios.defaults.baseURL = window.wpApiSettings.root;
axios.defaults.headers.common["X-WP-Nonce"] = window.wpApiSettings.nonce;

export default {

    initPublish() {
        let storage = store.state.LessonEditor;
        storage.loading = true;

        axios.post("/ldlms/v1/sfwd-courses/"+storage.lesson_id, {
            status: storage.private ? 'private' : 'publish',
            date_gmt: new Date().toISOString(),
        }).then(res => {
            storage.loading = false;
            storage.status = storage.private ? 'private' : 'publish';
        });
    },

    initUnpublish() {
        let storage = store.state.LessonEditor;
        storage.loading = true;

        axios.post("/ldlms/v1/sfwd-courses/"+storage.lesson_id, {
            status: 'draft',
            date_gmt: new Date().toISOString(),
        }).then(res => {
            storage.loading = false;
            storage.status = 'draft';
        });
    },

    async initSave (params) {
        let storage = store.state.LessonEditor;
        let errors = this.validateFields();
        if (!storage.saving || params.type === 'manual') {
            if (!errors.length) {
                console.log('lesson save initialized');
                if (params.type === 'manual') {
                    storage.loading = true;
                }
                storage.saving = true;

                console.log(store.state.LessonEditor);

                // Create session only on first save
                if (!storage.first_session_created) {

                    const result = await fetch(ajaxurl + "?action=create_lesson_session", {
                        method: "POST",
                        body: JSON.stringify({lesson_id: store.state.LessonEditor.lesson_id})
                    });

                    const serverResp = await result.json();
                    if (!serverResp.error) {
                        const element = jQuery("#previewButton");
                        element.attr(
                            "href",
                            serverResp.success
                        );
                        storage.first_session_created = true;
                    } else {
                        alert(serverResp.error);
                    }
                }

                const saveCourse = this.saveCourseMeta();
                // Update ACF fields:
                await saveCourse.then(async function (results) {
                    // Old implementation (direct saving):
                    // axios.post("/acf/v3/sfwd-courses/"+storage.lesson_id, {
                    //     fields: {
                    //         cover_image_url: storage.meta.thumbnail,
                    //     }
                    // });
                    // New implementation (pixabay re-save locally):
                    await axios.post("/academe/v1/update-lessons-order-params", {
                        post_id: storage.lesson_id,
                    });
                    await axios.post("/academe/v1/save-image", {
                        post_id: storage.lesson_id,
                        field: 'cover_image_url',
                        url: storage.meta.thumbnail,
                    }).then(res => {

                    });

                    // Save movie_id if created from movie:
                    if (storage.movie_id || helper.participantMovies()) {
                        await axios.post("/acf/v3/sfwd-courses/"+storage.lesson_id, {
                            fields: {
                                movie_id: storage.movie_id ?? null,
                                participant_movies: helper.participantMovies() ?? null,
                            }
                        });
                    }
                });

                let slides_ids = storage.slides.map(function(slide) {
                    return slide.lesson_id;
                });

                // Does not work for unknown reasons (403)
                // axios.post('/ldlms/v1/sfwd-courses/'+storage.lesson_id+'/steps', {
                //     "t": {
                //         "sfwd-lessons": slides_ids
                //     }
                // });

                if(storage.slides.length) {
                    await Promise.all(storage.slides.map(async (slide, index) => {
                        await this.saveSlide(slide, index);
                    })).then(() => {
                        storage.loading = false; // seems all saved
                        storage.saving = false;
                    });
                    // Old version without promises:
                    // storage.slides.forEach(async (slide, index) => {
                    //     await this.saveSlide(slide, index);
                    // });
                }
            } else {
                if (params.type === 'manual') {
                    errors.forEach((error_message) => {
                        setTimeout(function () { // fix notifications overlap
                            Notification({
                                type: 'error',
                                title: 'Save error',
                                message: error_message
                            });
                        }, 10);
                    });
                }
            }
        }
    },
    async saveCourseMeta() {
        let storage = store.state.LessonEditor;
        let status;
        switch (storage.status) {
            case "publish":
            case "private":
                status = storage.private ? 'private' : 'publish';
                break;
            default:
                status = 'draft';
                break;
        }
        try {
            const response = await axios.post("/ldlms/v1/sfwd-courses/"+storage.lesson_id, {
                course_price_type: 'open',
                course_prerequisite_compare: 'ANY',
                course_disable_lesson_progression: true,
                order_enabled: true,
                orderby: 'menu_order',
                order: 'ASC',
                expire_access_days: 0,
                title: storage.meta.title,
                content: storage.meta.description,

                faculty: storage.meta.faculties,
                grade: storage.meta.grades,
                subject: storage.meta.subjects,
                topic: storage.meta.topics,
                ptag: storage.meta.tags,

                status: status,
            });
            if (storage.status === 'auto-draft') {
                storage.status = 'draft';
            } else {
                storage.status = status;
            }

            return response.data;
        } catch (err) {
            console.error(`Failed to save course`, err);
        }
    },
    async saveSlide(slide, index) {
        let storage = store.state.LessonEditor;

        await axios.post("/ldlms/v1/sfwd-lessons/"+slide.lesson_id, {
            menu_order: index + 1,
        });

        var acf_fields = {
            slide_type: slide.slide_type,
        };

        switch (slide.slide_type) {
            case "meta":
                await axios.post("/acf/v3/sfwd-lessons/"+slide.lesson_id, {
                    fields: acf_fields
                }).then( response => {

                });

                break;
            case "text_image":

                acf_fields.pre_defined_template = slide.template;

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
                    template1_text3: {
                        template1_text3_fill_color: slide.fields.template1_text3_fill_color,
                        template1_text3_font: slide.fields.template1_text3_font,
                        template1_text3_font_size: slide.fields.template1_text3_font_size,
                        template1_text3_font_weight: slide.fields.template1_text3_font_weight,
                        template1_text3_text: slide.fields.template1_text3_text,
                        template1_text3_text_align: slide.fields.template1_text3_text_align,
                        template1_text3_text_color: slide.fields.template1_text3_text_color,

                    },
                    template1_text4: {
                        template1_text4_fill_color: slide.fields.template1_text4_fill_color,
                        template1_text4_font: slide.fields.template1_text4_font,
                        template1_text4_font_size: slide.fields.template1_text4_font_size,
                        template1_text4_font_weight: slide.fields.template1_text4_font_weight,
                        template1_text4_text: slide.fields.template1_text4_text,
                        template1_text4_text_align: slide.fields.template1_text4_text_align,
                        template1_text4_text_color: slide.fields.template1_text4_text_color,

                    },
                    // template1_media1: {
                    //     template1_media1_image: slide.fields.template1_media1_image,
                    // }
                };

                // Save image (ACF with local saving pixabay):
                if (slide.fields.template1_media1_image) {
                    await axios.post("/academe/v1/save-image", {
                        post_id: slide.lesson_id,
                        field: 'template1_template1_media1_template1_media1_image',
                        url: slide.fields.template1_media1_image,
                    });
                }
                if (slide.fields.template1_media2_image) {
                    await axios.post("/academe/v1/save-image", {
                        post_id: slide.lesson_id,
                        field: 'template1_template1_media2_template1_media2_image',
                        url: slide.fields.template1_media2_image,
                    });
                }
                if (slide.fields.template1_media3_image) {
                    await axios.post("/academe/v1/save-image", {
                        post_id: slide.lesson_id,
                        field: 'template1_template1_media3_template1_media3_image',
                        url: slide.fields.template1_media3_image,
                    });
                }
                if (slide.fields.template1_media4_image) {
                    await axios.post("/academe/v1/save-image", {
                        post_id: slide.lesson_id,
                        field: 'template1_template1_media4_template1_media4_image',
                        url: slide.fields.template1_media4_image,
                    });
                }


                await axios.post("/acf/v3/sfwd-lessons/"+slide.lesson_id, {
                    fields: acf_fields
                });

                // for (const [key, value] of Object.entries(slide.fields)) {
                //     acf_fields['pre_defined_template_0_'+key] = value;
                // }

                break;
            case "movie":
                acf_fields.movie_slide = {
                    kaltura_id: slide.fields.kaltura_id,
                    play_from: helper.timeStringToSeconds(slide.fields.play_from),
                    play_to: helper.timeStringToSeconds(slide.fields.play_to),
                    post_type: (slide.fields.post_type !== "") ? slide.fields.post_type : 'movie',
                };

                await axios.post("/acf/v3/sfwd-lessons/"+slide.lesson_id, {
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
                await axios.post("/ldlms/v1/sfwd-questions/"+newQuestion.question_id, {
                    _quizId: newQuestion.quiz_id,
                    _question: newQuestion.description,
                    _answerData: answer_data,
                    _answerType: answer_type,
                    _points: parseInt(newQuestion.score),
                });

                // Update ACF fields (show at):
                await axios.post("/acf/v3/sfwd-quiz/"+newQuestion.quiz_id, {
                    fields: {
                        show_at: newQuestion.start_time,
                    }
                });

                // Update ACF fields for lesson (slide type):
                await axios.post("/acf/v3/sfwd-lessons/"+slide.lesson_id, {
                    fields: acf_fields
                });

                break;
        }

    },

    validateFields() {
        let storage = store.state.LessonEditor;
        let errorsList = [];
        if (!storage.meta.title) {
            errorsList.push('Title field must be filled!');
        }
        if (!storage.meta.thumbnail) {
            errorsList.push('Thumbnail must be selected!');
        }
        if (!storage.meta.subjects.length) {
            errorsList.push('Subject must be selected!');
        }
        if (!storage.meta.grades.length) {
            errorsList.push('Grade must be selected!');
        }
        return errorsList;
    },

};
