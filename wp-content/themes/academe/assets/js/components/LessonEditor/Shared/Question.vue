<template>
    <div>
     <div class="row-view">
       <div v-if="store.active_question.addQuestion != false" class="btn" @click="saveQuestion('create')">
         <span>Save question</span>
       </div>
       <div v-else class="btn" @click="saveQuestion('update')">
         <span>Change question</span>
       </div>
     </div>
      <div class="row-view">
       <label>Start at</label>
       <input type="text" v-model="store.active_question.start_time" class="el-input--time el-input">
       </div>
       <div class="row-view">
        <label>Question Type</label>
        <el-select
                v-model="store.active_question.type"
                @change="changeAnswers()"
                size="medium">
            <el-option
                    v-for="(item, index) in types"
                    :key="index"
                    :label="item.text"
                    :value="item.value">
            </el-option>
        </el-select>
       </div>
       <div class="question-block" v-if="store.active_question.type === 'Open' || store.active_question.type === 'Discussion'">
        <div class="col-view">
            <label>Question Description</label>
            <el-input
                    type="textarea"
                    rows="4"
                    v-model="store.active_question.description"
                    style="width:100%">
            </el-input>
        </div>
       </div>
       <div class="question-block" v-if="store.active_question.type === 'Single Choice'">
        <div class="col-view">
            <label>Question Description</label>
            <el-input
                    type="textarea"
                    rows="4"
                    v-model="store.active_question.description"
                    style="width:100%">
            </el-input>
        </div>
        <div class="col-view">Answers</div>
        <ol class="answers">
          <li class="answer"
              v-for="(answer, index) in store.active_question.answers"
              :key="index">
            <input  type="text" 
                    class="answer-text" 
                    placeholder=" Answer..."
                    v-model="answer.text">
            <input  type="radio" 
                    class="answer-check" 
                    name="answer"
                    :value="index"
                    v-model="store.active_question.correctAnswerIndex">
            <el-dropdown trigger="click" class="more-actions">
                <img
                  src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwKSI+CjxwYXRoIGQ9Ik0xMC4wMDAxIDQuNDQ0NDhDMTEuMjI3NCA0LjQ0NDQ4IDEyLjIyMjMgMy40NDk1NSAxMi4yMjIzIDIuMjIyMjRDMTIuMjIyMyAwLjk5NDkzMSAxMS4yMjc0IDAgMTAuMDAwMSAwQzguNzcyNzYgMCA3Ljc3NzgzIDAuOTk0OTMxIDcuNzc3ODMgMi4yMjIyNEM3Ljc3NzgzIDMuNDQ5NTUgOC43NzI3NiA0LjQ0NDQ4IDEwLjAwMDEgNC40NDQ0OFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0xMC4wMDAxIDEyLjIyMjhDMTEuMjI3NCAxMi4yMjI4IDEyLjIyMjMgMTEuMjI3OSAxMi4yMjIzIDEwLjAwMDZDMTIuMjIyMyA4Ljc3MzI1IDExLjIyNzQgNy43NzgzMiAxMC4wMDAxIDcuNzc4MzJDOC43NzI3NiA3Ljc3ODMyIDcuNzc3ODMgOC43NzMyNSA3Ljc3NzgzIDEwLjAwMDZDNy43Nzc4MyAxMS4yMjc5IDguNzcyNzYgMTIuMjIyOCAxMC4wMDAxIDEyLjIyMjhaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTAuMDAwMSAyMC4wMDAxQzExLjIyNzQgMjAuMDAwMSAxMi4yMjIzIDE5LjAwNTIgMTIuMjIyMyAxNy43Nzc5QzEyLjIyMjMgMTYuNTUwNiAxMS4yMjc0IDE1LjU1NTcgMTAuMDAwMSAxNS41NTU3QzguNzcyNzYgMTUuNTU1NyA3Ljc3NzgzIDE2LjU1MDYgNy43Nzc4MyAxNy43Nzc5QzcuNzc3ODMgMTkuMDA1MiA4Ljc3Mjc2IDIwLjAwMDEgMTAuMDAwMSAyMC4wMDAxWiIgZmlsbD0id2hpdGUiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMCI+CjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0id2hpdGUiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K"
                />
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>Duplicate</el-dropdown-item>
                  <el-dropdown-item>Hide</el-dropdown-item>
                  <el-dropdown-item>Delete</el-dropdown-item
                  >
                </el-dropdown-menu>
              </el-dropdown>
          </li>
        </ol>
        <button class="add-answer" @click="addSingleanswer()">Add Answer</button>
       </div>
       <div class="question-block" v-if="store.active_question.type === 'Multiple Choice'">
        <div class="col-view">
            <label>Question Description</label>
            <el-input
                    type="textarea"
                    rows="4"
                    v-model="store.active_question.description"
                    style="width:100%">
            </el-input>
        </div>
        <div class="col-view">Answers</div>
        <ol class="answers">
          <li class="answer"
              v-for="(answer, index) in store.active_question.answers"
              :key="index">
            <input  type="text" 
                    class="answer-text" 
                    placeholder=" Answer..."
                    v-model="answer.text">
            <input  type="checkbox" 
                    class="answer-check" 
                    :value="index"
                    v-model="store.active_question.checkedItems[index]">
            <el-dropdown trigger="click" class="more-actions">
                <img
                  src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwKSI+CjxwYXRoIGQ9Ik0xMC4wMDAxIDQuNDQ0NDhDMTEuMjI3NCA0LjQ0NDQ4IDEyLjIyMjMgMy40NDk1NSAxMi4yMjIzIDIuMjIyMjRDMTIuMjIyMyAwLjk5NDkzMSAxMS4yMjc0IDAgMTAuMDAwMSAwQzguNzcyNzYgMCA3Ljc3NzgzIDAuOTk0OTMxIDcuNzc3ODMgMi4yMjIyNEM3Ljc3NzgzIDMuNDQ5NTUgOC43NzI3NiA0LjQ0NDQ4IDEwLjAwMDEgNC40NDQ0OFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0xMC4wMDAxIDEyLjIyMjhDMTEuMjI3NCAxMi4yMjI4IDEyLjIyMjMgMTEuMjI3OSAxMi4yMjIzIDEwLjAwMDZDMTIuMjIyMyA4Ljc3MzI1IDExLjIyNzQgNy43NzgzMiAxMC4wMDAxIDcuNzc4MzJDOC43NzI3NiA3Ljc3ODMyIDcuNzc3ODMgOC43NzMyNSA3Ljc3NzgzIDEwLjAwMDZDNy43Nzc4MyAxMS4yMjc5IDguNzcyNzYgMTIuMjIyOCAxMC4wMDAxIDEyLjIyMjhaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTAuMDAwMSAyMC4wMDAxQzExLjIyNzQgMjAuMDAwMSAxMi4yMjIzIDE5LjAwNTIgMTIuMjIyMyAxNy43Nzc5QzEyLjIyMjMgMTYuNTUwNiAxMS4yMjc0IDE1LjU1NTcgMTAuMDAwMSAxNS41NTU3QzguNzcyNzYgMTUuNTU1NyA3Ljc3NzgzIDE2LjU1MDYgNy43Nzc4MyAxNy43Nzc5QzcuNzc3ODMgMTkuMDA1MiA4Ljc3Mjc2IDIwLjAwMDEgMTAuMDAwMSAyMC4wMDAxWiIgZmlsbD0id2hpdGUiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMCI+CjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0id2hpdGUiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K"
                />
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>Duplicate</el-dropdown-item>
                  <el-dropdown-item>Hide</el-dropdown-item>
                  <el-dropdown-item>Delete</el-dropdown-item
                  >
                </el-dropdown-menu>
              </el-dropdown>
          </li>
        </ol>
        <button class="add-answer" @click="addSingleanswer()">Add Answer</button>
       </div>
       <div class="question-block" v-if="store.active_question.type === 'True / False'">
        <div class="col-view">
            <label>Question Description</label>
            <el-input
                    type="textarea"
                    rows="4"
                    v-model="store.active_question.description"
                    style="width:100%">
            </el-input>
        </div>
        <div class="col-view">Answers</div>
        <ol class="answers">
          <li class="answer">
            <input  type="text" 
                    class="answer-text" 
                    value="True"
                    readonly>
            <input  type="radio" 
                    class="answer-check" 
                    value="1"
                    name="answer"
                    v-model="store.active_question.questionIndex">
          </li>
          <li class="answer">
            <input  type="text" 
                    class="answer-text" 
                    value="False"
                    readonly>
            <input  type="radio" 
                    class="answer-check" 
                    value="0"
                    name="answer"
                    v-model="store.active_question.questionIndex">
          </li>
        </ol>
       </div>
        <div class="question-block" v-if="store.active_question.type === 'Poll'">
        <div class="col-view">
            <label>Question Description</label>
            <el-input
                    type="textarea"
                    rows="4"
                    v-model="store.active_question.description"
                    style="width:100%">
            </el-input>
        </div>
        <div class="col-view">Answers</div>
        <ol class="answers">
          <li class="answer"
              v-for="(answer, index) in store.active_question.answers"
              :key="index">
            <input  type="text" 
                    class="answer-text" 
                    placeholder=" Answer..."
                    v-model="answer.text">
            <el-dropdown trigger="click" class="more-actions">
                <img
                  src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwKSI+CjxwYXRoIGQ9Ik0xMC4wMDAxIDQuNDQ0NDhDMTEuMjI3NCA0LjQ0NDQ4IDEyLjIyMjMgMy40NDk1NSAxMi4yMjIzIDIuMjIyMjRDMTIuMjIyMyAwLjk5NDkzMSAxMS4yMjc0IDAgMTAuMDAwMSAwQzguNzcyNzYgMCA3Ljc3NzgzIDAuOTk0OTMxIDcuNzc3ODMgMi4yMjIyNEM3Ljc3NzgzIDMuNDQ5NTUgOC43NzI3NiA0LjQ0NDQ4IDEwLjAwMDEgNC40NDQ0OFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0xMC4wMDAxIDEyLjIyMjhDMTEuMjI3NCAxMi4yMjI4IDEyLjIyMjMgMTEuMjI3OSAxMi4yMjIzIDEwLjAwMDZDMTIuMjIyMyA4Ljc3MzI1IDExLjIyNzQgNy43NzgzMiAxMC4wMDAxIDcuNzc4MzJDOC43NzI3NiA3Ljc3ODMyIDcuNzc3ODMgOC43NzMyNSA3Ljc3NzgzIDEwLjAwMDZDNy43Nzc4MyAxMS4yMjc5IDguNzcyNzYgMTIuMjIyOCAxMC4wMDAxIDEyLjIyMjhaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTAuMDAwMSAyMC4wMDAxQzExLjIyNzQgMjAuMDAwMSAxMi4yMjIzIDE5LjAwNTIgMTIuMjIyMyAxNy43Nzc5QzEyLjIyMjMgMTYuNTUwNiAxMS4yMjc0IDE1LjU1NTcgMTAuMDAwMSAxNS41NTU3QzguNzcyNzYgMTUuNTU1NyA3Ljc3NzgzIDE2LjU1MDYgNy43Nzc4MyAxNy43Nzc5QzcuNzc3ODMgMTkuMDA1MiA4Ljc3Mjc2IDIwLjAwMDEgMTAuMDAwMSAyMC4wMDAxWiIgZmlsbD0id2hpdGUiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMCI+CjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0id2hpdGUiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K"
                />
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>Duplicate</el-dropdown-item>
                  <el-dropdown-item>Hide</el-dropdown-item>
                  <el-dropdown-item>Delete</el-dropdown-item
                  >
                </el-dropdown-menu>
              </el-dropdown>
          </li>
        </ol>
        <button class="add-answer" @click="addSingleanswer()">Add Answer</button>
       </div>

       <div class="question-block" v-if="store.active_question.type != 'Discussion'">
        <div class="row-view">
         <label>Score</label>
         <input type="text" v-model="store.active_question.score" class="el-input--time el-input">
        </div>
        <div class="row-view">
         <button class="advanced-arrow" :class="{ active: showAdvanced }" @click="toggleAdvanced()">Advanced Configuration</button>
        </div>
        <div v-if="showAdvanced">
         <div class="row-view">
          <label>Time Limit</label>
          <el-select
                  v-model="store.active_question.limit"
                  size="medium">
              <el-option
                      v-for="(item, index) in times"
                      :key="index"
                      :label="item.time"
                      :value="item.value">
              </el-option>
          </el-select>
         </div>
         <div class="row-view">
          <label>Allow Skipping</label>
          <label class="switch">
            <input type="checkbox" v-model="store.active_question.skipping">
            <span class="slider"></span>
          </label>
         </div>
         <div class="row-view">
          <label>Show Correct Answer</label>
          <label class="switch">
            <input type="checkbox" store.active_question.show_correct_answer>
            <span class="slider"></span>
          </label>
         </div>
        </div>
       </div>
    </div>
