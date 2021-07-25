import Vue from 'vue'
import Vuex from 'vuex'
import store from './vuex-store'
import axios from 'axios';
import ElementUI from 'element-ui';
import locale from 'element-ui/lib/locale/lang/en'
import '../../element-variables.scss';

axios.defaults.baseURL = window.wpApiSettings.root;
axios.defaults.headers.common['X-WP-Nonce'] = window.wpApiSettings.nonce;

Vue.use(Vuex);
Vue.use(ElementUI, { locale });

Vue.component('header-component', require('./components/LessonEditor/Shared/Header.vue').default);
Vue.component('lesson-editor-layout', require('./components/LessonEditor/Shared/Layout.vue').default);
Vue.component('lesson-editor', require('./components/LessonEditor/Editor.vue').default);

Vue.component('editor-meta', require('./components/LessonEditor/Meta.vue').default);
Vue.component('editor-slides', require('./components/LessonEditor/Slides.vue').default);

const app = new Vue({
    el: '#app',
    store,
    //render: h => h(App)
});