<template>
  <div class="video-body" id="slide-movie">  
      <div id="kalturaPlayer"></div>
      <!--<script type=application/javascript src="https://cdnapisec.kaltura.com/p/2538842/sp/253884200/embedIframeJs/uiconf_id/46602743/partner_id/2538842"> </script>-->
      <!-- <script type=application/javascript> 
          let script = document.querySelectorAll('#kalturaPlayer > script');

          script.onload = () => {
            kWidget.embed({
            targetId: "kaltura_player",
            wid: "2538842",
            uiconf_id: "46602743",
            entry_id: "1_2xb416gd",
              flashvars: {
                  "ks": "djJ8MjUzODg0Mnxj_4LdpB89uaDJ0I2b8ki1CLLbWRwcSGh2CawtNEcjHVC97MDTXtIM6NI9oplnMnY2Zf9kI1-14Prl4kJ7-sPIUtn8toBeTxIKRZpZGDXqwg==",                  
                  "applicationName": "mediaspace",
                  "playbackContext": "",
                  "disableAlerts": "false",
                  "externalInterfaceDisabled": "false",
                  "autoPlay": "false",
                  "streamerType": "auto",
                  "localizationCode": "he_IL",
                  "leadWithHTML5": "true",
                  "sideBarContainer": {"plugin": "true", "position": "left", "clickToClose": "true"},
                  "chapters": {"plugin": "true", "layout": "vertical", "thumbnailRotator": "false"},
                  "streamSelector": {"plugin": "true"},                            
              }
            })
          }
  </script> -->
  </div>
</template>

<script>
export default {
  name: "video-player",
  props: {
   movie_data: {
    type: Object,
    default() {
     return {}
    }
   },

  },
  methods: {
  },
  mounted() {
       let slide = document.getElementById('slide-movie');
       let recaptchaScript = document.createElement('script')
       recaptchaScript.setAttribute('src', 'https://cdnapisec.kaltura.com/p/2538842/sp/253884200/embedIframeJs/uiconf_id/46602743/partner_id/2538842')
       slide.prepend(recaptchaScript);
         
       recaptchaScript.onload = () => {
         this.active = true;
         console.log(this.active)
       }
  }
}
</script>

<style scoped>

</style>
