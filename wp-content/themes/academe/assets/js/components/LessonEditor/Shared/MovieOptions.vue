<template>
  <div class="movie-options">
    <div class="search-form-results">
      <div class="movie-item" v-if="movieMeta">
        <img :src="movieMeta.image" alt="img" />
        <div class="movie-item__body">
          <div class="movie-item__header">
            <span class="movie-item__name">{{ movieMeta.title }}</span>
            <span class="movie-item__time">{{ movieMeta.time }}</span>
          </div>
          <div class="movie-item__tags">
            <a
              href="#"
              class="movie-item__tag"
              v-for="(tag, index) in movieMeta.tags.slice(0, 2)"
              :key="index"
              >#{{ tag.tag }}
            </a>
          </div>
          <div class="movie-item__footer">
            <button class="movie-item__link" @click="$emit('replace-click')">
              Replace
            </button>

            <button class="movie-item__link" @click="$emit('remove-click')">
              Remove
            </button>

          </div>
        </div>
      </div>

      <p v-else>Loading movie meta info...</p>
    </div>
  </div>
</template>

<script>
import service from "../../../service";

export default {
  name: "movie-options",
  props: {
    kalturaId: String,
  },
  data() {
    return {
      movieMeta: null,
    };
  },
  computed: {
    myMovies() {
      return this.$store.state.LessonEditor.my_movies;
    },
  },
  methods: {
    async getVideoMeta() {
      if (this.myMovies) {
        // Search movie meta in my movies
        const meta = this.myMovies.find((m) => m.kaltura_id === this.kalturaId);
        
        if (meta) {
          this.movieMeta = meta;
        } else {
          // if this movie is not in my list then fetch info from server
          this.fetchMovieMetaByKaltura();
        }
      } else {
        // if my movies list not fetched yet then fetch movie meta from server
        this.fetchMovieMetaByKaltura();
      }
    },
    async fetchMovieMetaByKaltura() {
      this.movieMeta = await service.getMovieMetaByKalturaId(this.kalturaId);
    },
  },
  mounted() {
    this.getVideoMeta();
  },
  watch: {
    kalturaId() {
      this.movieMeta = null;
      this.getVideoMeta();
    },
  },
};
</script>

<style scoped>
.movie-item {
  display: flex;
  padding: 16px 0 19px;
  border-bottom: 1px solid #51acfd;
}
.movie-item img {
  width: 35%;
  margin-right: 10px;
}
.movie-item__body {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}
.movie-item__header {
  display: flex;
  justify-content: space-between;
}
.movie-item__name {
  font-family: Montserrat;
  font-weight: 600;
  font-size: 14px;
  line-height: 18px;
  text-transform: uppercase;
  color: #ffffff;
}
.movie-item__time {
  font-family: Montserrat;
  font-style: normal;
  font-weight: 500;
  font-size: 12px;
  line-height: 15px;
  color: #ffffff;
}
.movie-item__tags {
  display: flex;
  flex-wrap: wrap;
}
.movie-item__tag {
  margin-right: 3px;
  font-family: Montserrat;
  font-style: normal;
  font-weight: 500;
  font-size: 12px;
  line-height: 18px;
  letter-spacing: 0.175px;
  color: #51acfd;
}
.movie-item__footer {
  margin-top: 12px;
  flex-grow: 1;
  display: flex;
  align-items: flex-end;
}
.movie-item__link {
  background-color: transparent;
  border: none;
  font-family: Montserrat;
  font-style: normal;
  font-weight: 500;
  font-size: 14px;
  line-height: 18px;
  letter-spacing: 0.175px;
  text-decoration-line: underline;
  color: #51acfd;
  cursor: pointer;
  margin-right: 10px;
}


</style> 