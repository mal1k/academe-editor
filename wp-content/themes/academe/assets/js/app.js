import Vue from 'vue'
import Vuex from 'vuex'
import store from './vuex-store'
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en'
import '../../element-variables.scss';

window.axios = require('axios');

Vue.use(Vuex);
Vue.use(ElementUI, { locale });

Vue.component('header-component', require('./components/LessonEditor/Shared/Header.vue').default);
Vue.component('lesson-editor-layout', require('./components/LessonEditor/Shared/Layout.vue').default);
Vue.component('lesson-editor', require('./components/LessonEditor/Editor.vue').default);

Vue.component('editor-meta', require('./components/LessonEditor/Meta.vue').default);
Vue.component('editor-slides', require('./components/LessonEditor/Slides.vue').default);

Vue.component('session-slideshow', require('./components/Session/Slideshow.vue').default);
Vue.component('session-movie-presentation', require('./components/Session/MoviePresentation.vue').default);

const app = new Vue({
    el: '#app',
    store,
    //render: h => h(App)
});