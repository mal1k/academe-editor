<template>
  <div class="video-body wrapper" id="slide-movie">
    <p class="loadingIndicator" v-if="!loaded">Loading...</p>
    <div id="kalturaPlayer"></div>
    <slot></slot>
  </div>
</template>

<script>
export default {
  name: "video-player",
  props: {
    movieData: {
      type: Object,
      required: true,
    },
    showControls: {
      type: Boolean,
      default: function() {
        return true;
      }
    }
  },
  data() {
    return {
      loaded: false,
      kdp: null,
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
          streamSelector: { plugin: "true" },
          // continue watching:
          "mediaProxy.mediaPlayFrom": this.movieData.play_from || 0,
          "mediaProxy.mediaPlayTo": this.movieData.play_to || 0,
          "Kaltura.UseAppleAdaptive": true,
          'controlBarContainer.plugin': this.showControls,
          'disableOnScreenClick': !this.showControls,
        },
        readyCallback: (playerId) => {
          this.loaded = true;
          var _this = this;
          this.kdp = document.getElementById( playerId );
          this.kdp.kBind("playerPaused", function () {
            _this.$emit('paused', _this.playerCurrentTime());
          });
          this.kdp.kBind("playerPlayed", function () {
            _this.$emit('played', _this.playerCurrentTime());
          });
        },
      });
    },
    playerCurrentTime() {
      return Math.floor(this.kdp.evaluate("{video.player.currentTime}"));
    },
    playerPlay() {
      this.kdp.sendNotification('doPlay')
    },
    playerPause() {
      this.kdp.sendNotification('doPause')
    },
    nextSegment(playTo) {
      this.kdp.setKDPAttribute('mediaProxy', 'mediaPlayFrom', this.playerCurrentTime());
      this.kdp.setKDPAttribute('mediaProxy', 'mediaPlayTo', playTo);
      this.kdp.sendNotification("doSeek", this.playerCurrentTime() - 1);
      return false;
    }

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