</template>

<script>
    import axios from "axios";

    export default {
        name: "Question",
        props: {
            field: Object,
        },
        data() {
            return {
              selected: '',
              types: [
                { text: 'Open', value: 'Open' },
                { text: 'Single Choice', value: 'Single Choice' },
                { text: 'Multiple Choice', value: 'Multiple Choice' },
                { text: 'True / False', value: 'True / False' },
                { text: 'Poll', value: 'Poll' },
                { text: 'Discussion', value: 'Discussion' },
              ],
              times: [
               {time: 'No Time Limit', value: ''},
               {time: '10 Sec', value: '10'},
               {time: '20 Sec', value: '20'},
               {time: '30 Sec', value: '30'},
              ],
              showAdvanced: false,
              addQuestion: true,
              correctanswerIndex: '',
              checkedItems: [],
            };
        },
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            },
        },
        methods: {
         toggleAdvanced(){
          this.showAdvanced = !this.showAdvanced
         },
         saveQuestion(action){ // action: create/update
            let newQuestion = this.store.active_question;

            // Prepare question data to save:
            var answer_data = [];
            var answer_type = "";
            switch (newQuestion.type) {
                case "Single Choice":
                    answer_type = 'single';
                    newQuestion.answers.forEach((answer, index) => {
                        let answer_params = {
                            _answer: answer.text,
                            _correct: index === newQuestion.correctAnswerIndex,
                            _graded: "1",
                            _gradedType: "text",
                            _gradingProgression: "not-graded-none",
                            _html: false,
                            _points: (index === newQuestion.correctAnswerIndex) ? parseInt(newQuestion.score) : 0,
                            _sortString: "",
                            _sortStringHtml: false,
                            _type: "answer",
                        };
                        answer_data.push(answer_params);
                    });
                    break;
                case "Open":
                    answer_type = 'essay';
                    let answer_params = {
                        _answer: "",
                        _correct: false,
                        _graded: "1",
                        _gradedType: "text",
                        _gradingProgression: "not-graded-none",
                        _html: false,
                        _points: parseInt(newQuestion.score),
                        _sortString: "",
                        _sortStringHtml: false,
                        _type: "answer",
                    };
                    answer_data.push(answer_params);
                    break;
            }

            // Save question data (and answers)
            axios.post("/ldlms/v1/sfwd-questions/"+newQuestion.question_id, {
                _quizId: newQuestion.quiz_id,
                _question: newQuestion.description,
                _answerData: answer_data,
                _answerType: answer_type,
                _points: parseInt(newQuestion.score),
            });

             // Update ACF fields (show at):
             axios.post("/acf/v3/sfwd-quiz/"+newQuestion.quiz_id, {
                 fields: {
                     show_at: newQuestion.start_time,
                 }
             });
            if(action === 'create') {
                this.store.questions.push(newQuestion);
            }
            this.store.active_question = null;
         },
         changeAnswers() {
           this.store.active_question.answers = [];
           this.store.active_question.checkedItems = [];
           this.store.active_question.correctAnswerIndex = '';
           let singleAnswer = {text: '', value: 'false'};
           let firstAnswer = {text: '', value: ''};
           let falseTrueAnswer = [{text: 'True', value: ''},{text: 'False', value: ''}]

            if(  this.store.active_question.type === 'Single Choice') {
                  this.store.active_question.answers.push(singleAnswer);
                }
            if(  this.store.active_question.type === 'Multiple Choice' ||
                  this.store.active_question.type === 'Poll') {
                  this.store.active_question.answers.push(firstAnswer);
                }
            if( this.store.active_question.type === 'True / False'){
              this.store.active_question.answers = falseTrueAnswer;
            } 
         },
         addSingleanswer() {
           let singleanswer = {text: '', value: ''}
           this.store.active_question.answers.push(singleanswer);           
         },
        },
    }
