<template>
  <div class="search-filter">
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
    <div v-if="movies" class="search-form-results">
      <div>My List ({{ searchFilter.length }} Movies)</div>
      <MovieItem
        v-for="movie in searchFilter"
        v-on:add-click="$emit('add-movie', movie)"
        :key="movie.kalturaID"
        :movie_data="movie"
      />
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
      searchMovie: "",
    };
  },
  computed: {
    movies() {
      return this.$store.state.LessonEditor.my_movies;
    },
    searchFilter() {
      if (this.searchMovie) {
        return this.movies.filter((item) => {
          return this.searchMovie
            .toLowerCase()
            .split(" ")
            .every((v) => item.title.toLowerCase().includes(v));
        });
      } else {
        return this.movies;
      }
    },
  },
  methods: {
    getMyMovies() {
      this.$store.dispatch("LessonEditor/getMyMovies");
    },
  },
  async mounted() {
    if (!this.my_movies) {
      this.getMyMovies();
    }
  },
};
</script>

<style scoped>
.search-form {
  width: 100%;
  height: 42px;
  margin-bottom: 30px;
  border: 1px solid #c4c4c4;
  display: flex;
  align-items: center;
  padding: 10px;
}
.search-input {
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
</style>
