<template>
  <div class="video-body wrapper" id="slide-movie">
    <p class="loadingIndicator" v-if="!loaded">Loading...</p>
    <div id="kalturaPlayer"></div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "video-player",
  props: {
    movieData: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      loaded: false,
    };
  },
  computed: {
    kalturaConfig() {
      return this.$store.state.LessonEditor.kaltura_config;
    }
  },
  mounted() {
      if (!this.kalturaConfig) {
        this.getKalturaConfig();
      } else {
        this.initKalturaPlayer();
      }
  },
  beforeDestroy() {
    kWidget.destroy("videoPlayer");
  },
  methods: {
    getKalturaConfig() {
        this.$store.dispatch('LessonEditor/getKalturaConfig')
    },
    initKalturaPlayer() {
      kWidget.embed({
        targetId: "kalturaPlayer",
        wid: "_" + this.kalturaConfig.wid,
        uiconf_id: this.kalturaConfig.uiconf_id,
        entry_id: this.movieData.kaltura_id,
        flashvars: {
          ks: this.kalturaConfig.ks,
          applicationName: "mediaspace",
          playbackContext: "",
          disableAlerts: "false",
          externalInterfaceDisabled: "false",
          //"autoPlay": "true",
          //"autoMute": false,
          streamerType: "auto",
          localizationCode: "en_GB",
          leadWithHTML5: "true",
          sideBarContainer: {
            plugin: "true",
            position: "left",
            clickToClose: "true",
          },
          chapters: {
            plugin: "true",
            layout: "vertical",
            thumbnailRotator: "false",
          },
          streamSelector: { plugin: "true" },
          // continue watching:
          "mediaProxy.mediaPlayFrom": this.movieData.play_from || 0,
          "mediaProxy.mediaPlayTo": this.movieData.play_to || 0,
          "Kaltura.UseAppleAdaptive": true,
        },
        readyCallback: (playerId) => {
          this.loaded = true;
          // $("#kalturaPlayer").data("loaded", true);
          // window.kdp = document.getElementById(playerId);

          // setTimeout(function () {
          //   window.kdp.sendNotification("doPlay");
          // }, 300);

          // if (data_obj.data("mode") === "advanced") {
          //   window.kdp.kBind("doPause", function () {
          //     throttle(updateContinueWatchingList, 1000);
          //   });
          //   kdp.kBind("playerUpdatePlayhead.fullMovie", function (data, id) {
          //     if (parseInt(data) % 60 === 0) {
          //       //call only if current player time multiple of 60
          //       throttle(updateContinueWatchingList, 1000);
          //     }
          //   });
          //   window.kdp.kBind("playerPlayEnd", function () {});
          //   $(window).on("beforeunload", function () {
          //     throttle(updateContinueWatchingList, 1000);
          //     return undefined;
          //   });
          // }

          // $('.modal.movie-player .close').on('click', function () {
          //     window.kdp.sendNotification('doPause');
          // });
        },
      });
    },
  },
  watch: {
    kalturaConfig() {
      this.initKalturaPlayer();
    },
    movieData() {
      if (this.kalturaConfig) {
        this.initKalturaPlayer();
      }
    }
  }
};
</script>

<style scoped>
.wrapper {
  position: relative;
}
.loadingIndicator {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
