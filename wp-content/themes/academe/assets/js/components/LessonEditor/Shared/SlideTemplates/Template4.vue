<template>
  <div class="slide-template-preview">
    <div class="row">
      <div class="play-btn">
        <img
          src="data:image/png;base64,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"
        />
      </div>
      <img class="movie-thumbnail" v-if="movieThumbnail" :src="movieThumbnail" alt="Movie Thumbnail">
    </div>
  </div>
</template>

<script>
export default {
  name: "Template4",
  props: {
    kalturaId: String,
  },
  computed: {
    kalturaConfig() {
      return this.$store.state.LessonEditor.kaltura_config;
    },
    movieThumbnail() {
      if (!this.kalturaId || !this.kalturaConfig) return null;
      const height = 79;
      const width = height * 1.86; 
      const url = `https://cdnapisec.kaltura.com/p/${this.kalturaConfig.wid}/thumbnail/entry_id/${this.kalturaId}/width/${width}/height/${height}/type/1/quality/75`;
      return url;
    },
  },
};
</script>

<style scoped>
.slide-template-preview {
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
}

.movie-thumbnail {
    width: 100%;
    height: 79px;
}
</style> 