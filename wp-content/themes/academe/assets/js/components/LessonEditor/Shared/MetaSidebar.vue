<template>
    <div>
        <div class="meta-field column">
            <label>Title of Lesson</label>
            <el-input
                    placeholder=""
                    v-model="store.meta.title"
                    @input="debounceSuggestions"
                    @change="saveLesson()"
                    size="medium"
                    style="width:100%">
            </el-input>
        </div>
        <div class="meta-field column">
            <label>Lesson description</label>
            <el-input
                    type="textarea"
                    rows="4"
                    placeholder=""
                    v-model="store.meta.description"
                    style="width:100%">
            </el-input>
        </div>
        <div class="meta-field column">
            <label>Add Tags</label>
            <el-select
                    v-model="store.meta.tags"
                    multiple
                    filterable
                    remote
                    clearable
                    reserve-keyword
                    size="medium"
                    style="width:100%"
                    class="tags-selector"
                    placeholder="Enter Tag..."
                    no-match-text="No matching data"
                    no-data-text="No data"
                    loading-text="Loading"
                    :remote-method="loadTags"
                    :loading="loading"
                    @change="$emit('tags_changed')"
                    >
                <el-option
                        v-for="tag in store.meta_select_options.tags"
                        :key="tag.id"
                        :label="tag.name"
                        :value="tag.id">
                </el-option>
            </el-select>
        </div>
        <div class="meta-inline-dropdowns">
            <div class="meta-field row">
                <label>Subject</label>
                <el-select
                        v-model="store.meta.subjects"
                        filterable
                        clearable
                        multiple
                        collapse-tags
                        reserve-keyword
                        size="medium"
                        style="width:350px"
                        placeholder="All"
                        no-data-text="No data">
                    <el-option
                            v-for="subject in store.meta_select_options.subjects"
                            :key="subject.id"
                            :label="subject.name"
                            :value="subject.id">
                    </el-option>
                </el-select>
            </div>
            <div class="meta-field row">
                <label>Topic</label>
                <el-select
                        v-model="store.meta.topics"
                        filterable
                        clearable
                        multiple
                        collapse-tags
                        size="medium"
                        style="width:350px"
                        placeholder="All"
                        no-data-text="No data">
                    <el-option
                            v-for="topic in store.meta_select_options.topics"
                            :key="topic.id"
                            :label="topic.name"
                            :value="topic.id">
                    </el-option>
                </el-select>
            </div>
            <div class="meta-field row">
                <label>Grade</label>
                <el-select
                        v-model="store.meta.grades"
                        filterable
                        clearable
                        multiple
                        collapse-tags
                        size="medium"
                        style="width:350px"
                        placeholder="All"
                        no-data-text="No data">
                    <el-option
                            v-for="grade in store.meta_select_options.grades"
                            :key="grade.id"
                            :label="grade.name"
                            :value="grade.id">
                    </el-option>
                </el-select>
            </div>
        </div>
        <div class="meta-field column">
            <label>Select Cover Image</label>
            <div class="cover-source-btns">
                <div v-if="store.movie_id" @click="" class="cover-source-btn">From Library</div>
                <div @click="extended_search_pixabay_modal = true, suggestions_search = store.meta.title" class="cover-source-btn">From Pixabay</div>
            </div>
        </div>
        <el-dialog title="Select image" :visible.sync="extended_search_pixabay_modal">
            <el-input
                    placeholder=""
                    clearable
                    v-model="suggestions_search"
                    @input="debounceSuggestionsModal"
                    style="width:100%; margin-bottom: 40px;">
                <i class="el-icon-search el-input__icon" slot="prefix" @click=""></i>
            </el-input>

            <div class="suggestions-list five-per-line">
                <img v-for="image in pixabay_suggestions"
                     :key="image.id"
                     :src="image.preview"
                     @click="store.meta.thumbnail = image.full"
                     :class="{'selected' : store.meta.thumbnail === image.full}"
                />
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import axios from 'axios';
    import { debounce } from "debounce";
    import saveLessonService from "../../../save-lesson-service";

    export default {
        name: "MetaSidebar",
        components: {},
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            }
        },
        mounted() {
            let _this = this;

            Promise.all([this.loadSubjects(), this.loadTopics(), this.loadFaculties(), this.loadGrades()]).then(function (results) {
                _this.store.meta_fields_loaded = true;
            });
            this.loadMovieMeta();
            this.$emit('tags_changed');

            // setTimeout(function() {
            //     _this.getPixabaySuggestions(_this.store.meta.title);
            //     _this.getKalturaSuggestions();
            // }, 3000);
        },
        data() {
            return {
                loading: false,
                pixabay_suggestions: [],
                kaltura_suggestions: [],
                extended_search_pixabay_modal: false,
                suggestions_search: '',
            }
        },
        methods: {
            loadSubjects() {
                return axios.get('/wp/v2/subject?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.subjects = this.mapTerms(res.data);
                });
            },
            loadTopics() {
                return axios.get('/wp/v2/topic?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.topics = this.mapTerms(res.data);
                });
            },
            loadFaculties() {
                return axios.get('/wp/v2/faculty?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.faculties = this.mapTerms(res.data);
                });
            },
            loadGrades() {
                return axios.get('/wp/v2/grade?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.grades = this.mapTerms(res.data);
                });
            },
            loadTags(query) {
                if (query !== '') {
                    this.loading = true;
                    axios.get('/wp/v2/ptag?per_page=20&orderby=count&order=desc&search=' + query + '&_wpnonce=' + wpApiSettings.nonce).then(res => {
                        this.store.meta_select_options.tags = this.mapTerms(res.data);
                        this.loading = false;
                    });
                } else {
                    this.tags = [];
                }
            },
            loadMovieMeta() {
                var _this = this;
                setTimeout(function(){
                    if (_this.store.movie_id && _this.store.first_creation) {
                        axios.get('/wp/v2/movie/'+_this.store.movie_id+'?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                            _this.store.meta.title = res.data.title.rendered;
                            _this.store.meta.description = jQuery(res.data.content.rendered).text();

                            // This image is hardcoded. Please change it:
                            _this.store.meta.thumbnail = 'https://cdnapisec.kaltura.com/p/2538842/thumbnail/entry_id/'+res.data.acf.kaltura_id+'/width/1920/height/1080/type/1/quality/45';
                            // Add here tags, subjects, topics, faculties, grades
                            _this.store.meta.subjects = res.data.subject;
                            _this.store.meta.topics = res.data.topic;
                            _this.store.meta.grades = res.data.grade;
                            _this.store.meta.tags = res.data.ptag;

                            // Load tags names (to not show only ID):
                            axios.get('/wp/v2/ptag?per_page=100&orderby=count&order=desc&include=' + _this.store.meta.tags.join(',') + '&_wpnonce=' + wpApiSettings.nonce).then(res => {
                                _this.store.meta_select_options.tags = res.data.map(term => ({
                                    id: term.id,
                                    name: term.name,
                                    slug: term.slug,
                                }));
                                _this.$emit('tags_changed');
                            });
                        });
                    }
                }, 500);
            },
            debounceSuggestions:
                debounce(function (e) {
                    this.getPixabaySuggestions(this.store.meta.title)
                    //this.getKalturaSuggestions(this.store.meta.title)
                }, 500),
            debounceSuggestionsModal:
                debounce(function (e) {
                    this.getPixabaySuggestions(this.suggestions_search)
                    //this.getKalturaSuggestions(this.suggestions_search)
                }, 500),
            getPixabaySuggestions(query) {
                query = encodeURI(query + ' | (' + query.replaceAll(' ', '|') + ')');
                fetch('https://pixabay.com/api/?key=22034857-21d1cd8a83ad53f9ef50181a1&q='+query+'&image_type=photo,illustration&safesearch=true')
                    .then( response => {
                        return response.json();
                    }).then((data) => {
                    if (data.hits) {
                        this.pixabay_suggestions = data.hits.map(image => ({
                            id: image.id,
                            preview: image.previewURL,
                            full: image.largeImageURL,
                        }));
                    }
                });
            },

            getKalturaSuggestions() {
                var _this = this;
                setTimeout(function(){
                    if (_this.store.movie_id) {
                        axios.get('/wp/v2/movie/'+_this.store.movie_id+'?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                            _this.kaltura_id = res.data.acf.kaltura_id;
                            axios.get('/academe/v1/get-movie-images/'+_this.kaltura_id+'').then(res =>
                            { _this.store.kaltura_slice = res.data;
                                console.log (_this.store.kaltura_slice);
                                console.log ('+');
                            });
                        });
                    }
                }, 300);
            },

            mapTerms(terms) {
                return terms.map(term => ({
                    id: term.id,
                    name: term.name,
                    slug: term.slug,
                }));
            },
            saveLesson() {
                saveLessonService.initSave();
            }
        }
    }
</script>

<style scoped>
.meta-field {
    margin-bottom: 20px;
}
.meta-field.row {
    display: flex;
    align-items: center;
}
.meta-field label {
    font-weight: 600;
}
.meta-field.column label {
    margin-bottom: 5px;
    display: block;
}
.meta-field.row label {
    width: 180px;
}
.meta-inline-dropdowns {
    margin: 40px 0;
}
.cover-source-btns {
    display: flex;
    justify-content: flex-end;
}
.cover-source-btn {
    color: #51ACFD;
    margin-left: 15px;
    font-size: 12px;
    cursor: pointer;
}
</style>
<style>
.el-select__tags-text {
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    vertical-align: middle;
}
</style>