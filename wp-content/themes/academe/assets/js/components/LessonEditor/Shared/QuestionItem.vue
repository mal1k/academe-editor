<template>
  <div class="quesyion-item" @click="$emit('show-question')">
    <div class="quesyion-item__index">{{ number + 1 }}</div>
    <div class="quesyion-item__body">
      <div class="quesyion-item__time">{{questionTimeString}}</div>
      
      <div class="quesyion-item__type">{{ question_data.type }} Question</div>
      <div class="quesyion-item__text">
        <span>Q:</span>{{ question_data.description }}
      </div>
    </div>
    <div class="nav-buttons">
     <button class="nav-button" @click="$emit('change-question')">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
       <path d="M11.5 1.62132C11.8978 1.2235 12.4374 1 13 1C13.5626 1 14.1022 1.2235 14.5 1.62132C14.8978 2.01915 15.1213 2.55871 15.1213 3.12132C15.1213 3.68393 14.8978 4.2235 14.5 4.62132L5 14.1213L1 15.1213L2 11.1213L11.5 1.62132Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
     </button>
     <button class="nav-button" >
       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
       <g clip-path="url(#clip0)">
       <path d="M19.9259 9.76278C19.7684 9.5336 15.9834 4.16602 9.99995 4.16602C4.86569 4.16602 0.29062 9.50278 0.0981168 9.73028C-0.0327056 9.88528 -0.0327056 10.1128 0.0981168 10.2686C0.29062 10.4961 4.86569 15.8329 9.99995 15.8329C15.1342 15.8329 19.7092 10.4961 19.9017 10.2686C20.0226 10.1253 20.0334 9.91779 19.9259 9.76278ZM9.99995 14.9995C5.88407 14.9995 1.97065 11.0745 0.97649 9.99947C1.96901 8.92363 5.87825 4.99939 9.99995 4.99939C14.8158 4.99939 18.2151 8.91863 19.0451 9.97697C18.0867 11.0178 14.1508 14.9995 9.99995 14.9995Z" fill="white"/>
       <path d="M9.9994 6.66602C8.16104 6.66602 6.66602 8.16104 6.66602 9.9994C6.66602 11.8377 8.16104 13.3328 9.9994 13.3328C11.8377 13.3328 13.3328 11.8377 13.3328 9.9994C13.3328 8.16104 11.8377 6.66602 9.9994 6.66602ZM9.9994 12.4995C8.62105 12.4995 7.49935 11.3778 7.49935 9.99943C7.49935 8.62109 8.62105 7.49939 9.9994 7.49939C11.3777 7.49939 12.4994 8.62109 12.4994 9.99943C12.4994 11.3778 11.3777 12.4995 9.9994 12.4995Z" fill="white"/>
       </g>
       <defs>
       <clipPath id="clip0">
       <rect width="20" height="20" fill="white"/>
       </clipPath>
       </defs>
       </svg>
     </button>
     <button class="nav-button"  @click="$emit('remove-question')">
       <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
       <path d="M10.0003 18.3327C14.6027 18.3327 18.3337 14.6017 18.3337 9.99935C18.3337 5.39698 14.6027 1.66602 10.0003 1.66602C5.39795 1.66602 1.66699 5.39698 1.66699 9.99935C1.66699 14.6017 5.39795 18.3327 10.0003 18.3327Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
       <path d="M12.5 7.5L7.5 12.5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
       <path d="M7.5 7.5L12.5 12.5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
       </svg>
     </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "quesyion-item",
  props: {
    number: Number,
    question_data: {
      type: Object,
      default() {
        return {};
      },
    },
  },

computed: {
    questionTimeString() {
      const sec_num = parseInt(this.question_data.start_time, 10);
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
  }

};
</script>

<style scoped>
.quesyion-item:first-child {
 margin-top: 24px;
  border-top: 1px solid rgb(81, 172, 253);
}
.quesyion-item {
  border-bottom: 1px solid rgb(81, 172, 253);
  padding: 17px 5px 25px;
  display: flex;
  position: relative;
}
.quesyion-item:hover {
 background: rgba(81, 172, 253, 0.24);
}
.quesyion-item:hover .nav-buttons {
 opacity: 1;
}
.nav-buttons {
    position: absolute;
    right: 5px;
    top: 16px;
    display: flex;
    align-items: center;
    opacity: 0;
    transition: all .3s ease;
}
.nav-button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    padding: 0 5px;
}
.quesyion-item__index {
 min-width: 20px;
}
.quesyion-item__index,
.quesyion-item__time {
 margin-bottom: 6px;
  font-style: normal;
  font-weight: 500;
  font-size: 13px;
  line-height: 18px;
  letter-spacing: 0.175px;
  color: #FFFFFF;
 }
 .quesyion-item__type {
   margin-bottom: 6px;
  font-style: italic;
  font-weight: 300;
  font-size: 14px;
  line-height: 18px;
  letter-spacing: 0.175px;
  color: #51ACFD;
 }
 .quesyion-item__text {
  font-style: normal;
  font-size: 14px;
  line-height: 18px;
  letter-spacing: 0.175px;
  color: #FFFFFF;
 }
 .quesyion-item__text span {
  margin-right: 5px;
  font-weight: bold;
 }
</style>
