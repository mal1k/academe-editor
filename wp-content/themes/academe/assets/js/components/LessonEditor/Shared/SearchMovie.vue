<template>
  <div class="search-filter">
    <div >
      <div class="search-form__top">
        <div class="search-form">
          <div class="search-btn">
            <img src="/wp-content/themes/academe/assets/img/search.svg" />
          </div>
          <input
            class="search-input"
            type="text"
            v-model="searchMovie"
            placeholder="Search title.."
          />
        </div>
        <div v-if="activeName == 'library'">
          <el-link :underline="false" class="small-link" @click="advanced_search=true">Advanced Search</el-link>
        </div>
      </div>
      <div v-if="advanced_search" class="advanced_search">
        <div class="advanced_search__top">
          <div>Applied filters ({{filterCount}})</div>
          <el-link type="primary" @click="resetSearch()">Reset</el-link>
        </div>
        <div class="row-view">
          <label>Tags</label>
           <el-select
                  v-model="store.advanced_search.tags"
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
                    :value="tag.name">
            </el-option>
          </el-select>
        </div>
        <div class="row-view">
          <label>Subject</label>
            <el-select
                    v-model="store.advanced_search.subjects"
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
                        :value="subject.id">
                </el-option>
            </el-select>
        </div>
        <div class="row-view">
          <label>Topic</label>
            <el-select
                    v-model="store.advanced_search.topics"
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
                        :value="topic.id">
                </el-option>
            </el-select>
        </div>
        <div class="row-view">
          <label>Grade</label>
            <el-select
                    v-model="store.advanced_search.grades"
                    filterable
                    clearable
                    multiple
                    collapse-tags
                    size="medium"
                    style="width:350px"
                    placeholder="All"
                    no-data-text="No data">
                <el-option
                        v-for="grade in grades"
                        :key="grade.id"
                        :label="grade.name"
                        :value="grade.id">
                </el-option>
            </el-select>
        </div>
        <!-- <div class="row-view">
          <label>Faculties</label>
            <el-select
                    v-model="store.advanced_search.faculties"
                    filterable
                    clearable
                    multiple
                    collapse-tags
                    size="medium"
                    style="width:350px"
                    placeholder="All"
                    no-data-text="No data">
                <el-option
                        v-for="facultie in faculties"
                        :key="facultie.id"
                        :label="facultie.name"
                        :value="facultie.id">
                </el-option>
            </el-select>
        </div> -->
        <el-row>
          <el-button type="primary" size="medium" plain @click="advanced_search=false">Cancel</el-button>
           <el-button type="primary" size="medium" @click="applyFilter()">Apply</el-button>
        </el-row>
      </div>
    </div>

    <div v-if="movies" class="search-form-results">
      <el-tabs  v-model="activeName" @tab-click="changeLibrary" class="search-form-tabs">
        <el-tab-pane label="Library" name="library">
          <MovieItem
            v-for="movie in moviesList"
            v-on:add-click="$emit('add-movie', movie)"
            :key="movie.kalturaID"
            :movie_data="movie"
          />
        </el-tab-pane>
        <el-tab-pane label="My Videos" name="my-list">
          <!-- <div>My List ({{ searchFilter.length }} Movies)</div> -->
          <MovieItem
            v-for="movie in searchFilter"
            v-on:add-click="$emit('add-movie', movie)"
            :key="movie.kalturaID"
            :movie_data="movie"
          />
        </el-tab-pane>
      </el-tabs>   
    </div>
    <p v-else>Loading...</p>
  </div>
</template>

<script>
import MovieItem from "../Shared/MovieItem";

