<template>
    <main class="dark-wrap">
        <editor-meta v-if="store.active_page == 'meta'" />
        <editor-slides v-if="store.active_page == 'slides'" />
    </main>
</template>

<script>
    import { mapGetters, mapState } from 'vuex'

    export default {
        name: "LessonEditor",
        props: {
            post: Number,
        },
        data() {
            return {

            }
        },
        mounted() {
            // axios.get('/wp-json/wp/v2/faculty?_wpnonce=' + wpApiSettings.nonce ).then(res => {
            //     console.log(res.data);
            //     // if (!res.data.error) {
            //     //
            //     // } else {
            //     //
            //     // }
            // });
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
            }
        }
    }
</script>

<style scoped>
    .dark-wrap {
        color: #FFFFFF;
    }
</style>