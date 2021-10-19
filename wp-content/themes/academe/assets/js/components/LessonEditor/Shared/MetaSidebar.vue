<template>
    <div>
        <div class="meta-field column">
            <label><span class="required">*</span>Title of Lesson</label>
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
            <label>Participant Movies</label>
            <el-input
                    :value="participantMovies()"
                    readonly
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
            <label class="btn-group-space-between"><span>Add Tags</span><span class="clear-tags" @click="store.meta.tags = []">Clear tags</span></label>
            <el-select
                    v-model="store.meta.tags"
                    multiple
                    collapse-tags
                    filterable
                    remote
                    clearable
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
                <label><span class="required">*</span>Subject</label>
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
                <label><span class="required">*</span>Grade</label>
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
        <div class="meta-field row cover-select">
            <label><span class="required">*</span>Select Cover from</label>
            <div class="cover-source-btns">
               <!-- <div v-if="store.movie_id" @click="" class="cover-source-btn">From Library</div>
                <div @click="extended_search_pixabay_modal = true, suggestions_search = store.meta.title" class="cover-source-btn">From Pixabay</div>-->
                <div v-if="store.movie_id" @click="extended_search_library_modal = true, suggestions_search = store.meta.title, getKalturaSuggestions()" class="cover-source-btn">
                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M21 11.4767H26.2473V10H21V11.4767Z" fill="white"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M22.9998 8L22.9998 13.2473L24.4766 13.2473L24.4766 8L22.9998 8Z" fill="white"/>
                        <path d="M19.4561 17.2429H16.0364L16.9622 14.9566H18.5243L17.1177 11.4778L17.025 11.247L16.9315 11.4778L16.9009 11.5535L16.2271 13.2172L15.5219 14.9566L14.5969 17.2429H14.5932L14.2282 18.152L14.2267 18.1564L13.8596 19.0714H11L15.7522 7.55078H18.2947L23.0462 19.0714H20.1986L19.4561 17.2429Z" fill="white"/>
                        <path d="M27.75 3.16683V21.6668H9.25V3.16683H27.75ZM27.75 0.0834961H9.25C7.55417 0.0834961 6.16667 1.471 6.16667 3.16683V21.6668C6.16667 23.3627 7.55417 24.7502 9.25 24.7502H27.75C29.4458 24.7502 30.8333 23.3627 30.8333 21.6668V3.16683C30.8333 1.471 29.4458 0.0834961 27.75 0.0834961ZM0 6.25016V27.8335C0 29.5293 1.3875 30.9168 3.08333 30.9168H24.6667V27.8335H3.08333V6.25016H0Z" fill="white"/>
                    </svg>
                    <span>Movie</span>
                </div>
                <div @click="extended_search_pixabay_modal = true, suggestions_search = store.meta.title, getPixabaySuggestions(suggestions_search)" class="cover-source-btn">
                    <svg width="37" height="37" viewBox="0 0 37 37" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M30.833 6.16683V24.6668H12.333V6.16683H30.833ZM30.833 3.0835H12.333C10.6372 3.0835 9.24967 4.471 9.24967 6.16683V24.6668C9.24967 26.3627 10.6372 27.7502 12.333 27.7502H30.833C32.5288 27.7502 33.9163 26.3627 33.9163 24.6668V6.16683C33.9163 4.471 32.5288 3.0835 30.833 3.0835ZM17.7288 17.9914L20.3343 21.4756L24.1576 16.6964L29.2913 23.1252H13.8747L17.7288 17.9914ZM3.08301 9.25016V30.8335C3.08301 32.5293 4.47051 33.9168 6.16634 33.9168H27.7497V30.8335H6.16634V9.25016H3.08301Z" fill="white"/>
                    </svg>
                    <span>Library</span>
                </div>
            </div>
        </div>

        <el-dialog title="Select image" class="suggestions-modal" :visible.sync="extended_search_library_modal">
            <div v-if="libraryLoad" class="suggestions-list">
                <img v-for="(image, index) in store.kaltura_slice"
                        :key="index"
                        :src="image"
                        @error="store.kaltura_slice.splice(index, 1)"
                        @click="store.meta.thumbnail = image, saveLesson()"
                        :class="{'selected' : store.meta.thumbnail === image}"
                />
            </div>
            <div v-else>
                Loading...
            </div>
        </el-dialog>
        <el-dialog title="Select image" class="suggestions-modal" :visible.sync="extended_search_pixabay_modal">
            <el-input
                    placeholder=""
                    clearable
                    v-model="suggestions_search"
                    @input="debounceSuggestionsModal"
                    style="width:100%; margin-bottom: 40px;">
                <i class="el-icon-search el-input__icon" slot="prefix"></i>
            </el-input>

            <div v-if="pixabayLoad" class="suggestions-list">
                <img v-for="image in pixabay_suggestions"
                     :key="image.id"
                     :src="image.preview"
                     @click="store.meta.thumbnail = image.full, saveLesson()"
                     :class="{'selected' : store.meta.thumbnail === image.full}"
                />
            </div>
            <div v-else>
                Loading...
            </div>
        </el-dialog>

        <el-checkbox v-model="store.meta.accept" class="accept">I Have Read And Accept <br/><a class="colorlink" href="/terms-of-use" target="_blank">The Terms Of Use</a> And <a class="colorlink" href="/privacy-policy" target="_blank">Privacy Policy</a></el-checkbox>

        <!--<el-dialog title="Select image" :visible.sync="extended_search_pixabay_modal">
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
        </el-dialog>-->
    </div>