export default {
  name: "search-filter",
  components: {
    MovieItem,
  },
  props: {},
  data() {
    return {
      activeName: 'library',
      moviesList: [],
      searchMovie: "",
      advanced_search: false,
      loading: false,
      subjects: [],
      topics: [],
      faculties: [],
      grades: [],
      tags: [],
    };
  },
  computed: {
    store() {
      return this.$store.state.LessonEditor;
    },
    movies() {
      if(this.activeName == 'library'){
        return this.$store.state.LessonEditor.all_movies;
      } else {
        return this.$store.state.LessonEditor.my_movies;
      }     
    },
    searchFilter() {
      if (this.searchMovie) {
        return this.moviesList = this.movies.filter((item) => {
          return this.searchMovie
            .toLowerCase()
            .split(" ")
            .every((v) => item.title.toLowerCase().includes(v));
        });
      } else {
        return this.moviesList = this.movies;
      }
    },
    filterCount(){
      let count = 0;
      for (var i = 0; i < Object.keys(this.store.advanced_search).length; i++) {
        if (Object.values(this.store.advanced_search)[i] != null && Object.values(this.store.advanced_search)[i].length > 0){
         count++;
        }
      }
      return count
    },
  },
  async mounted() {
    if (!this.my_movies) {
      this.getMyMovies();
    }
    if (!this.all_movies) {
      this.getAllMovies();
    }

    this.loadSubjects();
    this.loadTopics();
    this.loadFaculties();
    this.loadGrades();

  },
  methods: {
    changeLibrary(tab, event) {
        this.searchMovie = "";
        if (this.activeName != 'library') {
          this.advanced_search = false
        }
    },
    getMyMovies() {
      this.$store.dispatch("LessonEditor/getMyMovies");
    },
    getAllMovies() {
      this.$store.dispatch("LessonEditor/getAllMovies");
    },


        //filters callback
    checkContainsTags(movie) {
      if (typeof movie['tags'] !== "undefined" && this.store.advanced_search.tags != null) {
        let movie_tag_list = movie.tags.map(el => el.name);
        return movie_tag_list.length - movie_tag_list.filter(tag => this.store.advanced_search.tags.indexOf(tag) < 0).length === this.store.advanced_search.tags.length
      } else if (typeof movie['tags'] === "undefined" && this.store.advanced_search.tags != null) {
        return false
      }else {
        return true
      }
    },
    checkContainsSubject(movie) {
      if (typeof movie['subject'] !== "undefined" && this.store.advanced_search.subjects != null) {
        let movie_subject_list = movie.subject.map(el => el.id);
        return movie_subject_list.length - movie_subject_list.filter(subject => this.store.advanced_search.subjects.indexOf(subject) < 0).length === this.store.advanced_search.subjects.length
      } else if (typeof movie['subject'] === "undefined" && this.store.advanced_search.subjects != null) {
        return false
      }else {
        return true
      }
    },
    checkContainsTopic(movie) {
      if (typeof movie['topic'] !== "undefined" && this.store.advanced_search.topics != null) {
        let movie_topic_list = movie.topic.map(el => el.id);
        return movie_topic_list.length - movie_topic_list.filter(topic => this.store.advanced_search.topics.indexOf(topic) < 0).length === this.store.advanced_search.topics.length
      } else if (typeof movie['topic'] === "undefined" && this.store.advanced_search.topics != null) {
        return false
      }else {
        return true
      }
    },
    checkContainsGraid(movie) {
      if (typeof movie['grade'] !== "undefined" && this.store.advanced_search.faculties != null) {
        let movie_grade_list = movie.grade.map(el => el.id);
        return movie_grade_list.length - movie_grade_list.filter(grade => this.store.advanced_search.grades.indexOf(grade) < 0).length === this.store.advanced_search.grades.length
      } else if (typeof movie['grade'] === "undefined" && this.store.advanced_search.faculties != null) {
        return false
      }else {
        return true
      }
    },
    checkContainsFaculty(movie) {
      if (typeof movie['faculty'] !== "undefined" && this.store.advanced_search.grades != null) {
        let movie_faculty_list = movie.faculty.map(el => el.id);
        return movie_faculty_list.length - movie_faculty_list.filter(faculty => this.store.advanced_search.faculties.indexOf(faculty) < 0).length === this.store.advanced_search.faculties.length
      } else if (typeof movie['faculty'] === "undefined" && this.store.advanced_search.grades != null) {
        return false
      }else {
        return true
      } 
    },
    // Apply advanced search on the list of movies
    applyFilter(){
      console.log(this.moviesList);
      if (this.searchMovie) {
        this.moviesList = this.movies.filter((item) => {
          return this.searchMovie
            .toLowerCase()
            .split(" ")
            .every((v) => item.title.toLowerCase().includes(v));
        });
      } else {
         this.moviesList = this.movies;
      }
      if (this.filterCount > 0) {
        return this.moviesList = this.moviesList
                    .filter(this.checkContainsTags)
                    .filter(this.checkContainsSubject)
                    .filter(this.checkContainsGraid)
                    .filter(this.checkContainsTopic);
      } else {
        return this.searchFilter;
      }
    },
    
    resetSearch() {
      this.store.advanced_search.tags = null;
      this.store.advanced_search.subjects = null;
      this.store.advanced_search.topics = null;
      this.store.advanced_search.faculties = null;
      this.store.advanced_search.grades = null;
      this.searchMovie = "";
      this.moviesList = this.movies;
    },
    //for advanced search
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
    loadGrades() {
        axios.get('/wp/v2/grade?_wpnonce=' + wpApiSettings.nonce ).then(res => {
            this.grades = this.mapTerms(res.data);
        });
    },
    loadTags(query) {
        if (query !== '') {
            this.loading = true;
            axios.get('/wp/v2/ptag?per_page=20&orderby=count&order=desc&search=' + query + '&_wpnonce=' + wpApiSettings.nonce).then(res => {
                this.tags = this.mapTerms(res.data);
                this.loading = false;
            });
        } else {
            this.tags = [];
        }
    },
    mapTerms(terms) {
        return terms.map(term => ({
            id: term.id,
            name: term.name,
            slug: term.slug,
        }));
    },
  },
};
</script>

