<template>
   <div class="question-view" v-if="question_data && question_data.type != ''">
    <div class="question-view__body" v-if="question_data.type ==='Open' || question_data.type ==='Discussion'">
     <div class="question-view__text">
      {{question_data.description}}
     </div>
     <div class="question-view__answer">
      <textarea class="question-view__textarea" rows="4" placeholder="Your Answer..."></textarea>
     </div>
    </div>
    <div class="question-view__body" v-if="question_data.type === 'Single Choice' || question_data.type === 'True / False' || question_data.type === 'Poll'">
     <div class="question-view__text">
      {{question_data.description}}
     </div>
     <div class="question-view__answer">
      <ul>
       <li v-for="(answer, index) in question_data.answers" :key="index">
        <label class="radio-item"> 
         <input type="radio" name="answer">
         <span class="checkmark"></span>
          {{answer.text}}
        </label>
       </li>
      </ul>
     </div>
    </div>
    <div class="question-view__body" v-if="question_data.type === 'Multiple Choice'">
     <div class="question-view__text">
      {{question_data.description}}
     </div>
     <div class="question-view__answer">
      <ul>
       <li v-for="(answer, index) in question_data.answers" :key="index">
        <label class="checkbox-item"> 
         <input type="checkbox" :value="answer.text">
         <span class="checkmark"></span>
          {{answer.text}}
        </label>
       </li>
      </ul>
     </div>
    </div>
    <!--<div class="question-view__footer">
     <button class="question-view__link">Skip >></button>
     <button class="question-view__btn">Submit</button>
    </div>-->
   </div>
</template>

<script>
    export default {
        name: "slide-question-template",
        props: {
        question_data: {
          type: Object,
          default() {
            return {};
          },
        },
      },
    }
</script>

<style scoped>
 .question-view {
  padding: 25px 26px 33px 39px;
  width: 100%;
  height: 100%;
  /*background: rgba(81, 172, 253, 0.8);*/
  position: relative;
  display: flex;
  flex-direction: column;
 }
 .question-view__close {
  position: absolute;
  right: 17px;
  top: 13px;
  cursor: pointer;
 }
 .question-view__body {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  align-items: center;
  justify-content: center;
 }
 .question-view__text {
  width: 80%;
  display: flex;
  color: #fff;
  font-weight: bold;
  font-size: 28px;
  line-height: 37px;
  position: relative;
  word-break: break-word;
  margin-bottom: 20px;
 }
 .question-view__answer {
  width: 80%;
  display: flex;
  color: #fff;
  font-weight: bold;
  font-size: 28px;
  line-height: 37px;
  position: relative;
  overflow-y: auto;
 }
 .question-view__answer ul {
  background: rgba(0, 104, 178, 0.9);
  border-radius: 12px;
  padding: 30px;
  width: 100%;
 }
 .question-view__footer {
  margin-top: 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  z-index: 1;
 }
 .question-view__btn {
  padding: 9px 30px;
  background: #FFFFFF;
  border: none;
  border-radius: 4px;
  font-style: normal;
  font-weight: 500;
  font-size: 20.7734px;
  line-height: 25px;
  letter-spacing: 0.129834px;
  color: #51ACFD; 
  cursor: pointer;
 }
 .question-view__link {
  font-style: normal;
  font-weight: 500;
  font-size: 20.7734px;
  line-height: 25px;
  letter-spacing: 0.129834px;
  color: #FFFFFF;
  cursor: pointer;
  background: transparent;
  border: none;
 }
 .question-view__textarea {
  padding: 11px 18px;
  width: 100%;
  font-style: normal;
  font-weight: 500;
  font-size: 23px;
  line-height: 28px;
  color: #FFFFFF;
  background: rgba(0, 104, 178, 0.9);
  opacity: 0.9;
  border-radius: 12px;
  border: none;
  resize: none;
 }
 .question-view__textarea::-webkit-input-placeholder {
  color: #FFFFFF;
}
.question-view__textarea::-moz-placeholder { 
  color: #FFFFFF;
}
.question-view__textarea:-ms-input-placeholder { 
  color: #FFFFFF;
}
.question-view__textarea:-moz-placeholder { 
  color: #FFFFFF;
}

.radio-item {
  display: block;
  position: relative;
  padding-left: 60px;
  margin: 12px 0;
  cursor: pointer;
  font-size: 23px;
  line-height: 30px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.radio-item input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}
.radio-item .checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 30px;
  width: 30px;
  border: 2px solid #FFFFFF;
  background-color: transparent;
  border-radius: 50%;
}
.radio-item .checkmark:after {
  content: "";
  position: absolute;
  display: none;
}
.radio-item input:checked ~ .checkmark:after {
  display: block;
}
.radio-item .checkmark:after {
  top: 4px;
  left: 4px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: white;
}

.checkbox-item {
  display: block;
  position: relative;
  padding-left: 60px;
  margin: 12px 0;
  cursor: pointer;
  font-size: 23px;
  line-height: 30px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}
.checkbox-item input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}
.checkbox-item .checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 28px;
  width: 28px;
  background-color: transparent;
  border: 1px solid #fff;
}
.checkbox-item .checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

.checkbox-item input:checked ~ .checkmark:after {
  display: block;
}

.checkbox-item .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
