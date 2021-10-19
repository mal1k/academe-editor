<template>
  <div class="movie-options">
    <div class="search-form-results">
      <div v-if="movie_meta">
        <div class="movie-item">
          <div class="img-container">
            <img :src="movie_meta.image" alt="img" />
            <div class="slide-label" :class="movie_meta.post_type">
              <template v-if="movie_meta.post_type === 'movie'">
                <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2.125 0.5C1.09684 0.5 0.25 1.34684 0.25 2.375V12.375C0.25 13.4032 1.09684 14.25 2.125 14.25H15.875C16.9032 14.25 17.75 13.4032 17.75 12.375V2.375C17.75 1.34684 16.9032 0.5 15.875 0.5H2.125ZM2.125 1.75H15.875C16.2268 1.75 16.5 2.02316 16.5 2.375V12.375C16.5 12.7268 16.2268 13 15.875 13H2.125C1.77316 13 1.5 12.7268 1.5 12.375V2.375C1.5 2.02316 1.77316 1.75 2.125 1.75ZM2.75 3V4.25H4V3H2.75ZM14 3V4.25H15.25V3H14ZM2.75 5.5V6.75H4V5.5H2.75ZM14 5.5V6.75H15.25V5.5H14ZM2.75 8V9.25H4V8H2.75ZM14 8V9.25H15.25V8H14ZM2.75 10.5V11.75H4V10.5H2.75ZM14 10.5V11.75H15.25V10.5H14Z" fill="white"/>
                </svg>
              </template>
              <template v-if="movie_meta.post_type === 'clip'">
                <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2.125 0.5C1.09125 0.5 0.25 1.34125 0.25 2.375V12.375C0.25 13.4087 1.09125 14.25 2.125 14.25H3.25537C3.41662 13.7912 3.6675 13.3687 4 13H2.125C1.78125 13 1.5 12.7187 1.5 12.375V2.375C1.5 2.03125 1.78125 1.75 2.125 1.75H15.875C16.2188 1.75 16.5 2.03125 16.5 2.375V8H17.75V2.375C17.75 1.34125 16.9088 0.5 15.875 0.5H2.125ZM2.75 3V4.25H4V3H2.75ZM14 3V4.25H15.25V3H14ZM2.75 5.5V6.75H4V5.5H2.75ZM14 5.5V6.75H15.25V5.5H14ZM7.75 6.75488C7.10889 6.75489 6.46795 6.99687 5.98242 7.48242C5.01136 8.45351 5.01133 10.0465 5.98242 11.0176C6.95351 11.9886 8.54652 11.9887 9.51758 11.0176C9.611 10.9242 9.68871 10.8211 9.76416 10.7173L12.2739 12.375L9.80322 14.0791C9.71924 13.9574 9.62558 13.8404 9.51758 13.7324C9.03203 13.2469 8.39111 13.0049 7.75 13.0049C7.10889 13.0049 6.46795 13.2469 5.98242 13.7324C5.01136 14.7035 5.01133 16.2965 5.98242 17.2676C6.95351 18.2386 8.54652 18.2387 9.51758 17.2676C10.0725 16.7127 10.2999 15.9555 10.2207 15.2266L13.3652 13.0977L17.0005 15.5H19L10.2207 9.52344C10.3 8.79449 10.0725 8.0373 9.51758 7.48242C9.03203 6.99689 8.39111 6.75487 7.75 6.75488ZM7.75 7.99512C8.06859 7.99511 8.38709 8.11952 8.63379 8.36621C9.12719 8.85959 9.12717 9.64039 8.63379 10.1338C8.14041 10.6272 7.35961 10.6272 6.86621 10.1338C6.37281 9.64041 6.37283 8.85961 6.86621 8.36621C7.1129 8.11951 7.43141 7.99512 7.75 7.99512ZM2.75 8V9.25H4V8H2.75ZM14 8V9.25H14.7056L15.25 8.896V8H14ZM17.0005 9.25L14.5005 10.876L15.626 11.6255L19 9.25H17.0005ZM2.75 10.5V11.75H4V10.5H2.75ZM7.75 14.2451C8.06859 14.2451 8.38709 14.3695 8.63379 14.6162C9.12719 15.1096 9.12717 15.8904 8.63379 16.3838C8.14041 16.8772 7.35961 16.8772 6.86621 16.3838C6.37281 15.8904 6.37283 15.1096 6.86621 14.6162C7.1129 14.3695 7.43141 14.2451 7.75 14.2451Z" fill="white"/>
                </svg>
              </template>
            </div>
          </div>
          <div class="movie-item__body">
            <div class="movie-item__header">
              <span class="movie-item__name">{{ movie_meta.title }}</span>
              <span class="movie-item__time" :class="{'clip': movie_meta.post_type === 'clip'}">{{ croppedTime() }}</span>
            </div>
            <div class="movie-item__tags" v-if="movie_meta.tags">
              <a
                href="#"
                class="movie-item__tag"
                v-for="(tag, index) in movie_meta.tags.slice(0, 2)"
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
        <div class="btn" @click="$emit('add-question')">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="#51acfd" id="plusBlue" xmlns="http://www.w3.org/2000/svg" class="icon">
            <path d="M7.5,15a1,1,0,0,1-1-1V1a1,1,0,0,1,2,0V14A1,1,0,0,1,7.5,15Z" class="cls-1"></path>
            <path d="M14,8.5H1a1,1,0,0,1,0-2H14a1,1,0,0,1,0,2Z" class="cls-1"></path>
          </svg>
          <span>Add Question</span>
        </div>
      </div>

      <p v-else>Loading movie meta info...</p>
    </div>
  </div>
</template>

<script>
import {mask, TheMask} from 'vue-the-mask';
import helper from "../../../helper";

export default {
  name: "movie-options",
  props: {
    movie_meta: Object,
  },
  components: {TheMask},
  data() {
    return {

    };
  },
  directives: {mask},
  mounted() {

  },
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
  },
  methods: {
    croppedTime() {
      let play_from_sec = helper.timeStringToSeconds(this.activeSlide.fields.play_from);
      let play_to_sec = this.activeSlide.fields.play_to === '00:00:00'
              ? this.activeSlide.movie_meta.duration
              : helper.timeStringToSeconds(this.activeSlide.fields.play_to);
      let duration_sec = play_to_sec - play_from_sec;
      return helper.secondsToHumanReadable(duration_sec);
    }
  },
};
</script>

<style scoped>
.movie-item {
  display: flex;
  padding: 16px 0 19px;
  border-bottom: 1px solid #51acfd;
}
.movie-item .img-container {
  width: 110px;
  min-width: 110px;
  margin-right: 10px;
  position: relative;
}
.movie-item .img-container img {
  object-fit: cover;
  width: 100%;
  height: 100%;
  max-height: 74px;
}
.movie-item .img-container .slide-label {
  position: absolute;
  top: 0;
  left: 0;
  padding: 4px 7px;
}
.movie-item .img-container .slide-label svg {
  display: block;
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
.movie-item__time.clip {
  color: #F8DA00;
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
.btn {
  margin-left: 60px;
  margin-right: 60px;
}
.btn svg {
  transition: .2s;
}
.btn:hover svg {
  fill: #fff;
}
.btn span {
  margin-left: 5px;
}
</style> 