<style>
.search-form {
  width: 100%;
  height: 42px;
  margin-bottom: 30px;
  border: 1px solid #c4c4c4;
  display: flex;
  align-items: center;
  padding: 10px;
  flex-grow: 1;
}
.search-input {
  width: 190px;
  border: none;
  background: rgba(0, 0, 0, 0);
  color: #fff;
  height: 20px;
  flex: 1;
}
.search-input::placeholder {
  color: #fff;
  opacity: 0.7;
  font-weight: 500;
}
.search-btn {
  margin-right: 10px;
}
.search-btn img {
  max-height: 30px;
}
.search-form__top {
  display: flex;
}
.search-form-tabs .el-tabs__nav {
  padding: 0;
}
.search-form-tabs .el-tabs__item {
  width: 50%;
  border: 1px solid #FFFFFF;
  border-color: #FFFFFF;
  color: #FFFFFF;
  -webkit-appearance: none;
  text-align: center;
  box-sizing: border-box;
  outline: none;
  margin: 0;
  transition: 0.1s;
  font-weight: 500;
  padding: 10px 20px;
  padding-left: 10px;
  padding-right: 10px;
  font-size: 14px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.search-form-tabs .el-tabs__item.is-active{
  border: 1px solid #51acfd;
  color: #51acfd;
}
.small-link .el-link--inner {
    font-size: 12px;
    padding: 0 10px;
    transition: all .3s ease;
}
.el-select {
  max-width: 100%;
}
.row-view {
    margin-bottom: 20px;
}
.row-view label{
    display: block;
    margin-bottom: 10px;
}
.advanced_search {
  padding: 10px;
  border-radius: 3px;
  border: 1px solid #fff;
  margin-bottom: 30px;
}
.advanced_search__top {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
</style>
