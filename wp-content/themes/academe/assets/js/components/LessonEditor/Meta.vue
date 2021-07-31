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
                                reserve-keyword
                                size="medium"
                                style="width:100%"
                                placeholder="Enter Tag..."
                                no-match-text="No matching data"
                                no-data-text="No data"
                                loading-text="Loading"
                                :remote-method="loadTags"
                                :loading="loading">
                            <el-option
                                    v-for="tag in tags"
                                    :key="tag.id"
                                    :label="tag.name"
                                    :value="tag.slug">
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
                                    v-for="subject in subjects"
                                    :key="subject.id"
                                    :label="subject.name"
                                    :value="subject.slug">
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
                                    v-for="topic in topics"
                                    :key="topic.id"
                                    :label="topic.name"
                                    :value="topic.slug">
                            </el-option>
                        </el-select>
                    </td>
                </tr>
                <tr>
                    <th>Faculty</th>
                    <td>
                        <el-select
                                v-model="store.meta.faculties"
                                filterable
                                clearable
                                multiple
                                collapse-tags
                                size="medium"
                                style="width:350px"
                                placeholder="All"
                                no-data-text="No data">
                            <el-option
                                    v-for="faculty in faculties"
                                    :key="faculty.id"
                                    :label="faculty.name"
                                    :value="faculty.slug">
                            </el-option>
                        </el-select>
                    </td>
                </tr>
            </table>

            <el-checkbox v-model="store.meta.accept">I Have Read And Accept The Terms Of Use And Privacy Policy</el-checkbox>
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
            <template v-if="pixabay_suggestions.length">
                <h2 class="cover-subtitle">Suggestions</h2>
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
            this.getPixabaySuggestions(this.store.meta.title);
        },
        data() {
            return {
                loading: false,
                subjects: [],
                topics: [],
                faculties: [],
                tags: [],
                pixabay_suggestions: [],
                extended_search_pixabay_modal: false,
                suggestions_search: '',
            }
        },
        methods: {
            loadSubjects() {
                axios.get('/wp/v2/subject?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.subjects = this.mapTerms(res.data);
                });
            },
            loadTopics() {
                axios.get('/wp/v2/topic?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.topics = this.mapTerms(res.data);
                });
            },
            loadFaculties() {
                axios.get('/wp/v2/faculty?_wpnonce=' + wpApiSettings.nonce ).then(res => {
                    this.faculties = this.mapTerms(res.data);
                });
            },
            loadTags(query) {
                if (query !== '') {
                    this.loading = true;
                    axios.get('wp/v2/ld_course_tag?per_page=20&orderby=count&order=desc&search=' + query + '&_wpnonce=' + wpApiSettings.nonce).then(res => {
                        this.tags = this.mapTerms(res.data);
                        this.loading = false;
                    });
                } else {
                    this.tags = [];
                }
            },
            debounceSuggestions:
                debounce(function (e) {
                    this.getPixabaySuggestions(this.store.meta.title)
                }, 500),
            debounceSuggestionsModal:
                debounce(function (e) {
                    this.getPixabaySuggestions(this.suggestions_search)
                }, 500),
            getPixabaySuggestions(query) {
                query = encodeURI(query + ' | (' + query.replaceAll(' ', '|') + ')');
                axios.get('https://pixabay.com/api/?key=22034857-21d1cd8a83ad53f9ef50181a1&q='+query+'&image_type=photo,illustration&safesearch=true').then(res => {
                   if (res.data.hits) {
                       this.pixabay_suggestions = res.data.hits.map(image => ({
                           id: image.id,
                           preview: image.previewURL,
                           full: image.largeImageURL,
                       }));
                   }
                });
            },
            mapTerms(terms) {
                return terms.map(term => ({
                    id: term.id,
                    name: term.name,
                    slug: term.slug,
                }));
            }
        }
    }
</script>

<style scoped>
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