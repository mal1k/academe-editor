// initial state
// shape: [{ id, quantity }]
const state = () => ({
    course_content: null,
    course_meta: null,
    slide_content: null,
    slide_type: null, // text_image || movie || question
    quiz_content: null,
    next_slide: null,
    prev_slide: null,
    active_movie_duration: null,

});

// getters
const getters = {

};

// actions
const actions = {

};

// mutations
const mutations = {

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}