// initial state
// shape: [{ id, quantity }]
const state = () => ({
    active_page: 'slides', //meta
    active_slide: null,
    active_block_meta: null,
    meta: {
        title: 'Some title',
        description: 'Some description',
        tags: null,
        subjects: null,
        topics: null,
        faculties: null,
        accept: false,
        thumbnail: null,
    },
    slides: [

    ],
    slide_templates: null,
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