import Vue from 'vue'
import Vuex from 'vuex'
import LessonEditor from './modules/lesson-editor'
import LessonSlideshow from './modules/lesson-slideshow'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        LessonEditor,
        LessonSlideshow
    },
    strict: false//debug,
})