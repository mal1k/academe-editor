// initial state
// shape: [{ id, quantity }]
import service from "../../service";

const state = () => ({
  lesson_id: null,
  movie_id: null,
  active_page: "slides", //meta or slides
  active_slide_id: null,
  active_video: null,
  active_block_meta: null,
  active_slide_movie_meta: null,
  meta: {
    title: "",
    description: "",
    tags: null,
    subjects: null,
    topics: null,
    faculties: null,
    accept: false,
    thumbnail: null,
  },

  advanced_search: {
    tags: null,
    subjects: null,
    topics: null,
    faculties: null,
    grades: null
  },

  slides: [],
  slide_templates: null,
  my_movies: null,
  all_movies: null,
  movie: null,
  kaltura_config: null,
  active_question: null,
  view_question: null,
  kaltura_slice: [],
  questions: [],
  course_steps: null,
  meta_select_options: {
    subjects: [],
    topics: [],
    faculties: [],
    grades: [],
    tags: [],
  },
  first_session_created: false,
  loading: false, // set to true when you need to show a preloader
  author: null,
  first_creation: false,
  meta_fields_loaded: false,
});

// getters
const getters = {
  activeSlide(state) {
    if (!state.active_slide_id) return null;
    return state.slides.find((s) => s.lesson_id === state.active_slide_id);
  },
};

// actions
const actions = {
  async getMyMovies({ commit }) {
    const myMovies = await service.getMyMovies();
    commit("setMyMovies", { myMovies });
  },

  async getAllMovies({ commit }) {
    const allMovies = await service.getAllMovies();
    commit('setAllMovies', { allMovies })
},

  async getKalturaConfig({ commit }) {
    const kalturaConfig = await service.getKalturaConfig();
    commit("setKalturaConfig", { kalturaConfig });
  },
};

// mutations
const mutations = {
  setMyMovies(state, payload) {
    state.my_movies = payload.myMovies;
  },

  setAllMovies(state, payload) {
    state.all_movies = payload.allMovies;
  },

  setKalturaConfig(state, payload) {
    state.kaltura_config = payload.kalturaConfig;
  },
  updateSlideFields(state, payload) {
    state.slides = state.slides.map((s) =>
      s.lesson_id === payload.id
        ? { ...s, fields: { ...s.fields, ...payload.fields } }
        : s
    );
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
