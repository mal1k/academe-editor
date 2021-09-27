<template>
  <div class="movie-options">
    <div class="search-form-results">
      <div v-if="movieMeta">
        <div class="movie-item">
          <img :src="movieMeta.image" alt="img" />
          <div class="movie-item__body">
            <div class="movie-item__header">
              <span class="movie-item__name">{{ movieMeta.title }}</span>
              <span class="movie-item__time">{{ movieMeta.time }}</span>
            </div>
            <div class="movie-item__tags" v-if="movieMeta.tags">
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
        <div class="movie-item__time-range">
          <div class="row-view">
            <label>From</label>
            <input
              type="tel" 
              v-mask="'##:##:##'"
              placeholder="xx:xx:xx"
              :value="play_from"
              @input="changePlayTime('play_from', $event.target.value)"
              class="el-input--time el-input--from el-input"
              :class="{'is-active': activeInput === 'playFrom'}"
              @focus="changeActiveInput('playFrom')"
            />
          </div>
          <div class="row-view">
            <label>To</label>
            <input
              type="tel" 
              v-mask="'##:##:##'"
              placeholder="xx:xx:xx"
              :value="play_to"
              @input="changePlayTime('play_to', $event.target.value)"
              class="el-input--time el-input--to el-input"
              :class="{'is-active': activeInput === 'playTo'}"
              @focus="changeActiveInput('playTo')"
            />
          </div>
        </div>
      </div>

      <p v-else>Loading movie meta info...</p>
    </div>
  </div>
</template>

<script>
import {mask} from 'vue-the-mask';
import service from "../../../service";

export default {
  name: "movie-options",
  props: {
    kalturaId: String,
  },
  data() {
    return {
      movieMeta: null,
      activeInput: 'playFrom',
    };
  },

  directives: {mask},

  computed: {
    store() {
      return this.$store.state.LessonEditor;
    },
    myMovies() {
      return this.$store.state.LessonEditor.my_movies;
    },
    activeSlide() {
      return this.$store.getters["LessonEditor/activeSlide"];
    },
    play_from() {
      const seconds = this.activeSlide?.fields?.play_from || 0;
      return this.secondsToTimeString(seconds);
    },
    play_to() {
      const seconds = this.activeSlide?.fields?.play_to || 0;
      return this.secondsToTimeString(seconds);
    },
  },
  watch: {
    kalturaId() {
      this.movieMeta = null;
      this.getVideoMeta();
    },
  },
  methods: {
    changeActiveInput(input){
       this.activeInput = input;
     },
    changePlayTime(type, val) {
      //const match = val.match(/\d\d:\d\d:\d\d/);
      const [h, m] = this.store.active_slide_movie_meta.time.split(" ");
      const maxTime = parseInt(+h.replace(/\D/g, '')) * 60 * 60 + parseInt(+m.replace(/\D/g, '')) * 60
      if (!this.activeSlide || this.timeStringToSeconds(val) > maxTime) return;

      this.$store.commit("LessonEditor/updateSlideFields", {
        id: this.activeSlide.lesson_id,
        fields: { [type]: this.timeStringToSeconds(val) },
      });
    },
    timeStringToSeconds(timeString) {
      const [hh, mm, ss] = timeString.split(":");
      const seconds = +hh * 60 * 60 + +mm * 60 + +ss;
      return seconds;
    },
    secondsToTimeString(input) {
      const sec_num = parseInt(input, 10);
      let hours = Math.floor(sec_num / 3600);
      let minutes = Math.floor((sec_num - hours * 3600) / 60);
      let seconds = sec_num - hours * 3600 - minutes * 60;

      if (hours < 10) {
        hours = "0" + hours;
      }
      if (minutes < 10) {
        minutes = "0" + minutes;
      }
      if (seconds < 10) {
        seconds = "0" + seconds;
      }
      return hours + ":" + minutes + ":" + seconds;
    },
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
      this.store.active_slide_movie_meta = this.movieMeta;
    },
  },
  mounted() {
    this.getVideoMeta();
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
  object-fit: cover;
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
.row-view {
  margin-bottom: 30px;
  display: flex;
  align-items: center;
}
.row-view label {
  min-width: 130px;
  margin-bottom: 10px;
  display: block;
}
.movie-item__time-range {
  margin-top: 24px;
}
.el-input {
  -webkit-appearance: none;
  background-color: #2f2f2f;
  background-image: none;
  border-radius: 0px;
  border: 1px solid #ffffff;
  box-sizing: border-box;
  color: #ffffff;
  display: inline-block;
  font-size: 16px;
  height: 34px;
  line-height: 34px;
  outline: none;
  padding: 0 20px;
  transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
  text-align: center;
}
.el-input--from {
  border: 1px solid #298f05;
}
.el-input--to {
  border: 1px solid #fd5151;
}
.el-input--time {
  width: 120px;
  max-width: 100%;
  text-align: center;
}
</style> 