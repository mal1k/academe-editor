<template>
  <div class="slides-content">
    <div class="slide-list">
      <div class="slide-container">
        <div @click="new_slide_modal = true" class="btn">
          <svg
            class="icon"
            width="15"
            height="15"
            viewBox="0 0 15 15"
            fill="#51acfd"
            id="plusBlue"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              class="cls-1"
              d="M7.5,15a1,1,0,0,1-1-1V1a1,1,0,0,1,2,0V14A1,1,0,0,1,7.5,15Z"
            ></path>
            <path
              class="cls-1"
              d="M14,8.5H1a1,1,0,0,1,0-2H14a1,1,0,0,1,0,2Z"
            ></path>
          </svg>
          <span>Add New Slide</span>
        </div>
      </div>
      <draggable
        v-model="store.slides"
        v-bind="dragOptions"
        @start="drag = true"
        @end="drag = false"
        handle=".draggable"
      >
        <transition-group type="transition" :name="!drag ? 'flip-list' : null">
          <div
            v-for="(slide, index) in store.slides"
            :key="slide.lesson_id"
            class="slide-container slide-preview-wrap"
            :class="{
              active: store.active_slide === parseInt(slide.lesson_id),
            }"
            @click="changeActiveSlide(slide)"
          >
            <div class="slide-number">
              <span>{{ parseInt(index) + 1 }}</span>
            </div>
            <div
              class="slide-preview flex-builder"
              :class="{
                active: store.active_slide === parseInt(slide.lesson_id),
              }"
            >
              <template v-if="slide.slide_type === 'text_image'">
                <template1 v-if="slide.template === 'template1'" />
                <template2 v-if="slide.template === 'template2'" />
                <template3 v-if="slide.template === 'template3'" />
              </template>
              <template v-if="slide.slide_type === 'movie'">
                <template4 v-if="slide.template === 'template4'" :kalturaId="slide.fields && slide.fields.kaltura_id"  />
              </template>
              <template v-if="slide.slide_type === 'question'"> <template5/> </template>
              <div class="draggable">
                <img
                  alt=""
                  src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNiIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDYgMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNDNEM0QzQiLz4KPHJlY3QgeD0iNCIgd2lkdGg9IjIiIGhlaWdodD0iMiIgZmlsbD0iI0M0QzRDNCIvPgo8cmVjdCB5PSI2IiB3aWR0aD0iMiIgaGVpZ2h0PSIyIiBmaWxsPSIjQzRDNEM0Ii8+CjxyZWN0IHg9IjQiIHk9IjYiIHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNDNEM0QzQiLz4KPHJlY3QgeT0iMTIiIHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNDNEM0QzQiLz4KPHJlY3QgeD0iNCIgeT0iMTIiIHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNDNEM0QzQiLz4KPHJlY3QgeT0iMTgiIHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNDNEM0QzQiLz4KPHJlY3QgeD0iNCIgeT0iMTgiIHdpZHRoPSIyIiBoZWlnaHQ9IjIiIGZpbGw9IiNDNEM0QzQiLz4KPC9zdmc+Cg=="
                />
              </div>
              <el-dropdown trigger="click" class="more-actions">
                <img
                  src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwKSI+CjxwYXRoIGQ9Ik0xMC4wMDAxIDQuNDQ0NDhDMTEuMjI3NCA0LjQ0NDQ4IDEyLjIyMjMgMy40NDk1NSAxMi4yMjIzIDIuMjIyMjRDMTIuMjIyMyAwLjk5NDkzMSAxMS4yMjc0IDAgMTAuMDAwMSAwQzguNzcyNzYgMCA3Ljc3NzgzIDAuOTk0OTMxIDcuNzc3ODMgMi4yMjIyNEM3Ljc3NzgzIDMuNDQ5NTUgOC43NzI3NiA0LjQ0NDQ4IDEwLjAwMDEgNC40NDQ0OFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0xMC4wMDAxIDEyLjIyMjhDMTEuMjI3NCAxMi4yMjI4IDEyLjIyMjMgMTEuMjI3OSAxMi4yMjIzIDEwLjAwMDZDMTIuMjIyMyA4Ljc3MzI1IDExLjIyNzQgNy43NzgzMiAxMC4wMDAxIDcuNzc4MzJDOC43NzI3NiA3Ljc3ODMyIDcuNzc3ODMgOC43NzMyNSA3Ljc3NzgzIDEwLjAwMDZDNy43Nzc4MyAxMS4yMjc5IDguNzcyNzYgMTIuMjIyOCAxMC4wMDAxIDEyLjIyMjhaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTAuMDAwMSAyMC4wMDAxQzExLjIyNzQgMjAuMDAwMSAxMi4yMjIzIDE5LjAwNTIgMTIuMjIyMyAxNy43Nzc5QzEyLjIyMjMgMTYuNTUwNiAxMS4yMjc0IDE1LjU1NTcgMTAuMDAwMSAxNS41NTU3QzguNzcyNzYgMTUuNTU1NyA3Ljc3NzgzIDE2LjU1MDYgNy43Nzc4MyAxNy43Nzc5QzcuNzc3ODMgMTkuMDA1MiA4Ljc3Mjc2IDIwLjAwMDEgMTAuMDAwMSAyMC4wMDAxWiIgZmlsbD0id2hpdGUiLz4KPC9nPgo8ZGVmcz4KPGNsaXBQYXRoIGlkPSJjbGlwMCI+CjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0id2hpdGUiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K"
                />
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item @click.native="duplicateSlide(parseInt(index))">Duplicate</el-dropdown-item>
                  <el-dropdown-item  @click.native="removeSlide(parseInt(index))">Delete</el-dropdown-item
                  >
                </el-dropdown-menu>
              </el-dropdown>
            </div>
          </div>
        </transition-group>
      </draggable>
    </div>

    <div class="slide-content">
      <div class="aspect-ratio-box">
        <div class="aspect-ratio-box-inside">
          <template v-if="activeSlide">
            <template v-if="activeSlide.template === 'template1'">
              <div class="slide-template-preview flex-builder">
                <div class="row h-30">
                  <div
                    class="col w-100 text-title"
                    :style="{
                      backgroundColor:             activeSlide.fields.template1_text1_fill_color,}"
                    @click="
                      changeActiveBlockMeta(                        store.slide_templates.template1.template1_text1)">
                    <div
                      v-if="activeSlide.fields.template1_text1_text == null"
                      class="content-type">
                      Text
                    </div>
                    <div
                      v-else
                      class="formatted-text"
                      :style="{
                        fontFamily: activeSlide.fields.template1_text1_font,
                        fontSize: activeSlide.fields.template1_text1_font_size,
                        lineHeight: '1.1em',
                        color: activeSlide.fields.template1_text1_text_color,
                        fontWeight:
                          activeSlide.fields.template1_text1_font_weight,
                        textAlign:
                          activeSlide.fields.template1_text1_text_align,
                      }"
                      v-html="activeSlide.fields.template1_text1_text"
                    ></div>
                    <slide-block-corners
                      v-if="
                        checkActiveBlockMeta(
                          store.slide_templates.template1.template1_text1
                        )
                      "
                    />
                  </div>
                </div>
                <div class="row h-70">
                  <div
                    class="col w-50 free-text"
                    :style="{
                      backgroundColor:
                        activeSlide.fields.template1_text2_fill_color,
                    }"
                    @click="
                      changeActiveBlockMeta(
                        store.slide_templates.template1.template1_text2
                      )
                    "
                  >
                    <div
                      v-if="activeSlide.fields.template1_text2_text == null"
                      class="content-type"
                    >
                      Text
                    </div>
                    <div
                      v-else
                      class="formatted-text"
                      :style="{
                        fontFamily: activeSlide.fields.template1_text2_font,
                        fontSize: activeSlide.fields.template1_text2_font_size,
                        lineHeight: '1.1em',
                        color: activeSlide.fields.template1_text2_text_color,
                        fontWeight:
                          activeSlide.fields.template1_text2_font_weight,
                        textAlign:
                          activeSlide.fields.template1_text2_text_align,
                      }"
                      v-html="activeSlide.fields.template1_text2_text"
                    ></div>
                    <slide-block-corners
                      v-if="
                        checkActiveBlockMeta(
                          store.slide_templates.template1.template1_text2
                        )
                      "
                    />
                  </div>
                  <div
                    class="col w-50 media"
                    @click="
                      changeActiveBlockMeta(
                        store.slide_templates.template1.template1_media1
                      )
                    "
                  >
                    <div
                      v-if="activeSlide.fields.template1_media1_image == null"
                      class="content-type"
                    >
                      Media
                    </div>
                    <template v-else>
                      <img
                        v-if="activeSlide.fields.template1_media1_image"
                        :src="activeSlide.fields.template1_media1_image"
                        style="width: 100%; height: 100%; object-fit: cover"
                      />
                    </template>

                    <slide-block-corners
                      v-if="
                        checkActiveBlockMeta(
                          store.slide_templates.template1.template1_media1
                        )
                      "
                    />
                  </div>
                </div>
              </div>
            </template>
            <template v-if="activeSlide.template === 'template2'">
              <div class="slide-template-preview flex-builder">
                <div class="row h-30">
                  <div class="col max-w-50"></div>
                </div>
                <div class="row h-70">
                  <div class="col w-100"></div>
                </div>
              </div>
            </template>
            <template v-if="activeSlide.template === 'template3'">
              <div class="slide-template-preview flex-builder"></div>
            </template>
            <template v-if="activeSlide.template === 'template4'">
              <div
                v-if="activeSlide.fields"
                class="slide-template-preview video-slide"
              >
                <div class="video-wrap">
                  <VideoPlayer v-bind:movieData="activeSlide.fields" />
                </div>
              </div>
              <template v-else>
                <div class="video-body">
                  <div class="video-label">Select movie</div>
                </div>
              </template>
            </template>
            
            
             <template v-if="activeSlide.template === 'template5'">
              <div class="question-block">
                <SlideQuestionTemplate :question_data="store.active_question"/>
              </div>
            </template>
            
            
            
          </template>
          <div v-else @click="new_slide_modal = true" class="btn">
            <svg
              class="icon"
              width="15"
              height="15"
              viewBox="0 0 15 15"
              fill="#51acfd"
              id="plusBlue"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                class="cls-1"
                d="M7.5,15a1,1,0,0,1-1-1V1a1,1,0,0,1,2,0V14A1,1,0,0,1,7.5,15Z"
              ></path>
              <path
                class="cls-1"
                d="M14,8.5H1a1,1,0,0,1,0-2H14a1,1,0,0,1,0,2Z"
              ></path>
            </svg>
            <span>Add New Slide</span>
          </div>
        </div>
        <div class="question-popup" v-if="store.view_question != null">
          <QuestionTemplate 
            v-on:remove-template="store.view_question = null"
            :question_data="store.view_question"/>
        </div>
        
      </div>
    </div>

    <div class="slide-meta">
      <div v-if="store.active_block_meta !== null" class="block-meta">
        <div class="header">{{ store.active_block_meta.label }}</div>
        <div class="body">
          <div
            v-for="(field, name) in store.active_block_meta.fields"
            :key="name"
            class="input-row"
          >
            <slide-block-input :property_name="name" :field="field" />
          </div>
        </div>
      </div>
      
      
      <div v-if="store.active_question !== null && store.active_question.place !== 'slide'" class="block-meta">
        <div class="header">
          <button class="back-meta" @click="store.active_question = null">New Questions</button>
        </div>
        <div class="body">
          <Question/>
        </div>
      </div>
      <div v-if="store.active_question !== null && store.active_question.place === 'slide'" class="block-meta">
        <div class="header">
          Question Properties
        </div>
        <div class="body">
          <SlideQuestion/>
        </div>
      </div>
      
      
      <el-tabs
        v-if="
          store.active_slide !== null &&
          store.active_block_meta == null &&
          store.active_question == null &&
          activeSlide &&
          activeSlide.slide_type === 'text_image'
        "
        v-model="active_slide_meta_tab"
      >
        <el-tab-pane label="Layout" name="layout">
          <div class="container-25px slide-templates flex-builder">
            <template1
              @click.native="activeSlide.template = 'template1'"
              :class="{
                active: activeSlide.template === 'template1',
              }"
              class="clickable"
            >
            </template1>
            <template2
              @click.native="activeSlide.template = 'template2'"
              :class="{
                active: activeSlide.template === 'template2',
              }"
              class="clickable"
            >
            </template2>
            <template3
              @click.native="activeSlide.template = 'template3'"
              :class="{
                active: activeSlide.template === 'template3',
              }"
              class="clickable"
            >
            </template3>
          </div>
        </el-tab-pane>
        <el-tab-pane label="Background" name="background"> </el-tab-pane>
        <el-tab-pane label="Theme" name="theme"> </el-tab-pane>
      </el-tabs>

      <el-tabs
        v-if="
          store.active_slide !== null &&
          store.active_block_meta == null &&
          store.active_question == null &&
          activeSlide &&
          activeSlide.slide_type === 'movie'
        "
        v-model="active_video_slide_meta_tab"
      >
        <el-tab-pane label="Movie" name="movie">
          <div
            v-if="!isReplaceMovie && activeSlide.fields && activeSlide.fields.kaltura_id"
            class="container-25px flex-builder"
          >
            <MovieOptions v-on:replace-click="isReplaceMovie=true" v-on:remove-click="removeMovie()" v-bind:kalturaId="activeSlide.fields.kaltura_id" />
          </div>
          <div v-else class="container-25px flex-builder">
            <div v-if="isReplaceMovie" @click="isReplaceMovie=false" class="btn --cancel-replace">
              <span>Cancel</span>
            </div>
            <SearchMovie v-on:add-movie="addMovieToSlide" />
          </div>
        </el-tab-pane>
        
        <el-tab-pane v-if="store.active_video != null"  label="Questions" name="questions">
          <div class="container-25px flex-builder">
            <div class="btn" @click="newQuestion()">
              <svg width="15" height="15" viewBox="0 0 15 15"
                fill="#51acfd" id="plusBlue"                 xmlns="http://www.w3.org/2000/svg"
                class="icon">
                <path
                  d="M7.5,15a1,1,0,0,1-1-1V1a1,1,0,0,1,2,0V14A1,1,0,0,1,7.5,15Z"
                  class="cls-1"
                ></path>
                <path
                  d="M14,8.5H1a1,1,0,0,1,0-2H14a1,1,0,0,1,0,2Z"
                  class="cls-1"
                ></path>
              </svg>
              <span>Add Question</span>
            </div>
            <div class="question-list">
              <QuestionItem 
                  v-on:show-question="showQuestion(question.question_id)"
                  v-on:remove-question="removeQuestion(question.question_id)"
                  v-on:change-question="changeQuestion(question.question_id)"
                  v-for="(question, index) in movieQuestions"
                  :key="question.question_id"
                  :question_data="question"
                  :number="index">
              </QuestionItem>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>

    <el-dialog
      title="Select Slide"
      width="780px"
      :visible.sync="new_slide_modal"
    >
      <p v-if="!store.slide_templates">Loading...</p>
      <div v-if="store.slide_templates" class="slide-types">
        <div @click="addSlideTextImage" class="slide-type">
          <span> Text /<br />Image </span>
          <img
            alt=""
            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA1OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgY2xpcC1wYXRoPSJ1cmwoI2NsaXAwKSI+CjxwYXRoIGQ9Ik00OC45OTc4IDQ4QzQ4Ljc2NjggNDggNDguNTMwMyA0Ny45NzExIDQ4LjI5NjYgNDcuOTA5MkwyNy4wMzUgNDIuMjE1M0MyNS41Nzc1IDQxLjgxMzggMjQuNzA4NSA0MC4zMDQxIDI1LjA5MDcgMzguODQ2NkwyNy43NzMzIDI4Ljg0NzZDMjcuODcyMyAyOC40ODA1IDI4LjI0OTEgMjguMjY3MyAyOC42MTQ4IDI4LjM2MDhDMjguOTgyIDI4LjQ1ODQgMjkuMTk5MiAyOC44MzY2IDI5LjEwMTYgMjkuMjAyM0wyNi40MjAzIDM5LjE5ODZDMjYuMjI5MiAzOS45MjczIDI2LjY2NjUgNDAuNjg2MyAyNy4zOTY2IDQwLjg4ODVMNDguNjUgNDYuNTc5NkM0OS4zODAxIDQ2Ljc3MjEgNTAuMTMzNiA0Ni4zMzc2IDUwLjMyMzMgNDUuNjExNkw1MS4zOTcyIDQxLjYzMjNDNTEuNDk2MiA0MS4yNjUyIDUxLjg3MyA0MS4wNDY2IDUyLjI0MDEgNDEuMTQ3QzUyLjYwNzIgNDEuMjQ2IDUyLjgyMzEgNDEuNjI0MSA1Mi43MjU1IDQxLjk4OThMNTEuNjUzIDQ1Ljk2MzZDNTEuMzI5OCA0Ny4xODczIDUwLjIxNjEgNDggNDguOTk3OCA0OFoiIGZpbGw9InVybCgjcGFpbnQwX2xpbmVhcikiLz4KPHBhdGggZD0iTTU1LjI1MDEgMzkuNzVIMzMuMjUwMUMzMS43MzM1IDM5Ljc1IDMwLjUwMDEgMzguNTE2NiAzMC41MDAxIDM3VjIwLjVDMzAuNTAwMSAxOC45ODM0IDMxLjczMzUgMTcuNzUgMzMuMjUwMSAxNy43NUg1NS4yNTAxQzU2Ljc2NjcgMTcuNzUgNTguMDAwMSAxOC45ODM0IDU4LjAwMDEgMjAuNVYzN0M1OC4wMDAxIDM4LjUxNjYgNTYuNzY2NyAzOS43NSA1NS4yNTAxIDM5Ljc1Wk0zMy4yNTAxIDE5LjEyNUMzMi40OTI1IDE5LjEyNSAzMS44NzUxIDE5Ljc0MjQgMzEuODc1MSAyMC41VjM3QzMxLjg3NTEgMzcuNzU3NiAzMi40OTI1IDM4LjM3NSAzMy4yNTAxIDM4LjM3NUg1NS4yNTAxQzU2LjAwNzcgMzguMzc1IDU2LjYyNTEgMzcuNzU3NiA1Ni42MjUxIDM3VjIwLjVDNTYuNjI1MSAxOS43NDI0IDU2LjAwNzcgMTkuMTI1IDU1LjI1MDEgMTkuMTI1SDMzLjI1MDFaIiBmaWxsPSJ1cmwoI3BhaW50MV9saW5lYXIpIi8+CjxwYXRoIGQ9Ik0zNy4zNzUxIDI3LjM3NUMzNS44NTg1IDI3LjM3NSAzNC42MjUxIDI2LjE0MTYgMzQuNjI1MSAyNC42MjVDMzQuNjI1MSAyMy4xMDg0IDM1Ljg1ODUgMjEuODc1IDM3LjM3NTEgMjEuODc1QzM4Ljg5MTcgMjEuODc1IDQwLjEyNTEgMjMuMTA4NCA0MC4xMjUxIDI0LjYyNUM0MC4xMjUxIDI2LjE0MTYgMzguODkxNyAyNy4zNzUgMzcuMzc1MSAyNy4zNzVaTTM3LjM3NTEgMjMuMjVDMzYuNjE3NSAyMy4yNSAzNi4wMDAxIDIzLjg2NzQgMzYuMDAwMSAyNC42MjVDMzYuMDAwMSAyNS4zODI2IDM2LjYxNzUgMjYgMzcuMzc1MSAyNkMzOC4xMzI3IDI2IDM4Ljc1MDEgMjUuMzgyNiAzOC43NTAxIDI0LjYyNUMzOC43NTAxIDIzLjg2NzQgMzguMTMyNyAyMy4yNSAzNy4zNzUxIDIzLjI1WiIgZmlsbD0idXJsKCNwYWludDJfbGluZWFyKSIvPgo8cGF0aCBkPSJNMzEuMjgzNyAzOC4yNzg0QzMxLjEwNzcgMzguMjc4NCAzMC45MzE3IDM4LjIxMSAzMC43OTY5IDM4LjA3NzZDMzAuNTI4OCAzNy44MDk1IDMwLjUyODggMzcuMzczNiAzMC43OTY5IDM3LjEwNTVMMzcuMjkxIDMwLjYxMTRDMzguMDY5MyAyOS44MzMxIDM5LjQyOTIgMjkuODMzMSA0MC4yMDc0IDMwLjYxMTRMNDIuMTQwNyAzMi41NDQ2TDQ3LjQ5MjIgMjYuMTIzNEM0Ny44ODEzIDI1LjY1NzIgNDguNDUzMyAyNS4zODY0IDQ5LjA2MjQgMjUuMzgwOUg0OS4wNzc1QzQ5LjY3OTggMjUuMzgwOSA1MC4yNTA0IDI1LjY0MjEgNTAuNjQzNyAyNi4xTDU3LjgzNDkgMzQuNDkwMkM1OC4wODI0IDM0Ljc3NzYgNTguMDQ5NCAzNS4yMTIxIDU3Ljc2MDcgMzUuNDU5NkM1Ny40NzMzIDM1LjcwNzEgNTcuMDQwMiAzNS42NzU1IDU2Ljc5MTMgMzUuMzg1NEw0OS42IDI2Ljk5NTFDNDkuNDY2NyAyNi44NDExIDQ5LjI4MjQgMjYuNzU1OSA0OS4wNzc1IDI2Ljc1NTlDNDguOTM0NSAyNi43NDM1IDQ4LjY4MjkgMjYuODQyNSA0OC41NDk1IDI3LjAwMzRMNDIuNzE1NCAzNC4wMDM1QzQyLjU5MTcgMzQuMTUyIDQyLjQxMTUgMzQuMjQxNCA0Mi4yMTc3IDM0LjI0OTZDNDIuMDIyNCAzNC4yNjM0IDQxLjgzNjggMzQuMTg2NCA0MS43MDA3IDM0LjA0ODlMMzkuMjM1MyAzMS41ODM1QzM4Ljk3NTQgMzEuMzI1IDM4LjUyMyAzMS4zMjUgMzguMjYzMiAzMS41ODM1TDMxLjc2OSAzOC4wNzc2QzMxLjYzNTcgMzguMjExIDMxLjQ1OTcgMzguMjc4NCAzMS4yODM3IDM4LjI3ODRaIiBmaWxsPSJ1cmwoI3BhaW50M19saW5lYXIpIi8+CjwvZz4KPHBhdGggZD0iTTI5LjkwMDUgMEgwVjcuODA4MDJIMTEuMDAxNlYzNUgxOC44OTkyVjcuODA4MDJIMjkuOTAwNVYwWk0yNy41MDM5IDUuNDM4NkgxNi41MDI2VjMyLjYzMDZIMTMuMzk4MlY1LjQzODZIMi4zOTY2MlYyLjM2OTQySDI3LjUwMzlWNS40Mzg2WiIgZmlsbD0idXJsKCNwYWludDRfbGluZWFyKSIvPgo8ZGVmcz4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDBfbGluZWFyIiB4MT0iMzguODc0OCIgeTE9IjI4LjMzODkiIHgyPSIzOC44NzQ4IiB5Mj0iNDgiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzNDNTY2RSIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDFfbGluZWFyIiB4MT0iNDQuMjUwMSIgeTE9IjE3Ljc1IiB4Mj0iNDQuMjUwMSIgeTI9IjM5Ljc1IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMzQzU2NkUiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjNTFBQ0ZEIi8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQyX2xpbmVhciIgeDE9IjM3LjM3NTEiIHkxPSIyMS44NzUiIHgyPSIzNy4zNzUxIiB5Mj0iMjcuMzc1IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMzQzU2NkUiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjNTFBQ0ZEIi8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQzX2xpbmVhciIgeDE9IjQ0LjI5ODQiIHkxPSIyNS4zODA5IiB4Mj0iNDQuMjk4NCIgeTI9IjM4LjI3ODQiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzNDNTY2RSIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDRfbGluZWFyIiB4MT0iMTQuOTUwMyIgeTE9IjAiIHgyPSIxNC45NTAzIiB5Mj0iMzUiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzNENTg3MiIvPgo8c3RvcCBvZmZzZXQ9IjAuNjI1IiBzdG9wLWNvbG9yPSIjNTFBQ0ZEIi8+CjwvbGluZWFyR3JhZGllbnQ+CjxjbGlwUGF0aCBpZD0iY2xpcDAiPgo8cmVjdCB3aWR0aD0iMzMiIGhlaWdodD0iMzMiIGZpbGw9IndoaXRlIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgyNSAxNSkiLz4KPC9jbGlwUGF0aD4KPC9kZWZzPgo8L3N2Zz4K"
          />
        </div>

        <div @click="addSlideVideo" class="slide-type">
          <span> Movie </span>
          <img
            alt=""
            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTkiIGhlaWdodD0iNTkiIHZpZXdCb3g9IjAgMCA1OSA1OSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTM3LjQyMjQgMEgxMy45NDM0VjEwLjM3MTFINDEuNTk5NkM0OS4yODggMTAuMzcxMSA1NS41NDMgMTYuNjI2IDU1LjU0MyAyNC4zMTQ1QzU1LjU0MyAzMC42NTUxIDUxLjA2NTQgMzYuMTc2MyA0NS4wMTg0IDM3LjcxNTVDNDUuMDQyOSAzNy4yODQ3IDQ1LjA1NjYgMzYuODUxIDQ1LjA1NjYgMzYuNDE0MUM0NS4wNTY2IDIzLjk2MDEgMzQuOTc2MyAxMy44MjgxIDIyLjU4NTkgMTMuODI4MUMxMC4xNjM5IDEzLjgyODEgMCAyMy45OTMzIDAgMzYuNDE0MUMwIDQ4LjgzNjIgMTAuMTY1MyA1OSAyMi41ODU5IDU5QzMwLjc0MTEgNTkgMzcuODk0OSA1NC42MTA0IDQxLjgzMzEgNDguMDU4OEM1MS43Nzk0IDQ1Ljk3ODcgNTkgMzcuMTg2OCA1OSAyNy4wNTEzQzU5IDI1LjMxMjIgNTkgMjMuMzE2OCA1OSAyMS41Nzc2QzU5IDkuNzEyNjQgNDkuMjg1NiAwIDM3LjQyMjQgMFpNMzQuNjg1NSA2LjkxNDA2SDI3Ljc3MTVWMy40NTcwM0gzNC42ODU1VjYuOTE0MDZaTTE3LjQwMDQgMy40NTcwM0gyNC4zMTQ1VjYuOTE0MDZIMTcuNDAwNFYzLjQ1NzAzWk00MS41OTk2IDYuOTE0MDZIMzguMTQyNlYzLjQ3MjkzQzQzLjM3MTYgMy42Nzg1MSA0OC4wMzg5IDYuMTA4OCA1MS4yMTk3IDkuODQzMDlDNDguMzgzMiA3LjkzNzQ2IDQ1LjA2MzYgNi45MTQwNiA0MS41OTk2IDYuOTE0MDZaTTIyLjU4NTkgNTUuNTQzQzEyLjAzODIgNTUuNTQzIDMuNDU3MDMgNDYuOTYxOCAzLjQ1NzAzIDM2LjQxNDFDMy40NTcwMyAyNS44NjYzIDEyLjAzODIgMTcuMjg1MiAyMi41ODU5IDE3LjI4NTJDMzMuMDcwMSAxNy4yODUyIDQxLjU5OTYgMjUuODY2MyA0MS41OTk2IDM2LjQxNDFDNDEuNTk5NiA0Ni45NjE4IDMzLjA3MDEgNTUuNTQzIDIyLjU4NTkgNTUuNTQzWk00My43ODc3IDQzLjg5NjFDNDQuMDc3NCA0My4wNjkxIDQ0LjMyMDcgNDIuMjIwNCA0NC41MTQgNDEuMzUyOEM0Ni44Njk1IDQwLjk1MzQgNDkuMTA3MSA0MC4wNzU5IDUxLjA5NjggMzguNzgwMUM0OS4xNDExIDQxLjAzMTYgNDYuNjM3NyA0Mi44MTUgNDMuNzg3NyA0My44OTYxWiIgZmlsbD0idXJsKCNwYWludDBfbGluZWFyKSIvPgo8cGF0aCBkPSJNMjIuNTg1OSAzMS4yMjg1QzI1LjQ0NTIgMzEuMjI4NSAyNy43NzE1IDI4LjkwMjMgMjcuNzcxNSAyNi4wNDNDMjcuNzcxNSAyMy4xODM3IDI1LjQ0NTIgMjAuODU3NCAyMi41ODU5IDIwLjg1NzRDMTkuNzI2NiAyMC44NTc0IDE3LjQwMDQgMjMuMTgzNyAxNy40MDA0IDI2LjA0M0MxNy40MDA0IDI4LjkwMjMgMTkuNzI2NiAzMS4yMjg1IDIyLjU4NTkgMzEuMjI4NVpNMjIuNTg1OSAyNC4zMTQ1QzIzLjUzOSAyNC4zMTQ1IDI0LjMxNDUgMjUuMDg5OSAyNC4zMTQ1IDI2LjA0M0MyNC4zMTQ1IDI2Ljk5NjEgMjMuNTM5IDI3Ljc3MTUgMjIuNTg1OSAyNy43NzE1QzIxLjYzMjggMjcuNzcxNSAyMC44NTc0IDI2Ljk5NjEgMjAuODU3NCAyNi4wNDNDMjAuODU3NCAyNS4wODk5IDIxLjYzMjggMjQuMzE0NSAyMi41ODU5IDI0LjMxNDVaIiBmaWxsPSJ1cmwoI3BhaW50MV9saW5lYXIpIi8+CjxwYXRoIGQ9Ik0yMi41ODU5IDQxLjU5OTZDMTkuNzI2NiA0MS41OTk2IDE3LjQwMDQgNDMuOTI1OCAxNy40MDA0IDQ2Ljc4NTJDMTcuNDAwNCA0OS42NDQ1IDE5LjcyNjYgNTEuOTcwNyAyMi41ODU5IDUxLjk3MDdDMjUuNDQ1MiA1MS45NzA3IDI3Ljc3MTUgNDkuNjQ0NSAyNy43NzE1IDQ2Ljc4NTJDMjcuNzcxNSA0My45MjU4IDI1LjQ0NTIgNDEuNTk5NiAyMi41ODU5IDQxLjU5OTZaTTIyLjU4NTkgNDguNTEzN0MyMS42MzI4IDQ4LjUxMzcgMjAuODU3NCA0Ny43MzgzIDIwLjg1NzQgNDYuNzg1MkMyMC44NTc0IDQ1LjgzMjEgMjEuNjMyOCA0NS4wNTY2IDIyLjU4NTkgNDUuMDU2NkMyMy41MzkgNDUuMDU2NiAyNC4zMTQ1IDQ1LjgzMjEgMjQuMzE0NSA0Ni43ODUyQzI0LjMxNDUgNDcuNzM4MyAyMy41MzkgNDguNTEzNyAyMi41ODU5IDQ4LjUxMzdaIiBmaWxsPSJ1cmwoI3BhaW50Ml9saW5lYXIpIi8+CjxwYXRoIGQ9Ik0xMi4yMTQ4IDMxLjIyODVDOS4zNTU1MyAzMS4yMjg1IDcuMDI5MyAzMy41NTQ4IDcuMDI5MyAzNi40MTQxQzcuMDI5MyAzOS4yNzM0IDkuMzU1NTMgNDEuNTk5NiAxMi4yMTQ4IDQxLjU5OTZDMTUuMDc0MiA0MS41OTk2IDE3LjQwMDQgMzkuMjczNCAxNy40MDA0IDM2LjQxNDFDMTcuNDAwNCAzMy41NTQ4IDE1LjA3NDIgMzEuMjI4NSAxMi4yMTQ4IDMxLjIyODVaTTEyLjIxNDggMzguMTQyNkMxMS4yNjE3IDM4LjE0MjYgMTAuNDg2MyAzNy4zNjcyIDEwLjQ4NjMgMzYuNDE0MUMxMC40ODYzIDM1LjQ2MSAxMS4yNjE3IDM0LjY4NTUgMTIuMjE0OCAzNC42ODU1QzEzLjE2NzkgMzQuNjg1NSAxMy45NDM0IDM1LjQ2MSAxMy45NDM0IDM2LjQxNDFDMTMuOTQzNCAzNy4zNjcyIDEzLjE2NzkgMzguMTQyNiAxMi4yMTQ4IDM4LjE0MjZaIiBmaWxsPSJ1cmwoI3BhaW50M19saW5lYXIpIi8+CjxwYXRoIGQ9Ik0zMi45NTcgMzEuMjI4NUMzMC4wOTc3IDMxLjIyODUgMjcuNzcxNSAzMy41NTQ4IDI3Ljc3MTUgMzYuNDE0MUMyNy43NzE1IDM5LjI3MzQgMzAuMDk3NyA0MS41OTk2IDMyLjk1NyA0MS41OTk2QzM1LjgxNjMgNDEuNTk5NiAzOC4xNDI2IDM5LjI3MzQgMzguMTQyNiAzNi40MTQxQzM4LjE0MjYgMzMuNTU0OCAzNS44MTYzIDMxLjIyODUgMzIuOTU3IDMxLjIyODVaTTMyLjk1NyAzOC4xNDI2QzMyLjAwMzkgMzguMTQyNiAzMS4yMjg1IDM3LjM2NzIgMzEuMjI4NSAzNi40MTQxQzMxLjIyODUgMzUuNDYxIDMyLjAwMzkgMzQuNjg1NSAzMi45NTcgMzQuNjg1NUMzMy45MTAxIDM0LjY4NTUgMzQuNjg1NSAzNS40NjEgMzQuNjg1NSAzNi40MTQxQzM0LjY4NTUgMzcuMzY3MiAzMy45MTAxIDM4LjE0MjYgMzIuOTU3IDM4LjE0MjZaIiBmaWxsPSJ1cmwoI3BhaW50NF9saW5lYXIpIi8+CjxwYXRoIGQ9Ik0yMC44NTc0IDM0LjY4NTVIMjQuMzE0NVYzOC4xNDI2SDIwLjg1NzRWMzQuNjg1NVoiIGZpbGw9InVybCgjcGFpbnQ1X2xpbmVhcikiLz4KPGRlZnM+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQwX2xpbmVhciIgeDE9IjI5LjUiIHkxPSIwIiB4Mj0iMjkuNSIgeTI9IjU5IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjNDI2RTk1Ii8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQxX2xpbmVhciIgeDE9IjIyLjU4NTkiIHkxPSIyMC44NTc0IiB4Mj0iMjIuNTg1OSIgeTI9IjMxLjIyODUiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzUxQUNGRCIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM0MjZFOTUiLz4KPC9saW5lYXJHcmFkaWVudD4KPGxpbmVhckdyYWRpZW50IGlkPSJwYWludDJfbGluZWFyIiB4MT0iMjIuNTg1OSIgeTE9IjQxLjU5OTYiIHgyPSIyMi41ODU5IiB5Mj0iNTEuOTcwNyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPgo8c3RvcCBzdG9wLWNvbG9yPSIjNTFBQ0ZEIi8+CjxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iIzQyNkU5NSIvPgo8L2xpbmVhckdyYWRpZW50Pgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50M19saW5lYXIiIHgxPSIxMi4yMTQ4IiB5MT0iMzEuMjI4NSIgeDI9IjEyLjIxNDgiIHkyPSI0MS41OTk2IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjNDI2RTk1Ii8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQ0X2xpbmVhciIgeDE9IjMyLjk1NyIgeTE9IjMxLjIyODUiIHgyPSIzMi45NTciIHkyPSI0MS41OTk2IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjNDI2RTk1Ii8+CjwvbGluZWFyR3JhZGllbnQ+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQ1X2xpbmVhciIgeDE9IjIyLjU4NTkiIHkxPSIzNC42ODU1IiB4Mj0iMjIuNTg1OSIgeTI9IjM4LjE0MjYiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iIzUxQUNGRCIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiM0MjZFOTUiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8L3N2Zz4K"
          />
        </div>
        <div @click="addSlideQuestion" class="slide-type">
          <span> Question </span>
          <img
            alt=""
            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjEiIGhlaWdodD0iNDQiIHZpZXdCb3g9IjAgMCAyMSA0NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTAuODIgNC4yNEMxLjM2IDMuNyAxLjk3IDMuMTggMi42NSAyLjY4QzMuMzMgMi4xOCA0LjA3IDEuNzQgNC44NyAxLjM2QzUuNjkgMC45NTk5OTggNi41OCAwLjY0OTk5NyA3LjU0IDAuNDI5OTk5QzguNSAwLjE4OTk5NyA5LjU0IDAuMDY5OTk2NiAxMC42NiAwLjA2OTk5NjZDMTIuMDQgMC4wNjk5OTY2IDEzLjM1IDAuMjc5OTk3IDE0LjU5IDAuNjk5OTk3QzE1Ljg1IDEuMTIgMTYuOTQgMS43MiAxNy44NiAyLjVDMTguOCAzLjI4IDE5LjU0IDQuMjQgMjAuMDggNS4zOEMyMC42NCA2LjUgMjAuOTIgNy43NiAyMC45MiA5LjE2QzIwLjkyIDEwLjY4IDIwLjY4IDExLjk5IDIwLjIgMTMuMDlDMTkuNzQgMTQuMTkgMTkuMTUgMTUuMTYgMTguNDMgMTZDMTcuNzEgMTYuODIgMTYuOTMgMTcuNTUgMTYuMDkgMTguMTlDMTUuMjUgMTguODEgMTQuNDYgMTkuNCAxMy43MiAxOS45NkMxMi45OCAyMC41IDEyLjM1IDIxLjA1IDExLjgzIDIxLjYxQzExLjMzIDIyLjE3IDExLjA2IDIyLjc5IDExLjAyIDIzLjQ3TDEwLjY5IDI4LjM5SDguNzRMOC41NiAyMy4yNkM4LjUyIDIyLjQyIDguNzMgMjEuNjggOS4xOSAyMS4wNEM5LjY1IDIwLjQgMTAuMjQgMTkuNzkgMTAuOTYgMTkuMjFDMTEuNjggMTguNjMgMTIuNDYgMTguMDQgMTMuMyAxNy40NEMxNC4xNCAxNi44NCAxNC45MiAxNi4xNiAxNS42NCAxNS40QzE2LjM4IDE0LjY0IDE2Ljk5IDEzLjc3IDE3LjQ3IDEyLjc5QzE3Ljk3IDExLjgxIDE4LjIyIDEwLjY0IDE4LjIyIDkuMjhDMTguMjIgOC4yIDE4LjAxIDcuMjQgMTcuNTkgNi40QzE3LjE3IDUuNTQgMTYuNiA0LjgyIDE1Ljg4IDQuMjRDMTUuMTYgMy42NCAxNC4zMyAzLjE5IDEzLjM5IDIuODlDMTIuNDUgMi41NyAxMS40NyAyLjQxIDEwLjQ1IDIuNDFDOS4xMyAyLjQxIDcuOTggMi41OSA3IDIuOTVDNi4wNCAzLjI5IDUuMjMgMy42OCA0LjU3IDQuMTJDMy45MSA0LjU0IDMuMzkgNC45MyAzLjAxIDUuMjlDMi42MyA1LjYzIDIuMzcgNS44IDIuMjMgNS44QzEuOTMgNS44IDEuNzEgNS42OCAxLjU3IDUuNDRMMC44MiA0LjI0Wk02Ljg4IDQwLjY5QzYuODggNDAuMzEgNi45NSAzOS45NSA3LjA5IDM5LjYxQzcuMjMgMzkuMjcgNy40MiAzOC45OCA3LjY2IDM4Ljc0QzcuOTIgMzguNDggOC4yMSAzOC4yOCA4LjUzIDM4LjE0QzguODcgMzcuOTggOS4yNCAzNy45IDkuNjQgMzcuOUMxMC4wMiAzNy45IDEwLjM4IDM3Ljk4IDEwLjcyIDM4LjE0QzExLjA2IDM4LjI4IDExLjM1IDM4LjQ4IDExLjU5IDM4Ljc0QzExLjg1IDM4Ljk4IDEyLjA1IDM5LjI3IDEyLjE5IDM5LjYxQzEyLjM1IDM5Ljk1IDEyLjQzIDQwLjMxIDEyLjQzIDQwLjY5QzEyLjQzIDQxLjA5IDEyLjM1IDQxLjQ2IDEyLjE5IDQxLjhDMTIuMDUgNDIuMTIgMTEuODUgNDIuNDEgMTEuNTkgNDIuNjdDMTEuMzUgNDIuOTEgMTEuMDYgNDMuMSAxMC43MiA0My4yNEMxMC4zOCA0My4zOCAxMC4wMiA0My40NSA5LjY0IDQzLjQ1QzguODYgNDMuNDUgOC4yIDQzLjE5IDcuNjYgNDIuNjdDNy4xNCA0Mi4xMyA2Ljg4IDQxLjQ3IDYuODggNDAuNjlaIiBmaWxsPSJ1cmwoI3BhaW50MF9saW5lYXIpIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50MF9saW5lYXIiIHgxPSIxMiIgeTE9Ii04IiB4Mj0iMTIiIHkyPSI0NyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPgo8c3RvcCBzdG9wLWNvbG9yPSIjM0M1NjZFIi8+CjxzdG9wIG9mZnNldD0iMC4zMTI1IiBzdG9wLWNvbG9yPSIjNTFBQ0ZEIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg=="
          />
        </div>
        <div class="slide-type disabling" >
          <span> Web </span>
          <img
            alt=""
            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTUiIGhlaWdodD0iNTUiIHZpZXdCb3g9IjAgMCA1NSA1NSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTU0Ljk5OTkgMjcuNTMzNkM1NC45OTk4IDEyLjMyODIgNDIuNjczNCAwLjAwMTgzODQxIDI3LjQ2ODEgMC4wMDE5NTMxM0MyNi40ODIyIDAuMDAxOTUzMTMgMjUuNDk3IDAuMDU0OTUxNyAyNC41MTY4IDAuMTYwNjA1QzIzLjYxODMgMC4yNTIzNzcgMjIuNzMwOSAwLjQwMjg4NCAyMS44NTU0IDAuNTg1NTExQzIxLjY4NiAwLjYyMDk1OCAyMS41MTY0IDAuNjU4MzU1IDIxLjM0NyAwLjY5NzQ3M0MxNi4xODg2IDEuODcxOTMgMTEuNDc4MyA0LjUwOTM1IDcuNzgxMTggOC4yOTM0N0M3LjQzNzM4IDguNjQ1ODggNy4xMDA4IDkuMDA4NzIgNi43NzE2OCA5LjM4MTg5Qy0zLjIxNTQ0IDIwLjg0NzUgLTIuMDE2ODkgMzguMjM4MyA5LjQ0ODggNDguMjI1NUMyMC40MjU3IDU3Ljc4NjkgMzYuOTQ4NyA1Ny4xNTA3IDQ3LjE1NzkgNDYuNzczNkM0Ny41MDI0IDQ2LjQyMTIgNDcuODM4OSA0Ni4wNTg0IDQ4LjE2NzQgNDUuNjg1MkM1Mi41NzQ5IDQwLjY2NTggNTUuMDAzOCAzNC4yMTMzIDU0Ljk5OTkgMjcuNTMzNlpNNDUuOTMxOSA0NS4zOTdDNDUuNzg2OSA0NS41NDY2IDQ1LjYzNzMgNDUuNjkwNyA0NS40ODk1IDQ1LjgzNjZDNDUuMzE4OCA0Ni4wMDQ2IDQ1LjE0ODEgNDYuMTc0NCA0NC45NzI4IDQ2LjMzNzdDNDQuODIwNSA0Ni40OCA0NC42NjM2IDQ2LjYxMyA0NC41MDc2IDQ2Ljc1NTNDNDQuMzI5NSA0Ni45MTMxIDQ0LjE1MjQgNDcuMDcxIDQzLjk3MDcgNDcuMjIzM0M0My44MTAxIDQ3LjM1NzMgNDMuNjQ2NyA0Ny40ODY3IDQzLjQ4MzQgNDcuNjE3QzQzLjI5OTggNDcuNzYzOSA0My4xMTYzIDQ3LjkxMDcgNDIuOTI3MiA0OC4wNTJDNDIuNzYwMiA0OC4xNzc4IDQyLjU5MDQgNDguMjk5OCA0Mi40MTk3IDQ4LjQxOTFDNDIuMjI5OCA0OC41NTQ5IDQyLjAzODkgNDguNjk0NCA0MS44NDUyIDQ4LjgyMTFDNDEuNjcxOCA0OC45Mzg1IDQxLjQ5NjUgNDkuMDUyMyA0MS4zMjAzIDQ5LjE2NDNDNDEuMTIzOSA0OS4yOTEgNDAuOTI2IDQ5LjQxMzQgNDAuNzI2NSA0OS41MzE0QzQwLjU0MyA0OS42Mzg4IDQwLjM2NzcgNDkuNzQzNCA0MC4xODY5IDQ5Ljg0NzFDMzkuOTgzOCA0OS45NjI3IDM5Ljc3OTQgNTAuMDc1NiAzOS41NzM5IDUwLjE4NTdDMzkuMzkwMyA1MC4yODM2IDM5LjIwNjggNTAuMzc5NCAzOS4wMjMyIDUwLjQ3M0MzOC44MTQgNTAuNTc4NSAzOC42MDI5IDUwLjY4MDQgMzguMzkwOSA1MC43ODA0QzM4LjIwNzQgNTAuODY3NiAzOC4wMTY1IDUwLjk1NDggMzcuODI3NCA1MS4wMzc0QzM3LjYxMTggNTEuMTI5MiAzNy4zOTM0IDUxLjIyMDkgMzcuMTc0OSA1MS4zMTI3QzM2Ljk4NSA1MS4zODk4IDM2Ljc5NTkgNTEuNDY2OSAzNi42MDUgNTEuNTM5NEMzNi4zODExIDUxLjYyNDcgMzYuMTU2MyA1MS43MDQ2IDM1LjkzMDUgNTEuNzgyNkMzNS43Mzk2IDUxLjg0OTYgMzUuNTQ4NyA1MS45MTY2IDM1LjM1NTEgNTEuOTc5QzM1LjEyNTcgNTIuMDUzMyAzNC44OTYyIDUyLjEyMTIgMzQuNjYyMiA1Mi4xODgyQzM0LjQ3MDQgNTIuMjQ1MSAzNC4yNzc3IDUyLjMwMiAzNC4wODQxIDUyLjM1NDNDMzQuMDA5NyA1Mi4zNzM2IDMzLjkzNTQgNTIuMzg5MiAzMy44NjEgNTIuNDA4NUMzNi41Nzc1IDQ5Ljc3MDggMzguNTkzNCA0Ni40OTczIDM5LjcyNjIgNDIuODg0M0M0MS45MDQ0IDQzLjQ1ODggNDQuMDEzMSA0NC4yNzAzIDQ2LjAxNDUgNDUuMzA0NEM0NS45ODQyIDQ1LjMzNzQgNDUuOTU4NSA0NS4zNjk1IDQ1LjkzMTkgNDUuMzk3Wk0yMC4yNzg3IDUyLjE5NTVDMjAuMDQ2NiA1Mi4xMjc2IDE5LjgxMzUgNTIuMDU5NyAxOS41ODQgNTEuOTg1NEMxOS4zOTA0IDUxLjkyMyAxOS4xOTk1IDUxLjg1NiAxOS4wMDg2IDUxLjc4OUMxOC43ODI5IDUxLjcxMSAxOC41NTcxIDUxLjYzMTEgMTguMzM0MSA1MS41NDU4QzE4LjE0MzIgNTEuNDczMyAxNy45NTUxIDUxLjM5NzEgMTcuNzY2IDUxLjMyQzE3LjU0NjcgNTEuMjI4MyAxNy4zMjczIDUxLjEzNjUgMTcuMTA5OCA1MS4wNDQ3QzE2LjkyMjYgNTAuOTYyMSAxNi43MzYzIDUwLjg3NzEgMTYuNTUwOSA1MC43ODk2QzE2LjMzNzEgNTAuNjg4NiAxNi4xMjQyIDUwLjU4NTkgMTUuOTA4NSA1MC40Nzk0QzE1LjcyNSA1MC4zODc2IDE1LjU0MTUgNTAuMjk1OSAxNS4zNTc5IDUwLjE5NThDMTUuMTUwNSA1MC4wODQ4IDE0Ljk0NDkgNDkuOTcxIDE0LjczOTQgNDkuODUzNUMxNC41NTU4IDQ5Ljc1MTYgMTQuMzgxNCA0OS42NDcgMTQuMjA0MyA0OS41NDA2QzE0LjAwMjQgNDkuNDE5NCAxMy44MDQyIDQ5LjI5NDYgMTMuNjA2IDQ5LjE2NzFDMTMuNDMxNiA0OS4wNTYgMTMuMjU3MiA0OC45NDMxIDEzLjA4NTYgNDguODI3NUMxMi44OTAxIDQ4LjY5NTQgMTIuNjk2NSA0OC41NTc3IDEyLjUwNDcgNDguNDJDMTIuMzM2OCA0OC4yOTk4IDEyLjE2ODggNDguMTc5NiAxMi4wMDM2IDQ4LjA1MjlDMTEuODEzNyA0Ny45MDk4IDExLjYyNzQgNDcuNzYyIDExLjQ0MiA0Ny42MTM0QzExLjI3OTUgNDcuNDg0IDExLjExOCA0Ny4zNTU1IDEwLjk1OTMgNDcuMjIyNEMxMC43NzU3IDQ3LjA3MDEgMTAuNTk4NiA0Ni45MTEzIDEwLjQyMDYgNDYuNzUzNEMxMC4yNjQ1IDQ2LjYxNDkgMTAuMTA3NiA0Ni40NzgxIDkuOTU1MjcgNDYuMzM2OEM5Ljc3OTk4IDQ2LjE3MzQgOS42MTAyIDQ2LjAwMzcgOS40Mzk1MSA0NS44MzU3QzkuMjkxNzUgNDUuNjg5OCA5LjE0MTI1IDQ1LjU0NTcgOC45OTYyNSA0NS4zOTYxQzguOTY5NjMgNDUuMzY3NyA4Ljk0Mzk0IDQ1LjMzODMgOC45MTczMiA0NS4zMTA4QzEwLjkxODUgNDQuMjc2MyAxMy4wMjcyIDQzLjQ2NDQgMTUuMjA1NiA0Mi44ODk4QzE2LjMzODYgNDYuNTAyNyAxOC4zNTQ0IDQ5Ljc3NjEgMjEuMDcwNyA1Mi40MTRDMjAuOTk2NCA1Mi4zOTQ3IDIwLjkyMTEgNTIuMzc5MSAyMC44NDY4IDUyLjM1ODlDMjAuNjYxNCA1Mi4zMTIxIDIwLjQ2OTYgNTIuMjUxNSAyMC4yNzg3IDUyLjE5NTVaTTcuNjg0ODIgMTEuMTQ3NkM5Ljg4OTMxIDEyLjM0MiAxMi4yMjcgMTMuMjcyNCAxNC42NDk0IDEzLjkxOTFDMTMuNDYxOSAxOC4wNDg0IDEyLjgzODUgMjIuMzE5NCAxMi43OTY1IDI2LjYxNThIMS43OTMwNEMxLjk5Mjk4IDIwLjk1MDEgNC4wNjQ4NiAxNS41MTA2IDcuNjg0ODIgMTEuMTQ3NlpNOS4wMDQ1MSA5LjY3MDA2QzkuMTQ5NTEgOS41MTk1NSA5LjMwMDAxIDkuMzc1NDcgOS40NDc3NyA5LjIyOTU1QzkuNjE4NDYgOS4wNjE2MSA5Ljc4ODI0IDguODkyNzUgOS45NjI2MSA4LjczMDMxQzEwLjExNTkgOC41ODcxNCAxMC4yNzM3IDguNDQ5NDkgMTAuNDMwNiA4LjMwOTk5QzEwLjYwNzggOC4xNTMwNiAxMC43ODQgNy45OTYxMyAxMC45NjU3IDcuODQ0NzFDMTEuMTI1NCA3LjcwOTggMTEuMjg5NiA3LjU4MDQgMTEuNDUzIDcuNDUwMDhDMTEuNjM2NSA3LjMwMzI1IDExLjgyMDEgNy4xNTY0MSAxMi4wMDkxIDcuMDE1MDhDMTIuMTc2MiA2Ljg4OTM2IDEyLjM0NTkgNi43NjczIDEyLjUxNjYgNi42NDc5OUMxMi43MDY2IDYuNTEyMTcgMTIuODk3NSA2LjM3MjY4IDEzLjA5MTEgNi4yNDYwM0MxMy4yNjQ2IDYuMTI4NTYgMTMuNDM5OSA2LjAxNDc3IDEzLjYxNjEgNS45MDE4OUMxMy44MTI1IDUuNzc2MTYgMTQuMDA5OCA1LjY1MjI2IDE0LjIwODkgNS41MzQ4QzE0LjM4ODIgNS40Mjc3NyAxNC41Njg3IDUuMzIyNDYgMTQuNzUwNCA1LjIxOTFDMTQuOTUzNSA1LjEwMzQ3IDE1LjE1NzUgNC45OTA1OSAxNS4zNjI1IDQuODgwNDZDMTUuNTQ2IDQuNzgyNjEgMTUuNzI5NiA0LjY4NjgyIDE1LjkxMzEgNC41OTMyMUMxNi4xMjI0IDQuNDg3NjcgMTYuMzMzNCA0LjM4NTgxIDE2LjU0NTQgNC4yODU3N0MxNi43MjkgNC4xOTg1OSAxNi45MTk5IDQuMTExNDEgMTcuMTA4OSA0LjAyODgxQzE3LjMyNDYgMy45MzcwNCAxNy41NDMgMy44NDUyNyAxNy43NjE0IDMuNzUzNDlDMTcuOTUxNCAzLjY3NTQ5IDE4LjE0MDQgMy41OTkzMiAxOC4zMzIyIDMuNTI1OUMxOC41NTQzIDMuNDQxNDcgMTguNzc5MiAzLjM2MjU0IDE5LjAwNCAzLjI4MzYyQzE5LjE5NTggMy4yMTY2MyAxOS4zODc2IDMuMTQ5NjMgMTkuNTgyMiAzLjA4NjMxQzE5LjgxMDcgMy4wMTI4OSAyMC4wNDEgMi45NDQ5OCAyMC4yNzIzIDIuODc3OTlDMjAuNDY2IDIuODIyIDIwLjY1NzggMi43NjQxOSAyMC44NTIzIDIuNzExODhDMjAuOTI2NyAyLjY5MjYxIDIxLjAwMSAyLjY3NyAyMS4wNzUzIDIuNjU3NzNDMTguMzU4OSA1LjI5NTM4IDE2LjM0MyA4LjU2ODkgMTUuMjEwMiAxMi4xODE5QzEzLjAzMTkgMTEuNjA3NCAxMC45MjMyIDEwLjc5NTkgOC45MjE5MSA5Ljc2MTgzQzguOTUyMiA5LjcyOTcxIDguOTc3ODkgOS42OTc1OSA5LjAwNDUxIDkuNjcwMDZaTTM0LjY1NzYgMi44NzE1NkMzNC44ODk4IDIuOTM5NDcgMzUuMTIyOSAzLjAwNzM4IDM1LjM1MjMgMy4wODE3MkMzNS41NDYgMy4xNDQxMiAzNS43MzY5IDMuMjExMTIgMzUuOTI3OCAzLjI3ODExQzM2LjE1MzUgMy4zNTYxMiAzNi4zNzkzIDMuNDM1OTYgMzYuNjAyMyAzLjUyMTMxQzM2Ljc5MzIgMy41OTM4MSAzNi45ODEzIDMuNjY5OTggMzcuMTcwNCAzLjc0NzA3QzM3LjM4OTcgMy44Mzg4NCAzNy42MDkgMy45MzA2MSAzNy44MjY1IDQuMDIyMzlDMzguMDEzNyA0LjEwNDk4IDM4LjIgNC4xODk5OSAzOC4zODU0IDQuMjc3NTFDMzguNTk5MyA0LjM3ODQ2IDM4LjgxMjIgNC40ODEyNSAzOS4wMjc4IDQuNTg3NzFDMzkuMjExNCA0LjY3OTQ4IDM5LjM5NDkgNC43NzEyNSAzOS41Nzg1IDQuODcxMjhDMzkuNzg1OSA0Ljk4MjMzIDM5Ljk5MTQgNS4wOTYxMiA0MC4xOTcgNS4yMTM1OUM0MC4zODA2IDUuMzE1NDYgNDAuNTU0OSA1LjQyMDA4IDQwLjczMiA1LjUyNjU0QzQwLjkzMzkgNS42NDc2OCA0MS4xMzIyIDUuNzcyNDkgNDEuMzMwNCA1LjkwMDA1QzQxLjUwNDggNi4wMTEwOSA0MS42NzkxIDYuMTIzOTcgNDEuODUwNyA2LjIzOTYxQzQyLjA0NjIgNi4zNzE3NiA0Mi4yMzg5IDYuNTA4NSA0Mi40MzA4IDYuNjQ2MTZDNDIuNTk5NiA2Ljc2NjM4IDQyLjc2NzYgNi44ODc1MiA0Mi45MzM3IDcuMDEzMjVDNDMuMTIyNyA3LjE1NTUgNDMuMzA4MSA3LjMwMzI1IDQzLjQ5NDQgNy40NTE5MkM0My42NTU5IDcuNTgwNCA0My44MTgzIDcuNzA4ODggNDMuOTc3MSA3Ljg0Mjg3QzQ0LjE2MDcgNy45OTUyMSA0NC4zMzc4IDguMTUzOTggNDQuNTE1OCA4LjMxMTgzQzQ0LjY3MTggOC40NTA0IDQ0LjgyODggOC41ODcxNSA0NC45ODExIDguNzI4NDdDNDUuMTU2NCA4Ljg5MTgzIDQ1LjMyNjIgOS4wNjE2MSA0NS40OTY5IDkuMjI5NTVDNDUuNjQ0NiA5LjM3NTQ3IDQ1Ljc5NTEgOS41MTk1NSA0NS45NDAxIDkuNjY5MTRDNDUuOTY2NyA5LjY5NzU5IDQ1Ljk5MjQgOS43MjY5NiA0Ni4wMTkgOS43NTQ0OUM0NC4wMTc4IDEwLjc4OSA0MS45MDkxIDExLjYwMDggMzkuNzMwOCAxMi4xNzU0QzM4LjU5NTYgOC41NjIxNCAzNi41NzcyIDUuMjg4OTYgMzMuODU4MyAyLjY1MjIzQzMzLjkzMjYgMi42NzE1IDM0LjAwNzkgMi42ODcxIDM0LjA4MjIgMi43MDcyOUMzNC4yNzQ5IDIuNzU1MDEgMzQuNDY2NyAyLjgxNTU4IDM0LjY1NzYgMi44NzE1NlpNNDIuMTM5OCAyNi42MTU4QzQyLjA5NzggMjIuMzE5NCA0MS40NzQ1IDE4LjA0ODQgNDAuMjg2OSAxMy45MTkxQzQyLjcwOTMgMTMuMjcyMSA0NS4wNDcgMTIuMzQxOCA0Ny4yNTE2IDExLjE0NzZDNTAuODcxMyAxNS41MTA3IDUyLjk0MzIgMjAuOTUwMyA1My4xNDMzIDI2LjYxNThINDIuMTM5OFpNMjYuNTUwNSAxMy43NDExQzIzLjM0MTkgMTMuNjk2MyAyMC4xNDY2IDEzLjMyMDIgMTcuMDE1MyAxMi42MTg3QzE5LjE4MTEgNi41NjYzMiAyMi42MjM1IDIuNDY3NzYgMjYuNTUwNSAxLjkwOTc5VjEzLjc0MTFaTTE2LjQzOSAxNC4zNzA2QzE5Ljc1NzYgMTUuMTI3NCAyMy4xNDcgMTUuNTMxNyAyNi41NTA1IDE1LjU3NjVWMjYuNjE1OEgxNC42MzJDMTQuNjc4OCAyMi40NzEyIDE1LjI4NjYgMTguMzUyMSAxNi40MzkgMTQuMzcwNlpNMjYuNTUwNSAyOC40NTEzVjM5LjQ5MDZDMjMuMTQ2OSAzOS41MzUzIDE5Ljc1NzYgMzkuOTM5NiAxNi40MzkgNDAuNjk2NUMxNS4yODY3IDM2LjcxNSAxNC42Nzg4IDMyLjU5NTggMTQuNjMyIDI4LjQ1MTNIMjYuNTUwNVpNMjYuNTUwNSA0MS4zMjZWNTMuMTU3M0MyMi42MjM1IDUyLjU5OTMgMTkuMTgxMSA0OC41MDA4IDE3LjAxNTMgNDIuNDQ4NEMyMC4xNDY2IDQxLjc0NzEgMjMuMzQyIDQxLjM3MTEgMjYuNTUwNSA0MS4zMjZaTTI4LjM4NTkgNDEuMzI2QzMxLjU5NDUgNDEuMzcwOCAzNC43ODk4IDQxLjc0NjkgMzcuOTIxMSA0Mi40NDg0QzM1Ljc1NTIgNDguNTAwOCAzMi4zMTI4IDUyLjU5OTMgMjguMzg1OSA1My4xNTczVjQxLjMyNlpNMzguNDk3NCA0MC42OTY1QzM1LjE3ODggMzkuOTM5NyAzMS43ODk0IDM5LjUzNTQgMjguMzg1OSAzOS40OTA2VjI4LjQ1MTNINDAuMzA0NEM0MC4yNTc3IDMyLjU5NTggMzkuNjQ5OCAzNi43MTUgMzguNDk3NCA0MC42OTY1Wk0yOC4zODU5IDI2LjYxNThWMTUuNTc2NUMzMS43ODk1IDE1LjUzMTggMzUuMTc4OCAxNS4xMjc1IDM4LjQ5NzQgMTQuMzcwNkMzOS42NDk3IDE4LjM1MjEgNDAuMjU3NiAyMi40NzEzIDQwLjMwNDQgMjYuNjE1OEgyOC4zODU5Wk0yOC4zODU5IDEzLjc0MTFWMS45MDk3OUMzMi4zMTI4IDIuNDY3NzYgMzUuNzU1MiA2LjU2NjMyIDM3LjkyMTEgMTIuNjE4N0MzNC43ODk4IDEzLjMxOTggMzEuNTk0NSAxMy42OTYgMjguMzg1OSAxMy43NDExWk0xMi43OTY1IDI4LjQ1MTNDMTIuODM4NSAzMi43NDc3IDEzLjQ2MTkgMzcuMDE4NyAxNC42NDk0IDQxLjE0OEMxMi4yMjcxIDQxLjc5NSA5Ljg4OTQyIDQyLjcyNTMgNy42ODQ4MiA0My45MTk1QzQuMDY1MDkgMzkuNTU2NCAxLjk5MzIxIDM0LjExNjggMS43OTMwNCAyOC40NTEzSDEyLjc5NjVaTTQwLjI4NjkgNDEuMTQ4QzQxLjQ3NDUgMzcuMDE4NyA0Mi4wOTc4IDMyLjc0NzcgNDIuMTM5OCAyOC40NTEzSDUzLjE0MzNDNTIuOTQzNSAzNC4xMTcgNTAuODcxNSAzOS41NTY1IDQ3LjI1MTYgNDMuOTE5NUM0NS4wNDcxIDQyLjcyNSA0Mi43MDk0IDQxLjc5NDggNDAuMjg2OSA0MS4xNDhaIiBmaWxsPSJ1cmwoI3BhaW50MF9saW5lYXIpIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50MF9saW5lYXIiIHgxPSIyNy41IiB5MT0iNTUuMDY1MiIgeDI9IjI3LjUiIHkyPSIwLjAwMTc5MjMxIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIG9mZnNldD0iMC4zOTU4MzMiIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjM0M1NjZFIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg=="
          />
        </div>
        <div class="slide-type disabling">
          <span> Pdf </span>
          <img
            alt=""
            src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDMiIGhlaWdodD0iNTUiIHZpZXdCb3g9IjAgMCA0MyA1NSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTQxLjc4OTMgMTEuNzk0MUwzMC4zMzA5IDAuMzM1NjkzQzMwLjExNjEgMC4xMjA4NSAyOS44MjUyIDAgMjkuNTIwOCAwSDUuNDU4MjZDMi45MzA1MiAwIDAuODc1IDIuMDU1NTIgMC44NzUgNC41ODMzN1Y1MC40MTY3QzAuODc1IDUyLjk0NDUgMi45MzA1MiA1NSA1LjQ1ODM3IDU1SDM3LjU0MTdDNDAuMDY5NSA1NSA0Mi4xMjUgNTIuOTQ0NSA0Mi4xMjUgNTAuNDE2NlYxMi42MDQxQzQyLjEyNSAxMi4yOTk4IDQyLjAwNDIgMTIuMDA4OSA0MS43ODkzIDExLjc5NDFaTTMwLjY2NjYgMy45MTE5OEwzOC4yMTMgMTEuNDU4NEgzMi45NTgzQzMxLjY5NSAxMS40NTg0IDMwLjY2NjYgMTAuNDMgMzAuNjY2NiA5LjE2Njc0VjMuOTExOThaTTM5LjgzMzQgNTAuNDE2NkMzOS44MzM0IDUxLjY3OTkgMzguODA1IDUyLjcwODMgMzcuNTQxNyA1Mi43MDgzSDUuNDU4MzdDNC4xOTUwOSA1Mi43MDgzIDMuMTY2NzQgNTEuNjc5OSAzLjE2Njc0IDUwLjQxNjZWNC41ODMzN0MzLjE2Njc0IDMuMzIwMDkgNC4xOTUwOSAyLjI5MTc0IDUuNDU4MzcgMi4yOTE3NEgyOC4zNzVWOS4xNjY3NEMyOC4zNzUgMTEuNjk0NSAzMC40MzA1IDEzLjc1IDMyLjk1ODQgMTMuNzVIMzkuODMzNFY1MC40MTY2WiIgZmlsbD0idXJsKCNwYWludDBfbGluZWFyKSIvPgo8cGF0aCBkPSJNMjcuMzQyMSAzMy42MzczQzI2LjI4MTMgMzIuODAyNSAyNS4yNzMyIDMxLjk0NDMgMjQuNjAxOCAzMS4yNzI5QzIzLjcyOSAzMC40MDAxIDIyLjk1MTMgMjkuNTU0MiAyMi4yNzU1IDI4Ljc0ODVDMjMuMzI5NiAyNS40OTEyIDIzLjc5MTcgMjMuODExNiAyMy43OTE3IDIyLjkxNjRDMjMuNzkxNyAxOS4xMTMgMjIuNDE3NiAxOC4zMzMgMjAuMzU0MiAxOC4zMzNDMTguNzg2NSAxOC4zMzMgMTYuOTE2NyAxOS4xNDc2IDE2LjkxNjcgMjMuMDI2MUMxNi45MTY3IDI0LjczNTkgMTcuODUzMyAyNi44MTE2IDE5LjcwOTcgMjkuMjI0MUMxOS4yNTU0IDMwLjYxMDUgMTguNzIxNiAzMi4yMDk1IDE4LjEyMTkgMzQuMDEyMkMxNy44MzMyIDM0Ljg3NzIgMTcuNTE5OSAzNS42NzgzIDE3LjE4ODYgMzYuNDE5MUMxNi45MTkgMzYuNTM4OSAxNi42NTcxIDM2LjY2MDggMTYuNDA0MiAzNi43ODcyQzE1LjQ5MzQgMzcuMjQyNyAxNC42Mjg0IDM3LjY1MjIgMTMuODI2MSAzOC4wMzI3QzEwLjE2NyAzOS43NjQ4IDcuNzUgNDAuOTEwNyA3Ljc1IDQzLjE3MzJDNy43NSA0NC44MTU5IDkuNTM0ODEgNDUuODMzIDExLjE4NzUgNDUuODMzQzEzLjMxOCA0NS44MzMgMTYuNTM1MSA0Mi45ODc0IDE4Ljg4NDkgMzguMTkzN0MyMS4zMjQzIDM3LjIzMTQgMjQuMzU2OCAzNi41MTg2IDI2Ljc1MDIgMzYuMDcyMUMyOC42NjgyIDM3LjU0NjkgMzAuNzg2NCAzOC45NTggMzEuODEyNSAzOC45NThDMzQuNjUzNiAzOC45NTggMzUuMjUgMzcuMzE1MyAzNS4yNSAzNS45Mzc4QzM1LjI1IDMzLjIyODggMzIuMTU0OSAzMy4yMjg4IDMwLjY2NjYgMzMuMjI4OEMzMC4yMDQ1IDMzLjIyODkgMjguOTY0NyAzMy4zNjU0IDI3LjM0MjEgMzMuNjM3M1pNMTEuMTg3NSA0My41NDE0QzEwLjUzMjkgNDMuNTQxNCAxMC4wODk4IDQzLjIzMjUgMTAuMDQxNiA0My4xNzMyQzEwLjA0MTYgNDIuMzYwOCAxMi40NjQyIDQxLjIxMjggMTQuODA3NCA0MC4xMDI4QzE0Ljk1NjIgNDAuMDMyMyAxNS4xMDczIDM5Ljk2MTggMTUuMjYwNiAzOS44ODlDMTMuNTM5NiA0Mi4zODQ0IDExLjgzNzYgNDMuNTQxNCAxMS4xODc1IDQzLjU0MTRaTTE5LjIwODQgMjMuMDI2MUMxOS4yMDg0IDIwLjYyNDcgMTkuOTUzNyAyMC42MjQ3IDIwLjM1NDIgMjAuNjI0N0MyMS4xNjQ0IDIwLjYyNDcgMjEuNTAwMSAyMC42MjQ3IDIxLjUwMDEgMjIuOTE2NEMyMS41MDAxIDIzLjM5OTggMjEuMTc3OCAyNC42MDgzIDIwLjU4ODEgMjYuNDk0OUMxOS42ODgzIDI1LjEwOTYgMTkuMjA4NCAyMy45MjI0IDE5LjIwODQgMjMuMDI2MVpNMjAuMDg2OCAzNS4zNDE1QzIwLjE1ODQgMzUuMTQyNCAyMC4yMjc4IDM0Ljk0MSAyMC4yOTQ5IDM0LjczNzNDMjAuNzIwMSAzMy40NjE3IDIxLjEwMjkgMzIuMzE1OCAyMS40NDQxIDMxLjI4NDFDMjEuOTE5NyAzMS44MDc4IDIyLjQzMjIgMzIuMzQzOCAyMi45ODE3IDMyLjg5MzJDMjMuMTk2NSAzMy4xMDggMjMuNzI5MSAzMy41OTE0IDI0LjQzODYgMzQuMTk2N0MyMy4wMjYyIDM0LjUwNDUgMjEuNTIzNSAzNC44ODYxIDIwLjA4NjggMzUuMzQxNVpNMzIuOTU4NCAzNS45Mzc5QzMyLjk1ODQgMzYuNDUyNyAzMi45NTg0IDM2LjY2NjQgMzEuODk1MyAzNi42NzMxQzMxLjU4MzIgMzYuNjA2IDMwLjg2MTQgMzYuMTgwOCAyOS45NzA2IDM1LjU3NDNDMzAuMjk0IDM1LjUzODUgMzAuNTMyNCAzNS41MjA2IDMwLjY2NjYgMzUuNTIwNkMzMi4zNTk3IDM1LjUyMDYgMzIuODM5OCAzNS42ODYxIDMyLjk1ODQgMzUuOTM3OVoiIGZpbGw9InVybCgjcGFpbnQxX2xpbmVhcikiLz4KPGRlZnM+CjxsaW5lYXJHcmFkaWVudCBpZD0icGFpbnQwX2xpbmVhciIgeDE9IjIxLjUiIHkxPSIwIiB4Mj0iMjEuNSIgeTI9IjU1IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMzQzU2NkUiLz4KPHN0b3Agb2Zmc2V0PSIwLjYwOTM3NSIgc3RvcC1jb2xvcj0iIzUxQUNGRCIvPgo8L2xpbmVhckdyYWRpZW50Pgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50MV9saW5lYXIiIHgxPSIyMS41IiB5MT0iMTguMzMzIiB4Mj0iMjEuNSIgeTI9IjQ1LjgzMyIgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiPgo8c3RvcCBzdG9wLWNvbG9yPSIjM0M1NjZFIi8+CjxzdG9wIG9mZnNldD0iMC42MDkzNzUiIHN0b3AtY29sb3I9IiM1MUFDRkQiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8L3N2Zz4K"
          />
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import QuestionItem from './Shared/QuestionItem'
import draggable from "vuedraggable";
import Template1 from "./Shared/SlideTemplates/Template1";
import Template2 from "./Shared/SlideTemplates/Template2";
import Template3 from "./Shared/SlideTemplates/Template3";
import Template4 from "./Shared/SlideTemplates/Template4";
import Template5 from "./Shared/SlideTemplates/Template5";
import SlideBlockCorners from "./Shared/SlideBlockCorners";
import SlideBlockInput from "./Shared/SlideBlockInput";
import SearchMovie from "./Shared/SearchMovie";
import MovieItem from "./Shared/MovieItem";
import VideoPlayer from "./Shared/VideoPlayer";
import MovieOptions from "./Shared/MovieOptions";
import Question from "./Shared/Question";
import QuestionTemplate from "./Shared/QuestionTemplate";
import SlideQuestion from "./Shared/SlideQuestion";
import SlideQuestionTemplate from "./Shared/SlideQuestionTemplate";
import service from "../../service";
import axios from "axios";

