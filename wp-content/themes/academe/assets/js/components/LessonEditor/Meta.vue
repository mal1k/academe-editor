<template>
    <div class="meta-content">
        <div class="left-side">
            <h2 class="lesson-editor-title">Create a Lesson</h2>
            <table class="meta-form">
                <tr>
                    <th><label>Title of Lesson</label></th>
                    <td>
                        <el-input
                                placeholder=""
                                v-model="store.meta.title"
                                @input="debounceSuggestions"
                                @change="saveLesson()"
                                size="medium"
                                style="width:100%">
                        </el-input>
                    </td>
                </tr>
                <tr>
                    <th><label>Lesson description</label></th>
                    <td>
                        <el-input
                                type="textarea"
                                rows="4"
                                placeholder=""
                                v-model="store.meta.description"
                                style="width:100%">
                        </el-input>
                    </td>
                </tr>
                <tr>
                    <th>Add Tag</th>
                    <td>
                        <el-select
                                v-model="store.meta.tags"
                                multiple
                                filterable
                                remote
                                clearable
                                size="medium"
                                style="width:100%"
                                placeholder="Enter Tag..."
                                no-match-text="No matching data"
                                no-data-text="No data"
                                loading-text="Loading"
                                :remote-method="loadTags"
                                :loading="loading">
                            <el-option
                                    v-for="tag in store.meta_select_options.tags"
                                    :key="tag.id"
                                    :label="tag.name"
                                    :value="tag.id">
                            </el-option>
                        </el-select>
                    </td>
                </tr>
                <tr>
                    <th>Subject</th>
                    <td>
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
                    </td>
                </tr>
                <tr>
                    <th>Topic</th>
                    <td>
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
                    </td>
                </tr>
                <tr>
                    <th>Grade</th>
                    <td>
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
                    </td>
                </tr>
            </table>

            <el-checkbox v-model="store.meta.accept">I Have Read And Accept <a class="colorlink" href="#">The Terms Of Use</a> And <a class="colorlink" href="#">Privacy Policy</a></el-checkbox>
        </div>

        <div class="right-side">
            <h2 class="cover-subtitle">Select Cover image</h2>
            <div class="cover-image-preview">
                <div v-if="!store.meta.thumbnail"
                    class="select-from-pixabay"
                    @click="extended_search_pixabay_modal = true, suggestions_search = store.meta.title">
                    Select From Pixabay
                </div>
                <img v-else :src="store.meta.thumbnail" class="cover-image" />
            </div>

            

                        
            <template v-if="store.kaltura_slice.length">
                <h2 class="cover-subtitle">Suggestions</h2>
                <div class="suggestions-list three-per-line">
                    <img v-for="(image, index) in store.kaltura_slice.slice(0,3)"
                         :key="index"
                         :src="image"
                         @click="store.meta.thumbnail = image"
                         :class="{'selected' : store.meta.thumbnail === image}"
                    />
                </div>
            </template>


            
                

            <template v-if="pixabay_suggestions.length">
                <h2 class="cover-subtitle">From Library</h2>
                <div class="suggestions-list three-per-line">
                    <img v-for="image in pixabay_suggestions.slice(0,3)"
                         :key="image.id"
                         :src="image.preview"
                         @click="store.meta.thumbnail = image.full"
                         :class="{'selected' : store.meta.thumbnail === image.full}"
                    />
                </div>
                <span class="show-more" @click="extended_search_pixabay_modal = true, suggestions_search = store.meta.title">Show more</span>
            </template>

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
    import saveLessonService from "../../save-lesson-service";

    export default {
        name: "Meta",
        components: {},
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            }
        },
        mounted() {
            this.loadSubjects();
            this.loadTopics();
            this.loadFaculties();
            this.loadGrades();
            this.loadMovieMeta();

            let _this = this;
            setTimeout(function() {
                _this.getPixabaySuggestions(_this.store.meta.title);
                _this.getKalturaSuggestions();
            }, 3000);

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
                axios.get('/wp/v2/subject?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.subjects = this.mapTerms(res.data);
                });
            },
            loadTopics() {
                axios.get('/wp/v2/topic?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.topics = this.mapTerms(res.data);
                });
            },
            loadFaculties() {
                axios.get('/wp/v2/faculty?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.store.meta_select_options.faculties = this.mapTerms(res.data);
                });
            },
            loadGrades() {
                axios.get('/wp/v2/grade?per_page=100&_wpnonce=' + wpApiSettings.nonce ).then(res => {
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
                    if (_this.store.movie_id) {
                        axios.get('/wp/v2/movie/'+_this.store.movie_id+'?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                            _this.store.meta.title = res.data.title.rendered;
                            _this.store.meta.description = jQuery(res.data.content.rendered).text();

                            // This image is hardcoded. Please change it:
                            _this.store.meta.thumbnail = 'https://cdnapisec.kaltura.com/p/2538842/thumbnail/entry_id/'+res.data.acf.kaltura_id+'/width/280/height/175/type/1/quality/45';
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
    .colorlink {color: #51ACFD;}
    .lesson-editor-title {
        color: #51ACFD;
        font-weight: 600;
        font-size: 22px;
    }
    .cover-subtitle {
        color: #FFFFFF;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 15px;
    }
    .meta-form {
        margin: 30px 0 60px 0;
        width: 80%;
    }
    .meta-form th {
        vertical-align: top;
        text-align: left;
        padding: 20px 10px 20px 0;
        width: 200px;
        line-height: 1.3;
        font-weight: 600;
    }
    .meta-form td {
        margin-bottom: 9px;
        padding: 15px 10px;
        line-height: 1.3;
        vertical-align: middle;
    }
    .meta-content {
        display: flex;
        padding: 0 80px;
    }
    .meta-content .left-side {
        flex: 1;
        padding: 40px 0;
    }
    .meta-content .right-side {
        background: #2F2F2F;
        box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.8);
        border-radius: 2px;
        width: 30%;
        min-width: 500px;
        padding: 40px;
    }
    .cover-image-preview {
        border: 1px solid #FFFFFF;
        display: flex;
        width: 100%;
        height: 250px;
        justify-content: center;
        align-items:center;
        margin-bottom: 30px;
    }
    .select-from-pixabay {
        color: #51ACFD;
        font-size: 12px;
        font-weight: 500;
        padding: 20px;
        cursor: pointer;
    }
    img.cover-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .suggestions-list {
        display: flex;
        flex-flow: row wrap;
        justify-content: space-between;
    }
    /*.suggestions-list::after {*/
    /*    content: "";*/
    /*    flex: auto;*/
    /*}*/
    .suggestions-list img {
        height: 95px;
        object-fit: cover;
        cursor: pointer;
        margin-bottom: 20px;
    }
    .suggestions-list img.selected {
        border: 2px solid #51ACFD;
    }
    .suggestions-list.three-per-line img {
        width: calc(100% / 3 - 7px);
    }
    .suggestions-list.five-per-line img {
        width: calc(100% / 5 - 7px);
    }
    .show-more {
        float: right;
        color: #51ACFD;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
    }
</style> 