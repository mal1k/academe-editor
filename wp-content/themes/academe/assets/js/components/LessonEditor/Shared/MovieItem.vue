<template>
  <div class="movie-item">
    <img :src="movie_data.image" alt="img" />
    <div class="movie-item__body">
      <div class="movie-item__header">
        <span class="movie-item__name">{{ movie_data.title }}</span>
        <span class="movie-item__time">{{ movie_data.time }}</span>
      </div>
      
      <div v-if="movie_data.tags" class="movie-item__tags">
        <a
          href="#"
          class="movie-item__tag"
          v-for="(tag, index) in movie_data.tags.slice(0, 2)"
          :key="index"
          >#{{ tag.tag }}
        </a>
      </div>


      <div class="movie-item__footer">
        <button class="movie-item__btn" @click="$emit('add-click')">Add</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "movie-item",
  props: {
    movie_data: {
      type: Object,
      default() {
        return {};
      },
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
.movie-item__btn {
  padding: 4px 16px;
  background: #51acfd;
  border-radius: 5px;
  font-family: Montserrat;
  font-style: normal;
  font-weight: 500;
  font-size: 14px;
  line-height: 18px;
  letter-spacing: 0.175px;
  color: #ffffff;
  cursor: pointer;
  transition: all 0.3s ease;
}
.movie-item__btn:hover {
  background: #4891d1;
}
</style>
 