</script>

<style scoped>
    .row-view, .col-view {
        margin-bottom: 30px;
    }
    .row-view {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .col-view label {
        margin-bottom: 10px;
        display: block;
    }
    .row-view label {
        display: block;
        flex-grow: 1;
    }
    .el-input {
     -webkit-appearance: none;
     background-color: #2f2f2f;
     background-image: none;
     border-radius: 0px;
     border: 1px solid #FFFFFF;
     box-sizing: border-box;
     color: #FFFFFF;
     display: inline-block;
     font-size: 16px;
     height: 34px;
     line-height: 34px;
     outline: none;
     padding: 0 20px;
     transition: border-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
     text-align: center;
    }
    .el-input--time {
     width: 120px;
     max-width: 100%;
     text-align: center;
    }
    .question-block {
     border-top: 1px solid rgb(81, 172, 253);
     padding-top: 22px;
     padding-bottom: 19px;
    }
    .advanced-arrow {
     display: flex;
     align-items: center;
     font-style: normal;
     font-weight: normal;
     font-size: 16px;
     line-height: 20px;
     color: #FFFFFF;
     background-color: transparent;
     border: none;
     cursor: pointer;
    }
    .advanced-arrow::before {
     content: "";
     margin-right: 10px;
     border: solid #fff;
     border-width: 0 3px 3px 0;
     display: inline-block;
     padding: 3px;
     transform: rotate(-45deg);
     -webkit-transform: rotate(-45deg);
     transition: all .3s ease;
    }
    .advanced-arrow.active::before {
     transform: rotate(45deg);
    -webkit-transform: rotate(45deg)
    }

   label.switch {
     position: relative;
     display: inline-block;
     max-width: 24px;
     width: 24px;
     height: 13px;
   }
   .switch input {
     opacity: 0;
     width: 0;
     height: 0;
   }
  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    border-radius: 7px;
  }

  .slider:before {
  position: absolute;
  content: "";
  height: 9px;
  width: 9px;
  border-radius: 50%;
  left: 3px;
  bottom: 2px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
  }

  input:checked + .slider {
    background-color: #51ACFD;
  }

  input:focus + .slider {
    box-shadow: 0 0 1px #51ACFD;
  }

  input:checked + .slider:before {
    -webkit-transform: translateX(9px);
    -ms-transform: translateX(9px);
    transform: translateX(9px);
  }
  .btn {
  border: 1px solid #51acfd;
  border-radius: 3px;
  padding: 6px 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.2s;
  color: #51acfd;
  font-weight: 600;
}
.btn:hover {
  background-color: #51acfd;
  color: #fff;
}
.answers {
   list-style-type: lower-alpha;
}
.answer {
  margin-bottom: 10px;
  display: flex;
  align-items: center;
}
.answer-text {
  margin: 4px 6px;
  flex-grow: 1;
  height: 34px;
  padding: 8px 14px;
  font-style: normal;
  font-weight: normal;
  font-size: 14px;
  line-height: 17px;
  color: #fff;
  border: 1px solid #C4C4C4;
  background-color: #2F2F2F;
}
.answer-check {
  margin: 0 10px;
}
.add-answer {
  font-style: normal;
  font-weight: normal;
  font-size: 14px;
  line-height: 17px;
  color: #51ACFD;
  background-color: transparent;
  border: none;
  cursor: pointer;
}
</style>
