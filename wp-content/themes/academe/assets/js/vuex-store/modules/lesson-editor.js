

// initial state
// shape: [{ id, quantity }]
import service from '../../service';

const state = () => ({
    active_page: 'slides', //meta
    active_slide: null,
    active_video: null,
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
    my_movies: null,
    movie: null,
    kaltura_config: null,
    active_question: null,
    view_question: null,
    questions: [],
});

// getters
const getters = {

};

// actions
const actions = {
    async getMyMovies({ commit }) {
        const myMovies = await service.getMyMovies();
        commit('setMyMovies', { myMovies })
    },
    async getKalturaConfig({ commit }) {
        const kalturaConfig = await service.getKalturaConfig();
        commit('setKalturaConfig', { kalturaConfig })
    },
};

// mutations
const mutations = {
    setMyMovies(state, payload) {
        state.my_movies = payload.myMovies;
    },
    setKalturaConfig(state, payload) {
        state.kaltura_config = payload.kalturaConfig;
    },
    updateSlide(state, payload) {
        state.slides = state.slides.map(s => s.lesson_id === payload.lesson_id ? { ...s, ...payload } : s)
    }
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