</template>

<script>
    import axios from 'axios';
    import { debounce } from "debounce";
    import saveLessonService from "../../../save-lesson-service";
    import helper from "../../../helper";

    export default {
        name: "MetaSidebar",
        components: {},
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            },
            participantMovies() {
                return helper.participantMovies;
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
                pixabayLoad: false,
                libraryLoad: false,
                pixabay_suggestions: [],
                kaltura_suggestions: [],
                extended_search_pixabay_modal: false,
                extended_search_library_modal: false,
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
                            // Asked to remove:
                            // _this.store.meta.title = res.data.title.rendered;
                            // _this.store.meta.description = jQuery(res.data.content.rendered).text();

                            // This image is hardcoded. Please change it:
                            _this.store.meta.thumbnail = 'https://cdnapisec.kaltura.com/p/2538842/thumbnail/entry_id/'+res.data.acf.kaltura_id+'/width/1920/height/1080/quality/45';
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
                    this.getKalturaSuggestions(this.store.meta.title)
                }, 500),
            debounceSuggestionsModal:
                debounce(function (e) {
                    this.getPixabaySuggestions(this.suggestions_search)
                    this.getKalturaSuggestions(this.suggestions_search)
                }, 500),
            getPixabaySuggestions(query) {
                query = encodeURI(query + ' | (' + query.replaceAll(' ', '|') + ')');
                fetch('https://pixabay.com/api/?key=22034857-21d1cd8a83ad53f9ef50181a1&q='+query+'&image_type=photo,illustration&safesearch=true&per_page=100')
                    .then( response => {
                        return response.json();
                    }).then((data) => {
                    if (data.hits) {
                        this.pixabayLoad = true;
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
                    if (_this.store.movie_id) {
                        axios.get('/wp/v2/movie/'+_this.store.movie_id+'?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                            _this.kaltura_id = res.data.acf.kaltura_id;
                            axios.get('/academe/v1/get-movie-images/'+_this.kaltura_id+'').then(res =>
                            { 
                               _this.libraryLoad = true; 
                                _this.store.kaltura_slice = res.data;
                            });
                        });
                    }
            },

            mapTerms(terms) {
                return terms.map(term => ({
                    id: term.id,
                    name: term.name,
                    slug: term.slug,
                }));
            },
            saveLesson() {
                saveLessonService.initSave({
                    'type': 'auto'
                });
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
    position: relative;
}
.meta-field.column label {
    margin-bottom: 5px;
    display: block;
}
.meta-field.row label {
    width: 180px;
}
.meta-field label .required {
    position: absolute;
    top: -4px;
    left: -12px;
    color: #FF0000;
}
.meta-inline-dropdowns {
    margin: 40px 0;
}
.cover-source-btns {
    display: flex;
    justify-content: flex-end;
}
.cover-source-btn {
    margin-left: 20px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
}
.cover-source-btn span {
    color: #D0D0D0;
    font-size: 10px;
    font-weight: 500;
    line-height: 18px;
}
.cover-select {
    justify-content: space-between;
}
.cover-select label {
    margin-bottom: 10px;
}
</style>
<style>
.el-select__tags-text {
    max-width: 65px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: inline-block;
    vertical-align: middle;
}
.colorlink {
    color: #51ACFD;
    font-size: 14px;
}
.suggestions-modal .el-dialog {
    max-width: calc(100% - 80px);
    width: 885px;
}
label.btn-group-space-between {
    display: flex !important;
    justify-content: space-between;
}
.btn-group-space-between .clear-tags {
    font-weight: 500;
    cursor: pointer;
}
.el-input input:read-only {
    color: #979797;
}
</style>