export default {
  name: "Slides",
  components: {
    QuestionItem,
    Template1,
    Template2,
    Template3,
    Template4,
    Template5,
    draggable, // docs: https://github.com/SortableJS/Vue.Draggable
    SlideBlockCorners,
    SlideBlockInput,
    SearchMovie,
    MovieItem,
    VideoPlayer,
    MovieOptions,
    Question,
    QuestionTemplate,
    SlideQuestion,
    SlideQuestionTemplate,
  },
  data() {
    return {
      isReplaceMovie: false,
      drag: false,
      new_slide_modal: false,
      active_slide_meta_tab: "layout", // layout/background/theme
      active_video_slide_meta_tab: "movie", // movie/questions
      movie_question: "",
    };
  },
  mounted() {
    this.getSlideTemplates();
  },
  computed: {
    store() {
      return this.$store.state.LessonEditor;
    },
    dragOptions() {
      return {
        animation: 200,
        group: "description",
        disabled: false,
        ghostClass: "ghost",
      };
    },
    activeSlideIndex() {
      return this.store.slides
        .map((x) => x.lesson_id)
        .indexOf(this.store.active_slide);
    },
    activeSlide() {
      const activeSlide = this.getSlideById(this.store.active_slide);
      return activeSlide;
    },
    
    movieQuestions: function() {
			var idSlide = this.store.active_slide;
			var video = this.store.active_video;

			if(idSlide != null) {
				return this.store.questions.filter(function(question) {
					return question.idSlide === idSlide;
				});
			}
		}
    
  },
  watch: {
    async activeSlide(slide) {
      if (slide.slide_type === "movie") {
        this.store.active_block_meta = null;
      }
    },
  },
  methods: {

    removeMovie() {
      this.store.active_video = null;
      this.activeSlide.fields = null;
    },

    addMovieToSlide(movieMeta) {
      this.store.active_video = movieMeta.kaltura_id;
      this.$store.commit("LessonEditor/updateSlide", {
        lesson_id: this.store.active_slide,
        fields: {
          kaltura_id: movieMeta.kaltura_id,
          play_from: 0,
          play_to: 0,
        },
      });

      // Hide search movies panel
      this.isReplaceMovie = false;
    },
    getSlideTemplates() {
      service
        .getSlideTemplates()
        .then((data) => {
          this.store.slide_templates = data;
        });
    },
    getTemplateFields(template_name) {
      //let template_fields = {};
      //for (const [template_key, template] of Object.entries(this.store.slide_templates)) {
      var block_fields = {};

      for (const [block_key, block] of Object.entries(
        this.store.slide_templates[template_name]
      )) {
        for (let field of Object.keys(block.fields)) {
          block_fields[field] = null;
        }
      }
      //template_fields['"'+template_key+'"'] = block_fields;
      //}
      return block_fields;
    },
    async addSlide(slide) {
      var _this = this;

      // Generate real slide (lesson) ID using WP:
      await axios.post('/academe/v1/create_new_step', {
        course_id: this.store.lesson_id,
        post_type: 'sfwd-lessons'
      }).then(response => {
        // Add slide with empty structure to the slides list
        const lesson_id = response.data;
        _this.store.slides.push({ ...slide, lesson_id });

        // Select last slide after creation
        _this.store.active_slide = lesson_id;

        // Close slide type selection modal
        _this.new_slide_modal = false;
      });

    },
    addSlideTextImage() {
      this.store.active_question = null;
      this.store.view_question = null;
      const new_slide = {
        slide_type: "text_image",
        template: "template1",
        fields: this.getTemplateFields("template1"),
      };

      this.addSlide(new_slide);
    },
    addSlideVideo() {
      // reset meta in movies
      this.store.active_block_meta = null;
      this.store.active_video = null;
      this.store.active_question = null;
      this.store.view_question = null;

      const new_slide = {
        slide_type: "movie",
        template: "template4",
      };

      this.addSlide(new_slide);
    },
    
    /*addSlideQuestion() {
      this.store.view_question = null;
      //create slide
      const new_slide = {
        slide_type: "question",
        template: "template5",
      };
      var _this = this;
      this.addSlide(new_slide).then(function (response) {

        // Generate real quiz + question IDs using WP:
        // You should to create firstly a quiz
        axios.post('/academe/v1/create_new_step', {
            course_id: _this.store.lesson_id,
            lesson_id: _this.store.active_slide,
            post_type: 'sfwd-quiz'
        }).then(response => {
            const quiz_id = response.data;

            // Update quiz settings (set up default params needed to show quiz correctly on the session front):
            axios.post("/academe/v1/default-quiz-settings", {
                quiz_id: quiz_id
            });

            // After quiz creation you should to create a question and add it to quiz
            axios.post('/academe/v1/create_new_step', {
                course_id: _this.store.lesson_id,
                lesson_id: _this.store.active_slide,
                quiz_id: quiz_id,
                post_type: 'sfwd-question'
            }).then(response => {
                const question_id = response.data;

                //create new question
                let newQuestion = {};
                newQuestion.idSlide = _this.store.active_slide;
                newQuestion.quiz_id = quiz_id; // real WP id of quiz
                newQuestion.question_id = question_id; // real WP id of question
                newQuestion.type = '';
                newQuestion.description = '';
                newQuestion.answers = [];
                newQuestion.checkedItems = [];
                newQuestion.questionIndex = [];
                newQuestion.score = '0';
                newQuestion.place = 'slide';

                _this.store.questions.push(newQuestion);
                _this.store.active_question = newQuestion;
            });

        });

      });
    },*/
    
    addSlideQuestionTempl(slide, newQuestion){
        var _this = this;
        // Generate real quiz + question IDs using WP:
        // You should to create firstly a quiz
        axios.post('/academe/v1/create_new_step', {
            course_id: _this.store.lesson_id,
            lesson_id: _this.store.active_slide,
            post_type: 'sfwd-quiz'
        }).then(response => {
            const quiz_id = response.data;
            
            // Add slide with empty structure to the slides list
            const lesson_id = response.data;
            _this.store.slides.push({ ...slide, lesson_id });

            // Select last slide after creation
            _this.store.active_slide = lesson_id;

            // Close slide type selection modal
            _this.new_slide_modal = false;

            // Update quiz settings (set up default params needed to show quiz correctly on the session front):
            axios.post("/academe/v1/default-quiz-settings", {
                quiz_id: quiz_id
            });

            // After quiz creation you should to create a question and add it to quiz
            axios.post('/academe/v1/create_new_step', {
                course_id: _this.store.lesson_id,
                lesson_id: _this.store.active_slide,
                quiz_id: quiz_id,
                post_type: 'sfwd-question'
            }).then(response => {
                const question_id = response.data;
                newQuestion.idSlide = _this.store.active_slide;
                newQuestion.quiz_id = quiz_id; // real WP id of quiz
                newQuestion.question_id = question_id; // real WP id of question

                _this.store.questions.push(newQuestion);
                _this.store.active_question = newQuestion;
            });

        });
    },    
    addSlideQuestion() {
      this.store.active_block_meta = null;
      this.store.active_question = null;
      this.store.view_question = null;
      //create slide
      const new_slide = {
        slide_type: "question",
        template: "template5",
      };
       //create new question
          let newQuestion = {};
          newQuestion.idSlide = '';
          newQuestion.quiz_id = '';
          newQuestion.question_id = '';
          newQuestion.type = '';
          newQuestion.description = '';
          newQuestion.answers = [];
          newQuestion.checkedItems = [];
          newQuestion.questionIndex = [];
          newQuestion.score = '0';
          newQuestion.place = 'slide';

      this.addSlideQuestionTempl(new_slide, newQuestion);
    },


    //slide actions
    duplicateQuestions(id, newId){
      for (var i = 0; i < this.store.questions.length; i++) {
          if (this.store.questions[i].idSlide == id) {
            let newQuestion = JSON.parse(JSON.stringify(this.store.questions[i]));
            newQuestion.idSlide = newId;
            this.store.questions.push(newQuestion);
          }
      }
    },
    duplicateSlide(index) {
      let _this = this;
      let id = this.store.slides[index].lesson_id;
      let slideType = this.store.slides[index].slide_type;
      let newSlide = JSON.parse(JSON.stringify(this.store.slides[index]));
      if (slideType === 'text_image'){
        this.addSlide(newSlide); 
      }
      if (slideType === 'question' ){
        let newQuestion;
        for (var i = 0; i < this.store.questions.length; i++) {
          if (this.store.questions[i].idSlide == id) {
            newQuestion = JSON.parse(JSON.stringify(this.store.questions[i]));
          }
        }
        console.log(newQuestion)
        this.addSlideQuestionTempl(newSlide, newQuestion); 
      }
      if (slideType === 'movie') {   
        this.addSlide(newSlide); 
        setTimeout(function(){
          for (var i = 0; i < _this.store.questions.length; i++) {
            if (_this.store.questions[i].idSlide == id) {
              let newQuestion = JSON.parse(JSON.stringify(_this.store.questions[i]));
              _this.duplicateMovieQuestion(newQuestion)
            }
          }       
        },3000) 
        console.log(_this.store.questions);
        console.log(_this.store.slides);
      }
    },
    
    removeSlide(index) {

      axios.delete('/ldlms/v1/sfwd-lessons/'+this.store.slides[index].lesson_id);

      this.store.slides.splice(index, 1);

      if (this.store.slides.length > 0) {
        if (index > 0) {
          this.store.active_slide = this.store.slides[index - 1].lesson_id;
        } else {
          this.store.active_slide = this.store.slides[0].lesson_id;
        }
      } else {
        this.store.active_slide = null;
      }
    },
    changeActiveBlockMeta(meta_obj) {
      this.store.active_block_meta =
        this.store.active_block_meta === meta_obj ? null : meta_obj;
    },
    checkActiveBlockMeta(meta_obj) {
      return this.store.active_block_meta === meta_obj;
    },
    changeActiveSlide(slide) {
        this.store.view_question = null;
        this.store.active_question = null;

      const slideId = parseInt(slide.lesson_id);
      this.store.active_slide = slideId;
      if (slide.slide_type === 'question'){
        this.store.active_question = this.getSlideQuestionById(slideId)
      }

    },
    getSlideById(lesson_id) {
      return this.store.slides.find((s) => s.lesson_id === lesson_id);
    },
    
    getSlideQuestionById(lesson_id) {
      return this.store.questions.find((s) => s.idSlide === lesson_id);
    },
    


    getQuestionById(id) {
          return this.store.questions.find((s) => s.question_id === id);
        },


    //create new question in movies - commented
    /*newQuestion() {
      var _this = this;

      // Generate real quiz + question IDs using WP:
      // You should to create firstly a quiz
      axios.post('/academe/v1/create_new_step', {
          course_id: this.store.lesson_id,
          lesson_id: this.store.active_slide,
          post_type: 'sfwd-quiz'
      }).then(response => {
          const quiz_id = response.data;

          // Update quiz settings (set up default params needed to show quiz correctly on the session front):
          axios.post("/academe/v1/default-quiz-settings", {
            quiz_id: quiz_id
          });

          // After quiz creation you should to create a question and add it to quiz
          axios.post('/academe/v1/create_new_step', {
              course_id: this.store.lesson_id,
              lesson_id: this.store.active_slide,
              quiz_id: quiz_id,
              post_type: 'sfwd-question'
          }).then(response => {
              const question_id = response.data;

              // Question structure:
              let question = {};
              let index = _this.store.questions.length;
              question.kaltura_id = this.store.active_video;
              question.idSlide = this.store.active_slide; // real WP id of slide (lesson)
              question.quiz_id = quiz_id; // real WP id of quiz
              question.question_id = question_id; // real WP id of question
              question.questionIndex = index;
              question.start_time = '00:00:00';
              question.type = 'Open';
              question.score ='0';
              question.limit ='';
              question.answers = [];
              question.checkedItems = [];
              question.correctAnswerIndex = '';
              question.addQuestion = true;
              question.place = 'movie';
              _this.store.active_question = question;
        });

      });


    },*/
    
    //create new question in movies
    addNewQuestion(question){
      var _this = this;
      // Generate real quiz + question IDs using WP:
      // You should to create firstly a quiz
      axios.post('/academe/v1/create_new_step', {
          course_id: _this.store.lesson_id,
          lesson_id: _this.store.active_slide,
          post_type: 'sfwd-quiz'
      }).then(response => {
          const quiz_id = response.data;

          // Update quiz settings (set up default params needed to show quiz correctly on the session front):
          axios.post("/academe/v1/default-quiz-settings", {
            quiz_id: quiz_id
          });

          // After quiz creation you should to create a question and add it to quiz
          axios.post('/academe/v1/create_new_step', {
              course_id: _this.store.lesson_id,
              lesson_id: _this.store.active_slide,
              quiz_id: quiz_id,
              post_type: 'sfwd-question'
          }).then(response => {
              const question_id = response.data; 
                question.idSlide = _this.store.active_slide; // real WP id of slide (lesson)
                question.quiz_id = quiz_id; // real WP id of quiz
                question.question_id = question_id; // real WP id of question         
                _this.store.active_question = question;
        });
      });
    },
    //duplicate questions in movies
    duplicateMovieQuestion(question){
      var _this = this;
      // Generate real quiz + question IDs using WP:
      // You should to create firstly a quiz
      axios.post('/academe/v1/create_new_step', {
          course_id:  _this.store.lesson_id,
          lesson_id:  _this.store.active_slide,
          post_type: 'sfwd-quiz'
      }).then(response => {
          const quiz_id = response.data;

          // Update quiz settings (set up default params needed to show quiz correctly on the session front):
          axios.post("/academe/v1/default-quiz-settings", {
            quiz_id: quiz_id
          });

          // After quiz creation you should to create a question and add it to quiz
          axios.post('/academe/v1/create_new_step', {
              course_id:  _this.store.lesson_id,
              lesson_id:  _this.store.active_slide,
              quiz_id: quiz_id,
              post_type: 'sfwd-question'
          }).then(response => {
              const question_id = response.data; 
                question.idSlide =  _this.store.active_slide; // real WP id of slide (lesson)
                question.quiz_id = quiz_id; // real WP id of quiz
                question.question_id = question_id; // real WP id of question         
                _this.store.questions.push(question);
        });
      });
    },
    newQuestion() {
      // Question structure:
      let question = {};
      let index = this.store.questions.length;
      question.kaltura_id = this.store.active_video;
      question.idSlide = ''; // real WP id of slide (lesson)
      question.quiz_id = ''; // real WP id of quiz
      question.question_id = ''; // real WP id of question
      question.questionIndex = index;
      question.start_time = '00:00:00';
      question.type = 'Open';
      question.score ='0';
      question.limit ='';
      question.answers = [];
      question.checkedItems = [];
      question.correctAnswerIndex = '';
      question.addQuestion = true;
      question.place = 'movie';
      this.addNewQuestion(question)
    },


    changeQuestion(index){
      //this.store.active_question = this.store.questions[index];
      this.store.active_question = this.getQuestionById(index);
      this.store.active_question.addQuestion = false;
    },
    removeQuestion(index) {
    let elIndex = this.store.questions.indexOf(this.getQuestionById(index));  
    
    this.store.view_question = null;
    this.store.questions.splice(elIndex, 1);

    },
    showQuestion(index) {
      //let activeQuestion = this.store.questions[index];
      let activeQuestion = this.getQuestionById(index);
      this.store.view_question = null;
      this.store.view_question = activeQuestion;
    }
    
  },
};
</script>

