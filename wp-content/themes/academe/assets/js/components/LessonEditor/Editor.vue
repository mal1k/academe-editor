<template>
    <main class="dark-wrap"
          v-loading="store.loading"
          element-loading-text="Loading..."
          element-loading-spinner="el-icon-loading"
          element-loading-background="rgba(0, 0, 0, 0.8)">
        <!--<editor-meta v-if="store.active_page == 'meta'" />-->
        <editor-slides ref="editorSlides" v-if="store.active_page == 'slides'" />
    </main>
</template>

<script>
    import { mapGetters, mapState } from 'vuex'
    import loadLessonService from "../../load-lesson-service";

    export default {
        name: "LessonEditor",
        props: {
            post: Number,
            movie: {
                type: Number,
                required: false,
                default: null
            },
            clip: {
                type: Number,
                required: false,
                default: null
            },
            author: String,
        },
        data() {
            return {

            }
        },
        mounted() {
            this.store.lesson_id = this.post;
            this.store.movie_id = this.movie;
            this.store.clip_id = this.clip;
            this.store.author = this.author;

            this.updateQueryString();
        },
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            },
            // ...mapState({
            //     meta: state => state.LessonEditor.meta,
            //     slides: state => state.LessonEditor.slides
            // }),
            // ...mapGetters('lessonEditor', {
            //     //products: 'cartProducts',
            // }),
            meta: {
                get () {
                    return this.$store.state.LessonEditor.meta
                },
                set (value) {
                    this.$store.commit('updateMeta', value)
                }
            },
            slides: {
                get () {
                    return this.$store.state.LessonEditor.slides
                },
                set (value) {
                    this.$store.commit('updateSlides', value)
                }
            }
        },
        methods: {
            changeTT() {
                this.$store.state.LessonEditor.slides[0].template_type = 'layout1';
            },
            updateQueryString() {
                let params = new URLSearchParams(location.search);

                if (params.has('movie_id')) {
                    params.delete('movie_id');
                }

                if (params.has('clip_id')) {
                    params.delete('clip_id');
                }

                if (!params.has('lesson_id')) {
                    params.set('lesson_id', this.store.lesson_id);
                    window.history.replaceState({}, '', `${location.pathname}?${params}`);
                    this.store.first_creation = true;
                    this.store.loading = true;
                    this.$refs.editorSlides.createMetaSlide();
                } else {
                    loadLessonService.initLoad();
                }
            }
        }
    }
</script>

<style scoped>
    .dark-wrap {
        color: #FFFFFF;
    }
</style> 