<style scoped>
.slide-type.disabling {
  cursor: unset;
  opacity: 0.5;
}
.slide-type.disabling:hover {
  border-color: #fff;
}

.slides-content {
  display: flex;
}
.slide-list {
  width: 250px;
  background: #2f2f2f;
  height: calc(100vh - 82px);
  overflow-y: auto;
}
.slide-list::-webkit-scrollbar-thumb,
.slide-meta::-webkit-scrollbar-thumb {
  border: 5px solid rgba(0, 0, 0, 0);
  background-clip: padding-box;
  -webkit-border-radius: 7px;
  height: 30px;
}
.slide-list::-webkit-scrollbar,
.slide-meta::-webkit-scrollbar {
  width: 14px;
  height: 18px;
}
.slide-list::-webkit-scrollbar-thumb,
.slide-meta::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.2);
}
.slide-content {
  flex: 1;
  max-height: calc(100vh - 82px);
  padding: 50px 80px 0;
}
.slide-meta {
  width: 380px;
  background: #2f2f2f;
  box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.8);
  height: calc(100vh - 150px);
  margin-right: 25px;
  overflow-y: auto;
}
.slide-list .slide-container {
  padding: 25px;
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
.btn:hover svg {
  fill: #fff;
}
.--cancel-replace {
  margin-bottom: 15px;
}
.btn span {
  margin-left: 5px;
}
.slide-types {
  display: flex;
  flex-flow: row wrap;
}
.slide-type {
  display: flex;
  justify-content: space-around;
  align-items: center;
  margin: 15px;
  padding: 20px 15px;
  width: calc(100% / 3 - 30px);
  background: #333333;
  border: 1px solid #ffffff;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}
.slide-type:hover {
  border-color: #51acfd;
}
.slide-type span {
  margin-right: 20px;
  font-size: 20px;
}
.slide-preview-wrap {
  display: flex;
  align-items: flex-start;
  cursor: pointer;
  position: relative;
}
.slide-preview-wrap * {
  user-select: none;
}
.slide-preview-wrap .slide-number {
  font-weight: 600;
  margin-right: 5px;
  display: flex;
  justify-content: flex-end;
  width: 10px;
}
.slide-preview-wrap .slide-preview {
  flex: 1;
  height: 100px;
  border: 2px solid #ffffff;
  box-shadow: 0px 1.75px 10.5px rgba(0, 0, 0, 0.8);
  border-radius: 7px;
  margin-right: 10px;
  background: #2f2f2f;
  position: relative;
}
.slide-preview-wrap.active {
  background: rgba(81, 172, 253, 0.24);
}
.slide-preview.active {
  border-color: #51acfd;
}
.slide-preview-wrap .draggable,
.slide-preview-wrap .more-actions {
  position: absolute;
  opacity: 0;
  transition: 0.2s;
}
.slide-preview-wrap:hover .draggable,
.slide-preview-wrap:hover .more-actions {
  opacity: 1;
}
.slide-preview-wrap .draggable {
  top: calc(50% - 13px);
  left: -25px;
  padding: 3px 8px;
}
.slide-preview-wrap .more-actions {
  top: 0;
  right: -30px;
}
.container-25px {
  padding: 25px;
}
.slide-templates {
  display: flex;
  flex-flow: row wrap;
  justify-content: space-between;
}
.aspect-ratio-box {
  height: 0;
  overflow: hidden;
  padding-top: 56%;
  background: #2f2f2f;
  position: relative;
  box-shadow: 0px 2px 12px rgba(0, 0, 0, 0.8);
  border-radius: 2px;
}
.aspect-ratio-box-inside {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.question-popup {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.61);
}
.question-block {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #51ACFD;
}
.aspect-ratio-box .slide-template-preview {
  height: 100%;
  width: 100%;
  border: none;
  margin-bottom: 0;
  cursor: default;
}
.aspect-ratio-box .slide-template-preview .col {
  padding: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}
.aspect-ratio-box .slide-template-preview .col.free-text {
  align-items: flex-start;
}
.aspect-ratio-box .slide-template-preview .col.media {
  padding: 0;
}
.aspect-ratio-box .slide-template-preview .col .content-type {
  height: 100%;
  display: flex;
  align-items: center;
}

.block-meta .header {
  height: 50px;
  padding: 0 30px;
  display: flex;
  align-items: center;
  font-weight: 600;
  border-bottom: 1px solid rgba(81, 172, 253, 0.33);
}
.block-meta .body {
  padding: 30px;
}
.formatted-text {
  width: 100%;
  max-height: 100%;
  overflow: hidden;
  white-space: pre-line;
  word-break: break-word;
}
</style>

<style>
.slide-template-preview {
  display: flex;
  flex-flow: row wrap;
  height: 95px;
  padding: 8px 12px;
}
.slide-template-preview.clickable.active {
  background: #51acfd;
  border: 2px solid #51acfd;
  box-shadow: 0 2px 14px rgba(0, 0, 0, 0.8);
  transform: scale(1.03);
}
.slide-template-preview.clickable {
  width: calc(50% - 10px);
  border: 2px solid #ffffff;
  border-radius: 10px;
  margin-bottom: 30px;
  cursor: pointer;
  transition: 0.2s;
}

.flex-builder .row {
  display: flex;
  justify-content: space-between;
  flex-direction: row;
  width: 100%;
}
.flex-builder .col {
  position: relative;
  display: flex;
  flex: 1;
  margin: 3%;
  background: rgba(196, 196, 196, 0.64);
}
.flex-builder .w-100 {
  width: 100%;
}
.flex-builder .w-50 {
  flex: 1 1 50%;
}
.flex-builder .max-w-50 {
  max-width: 50%;
}
.flex-builder .h-30 {
  height: 30%;
}
.flex-builder .h-40 {
  height: 40%;
}
.flex-builder .h-50 {
  height: 50%;
}
.flex-builder .h-70 {
  height: 70%;
}

.el-tabs__item {
  color: #979797;
  transition: 0.2s;
}
.el-tabs__item.is-active {
  color: #ffffff;
}
.el-tabs__item:hover {
  color: #ffffff;
}
.el-tabs__nav-wrap::after {
  height: 1px;
  background: rgba(81, 172, 253, 0.33);
}
.el-tabs__active-bar {
  display: none;
}
.el-tabs__nav {
  float: none;
  display: flex;
  justify-content: space-between;
  padding: 5px 25px;
}
.el-tabs__header {
  margin: 0;
}
</style>

<style>
.slide-template-preview .play-btn {
  width: 32px;
  max-width: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
.video-slide {
  width: 100%;
  display: flex;
  flex-direction: column;
  padding: 0 0 20px 0;
}
.video-wrap {
  width: 100%;
  height: 100%;
}
.video-slide .video-body {
  flex-grow: 1;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.video-slide .video-label {
  font-family: Montserrat;
  font-style: normal;
  font-weight: 600;
  font-size: 30px;
  letter-spacing: 0.159638px;
  color: #ffffff;
  text-align: center;
}
/* .video-slide .video-time-scroll {
    height: 20px;
    background: #0d0d0d;
}
.video-slide .video-footer-img img {
    width: 100%;
} */
</style>


<style scope>
  .back-meta {
    font-style: normal;
    font-weight: 600;
    font-size: 16px;
    line-height: 18px;
    letter-spacing: 0.175px;
    color: #FFFFFF;
    background-color: transparent;
    border: none;
    cursor: pointer;
  }
  .back-meta::before {
    content: '';
    display: inline-block;
    margin-right: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    padding: 3px;
        transform: rotate(135deg);
    -webkit-transform: rotate(135deg);
  }
  
</style>
