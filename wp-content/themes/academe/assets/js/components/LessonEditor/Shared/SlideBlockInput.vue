<template>
    <div>
        <div v-if="field.type === 'text'" class="col-view">
            <label>{{field.label}}</label>
            <el-input
                :placeholder="field.label"
                v-model="store.slides[$parent.activeSlideIndex].fields[property_name]"
                style="width:100%">
            </el-input>
        </div>
        <div v-else-if="field.type === 'textarea'" class="col-view">
            <label>{{field.label}}</label>
            <el-input
                    type="textarea"
                    rows="4"
                    :placeholder="field.label"
                    v-model="store.slides[$parent.activeSlideIndex].fields[property_name]"
                    style="width:100%">
            </el-input>
        </div>
        <div v-else-if="field.type === 'select'" class="row-view">
            <label>{{field.label}}</label>
            <el-select
                    v-model="store.slides[$parent.activeSlideIndex].fields[property_name]"
                    size="medium"
                    style=""
                    :placeholder="field.label">
                <el-option
                        v-for="(value, name) in field.values"
                        :key="name"
                        :label="value"
                        :value="name">
                </el-option>
            </el-select>
        </div>
        <div v-else-if="field.type === 'colorpicker'" class="row-view colorpicker-box">
            <label>{{field.label}}</label>

            <el-input v-model="store.slides[$parent.activeSlideIndex].fields[property_name]">
                <el-color-picker v-model="store.slides[$parent.activeSlideIndex].fields[property_name]" size="small" slot="prefix" show-alpha></el-color-picker>
            </el-input>

        </div>
        <div v-else-if="field.type === 'alignment'" class="row-view alignment-box">
            <label>{{field.label}}</label>
            <el-radio v-for="(value, name) in field.values" :key="name"
                      v-model="store.slides[$parent.activeSlideIndex].fields[property_name]" :label="name">
                <span class="alignment-icon" :class="name">
                    <span class="top"></span>
                    <span class="center"></span>
                    <span class="bottom"></span>
                </span>
            </el-radio>
        </div>
        <div v-else-if="field.type === 'range'" class="col-view range-box">
            <label>{{field.label}}</label>
            <el-slider v-model="store.slides[$parent.activeSlideIndex].fields[property_name]"
            :step="field.step"
            :min="field.from"
            :max="field.to"
            style="padding: 0 20px;">
            </el-slider>
        </div>
        <div v-else-if="field.type === 'image'" class="col-view">
            <label>{{field.label}}</label>
            <el-upload
                    class="upload-image"
                    drag
                    action=""
                    :show-file-list="false"
                    :on-success="handleImageLoadSuccess"
                    :before-upload="beforeImageUpload">
                <template v-if="store.slides[$parent.activeSlideIndex].fields[property_name] == null">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">Upload image</div>
                    <div class="el-upload__tip" slot="tip">jpg/png files with a size less than 500kb</div>
                </template>
                <img v-else class="image-preview" :src="store.slides[$parent.activeSlideIndex].fields[property_name]">
            </el-upload>
            <div v-if="store.slides[$parent.activeSlideIndex].fields[property_name] != null">
                <button class="btn" @click="removeMedia()">Remove image</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "SlideBlockInput",
        props: {
            property_name: String,
            field: Object,
        },
        data() {
            return {

            };
        },
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            },
        },
        methods: {

            removeMedia() {
                this.store.slides[this.$parent.activeSlideIndex].fields[this.property_name] = null;
            },

            handleImageLoadSuccess(res, file) {
                this.store.slides[this.$parent.activeSlideIndex].fields[this.property_name] = URL.createObjectURL(file.raw);
            },
            beforeImageUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isPNG = file.type === 'image/png';
                const isLt500k = file.size / 1024 / 1024 < 0.5;

                if (!isJPG && !isPNG) {
                    this.$message.error('Uploaded image must be JPG or PNG format!');
                }
                if (!isLt500k) {
                    this.$message.error('Uploaded image size can not exceed 500Kb!');
                }
                return (isJPG || isPNG) && isLt500k;
            }
        }
    }
</script>

<style scoped>
    .row-view, .col-view {
        margin-bottom: 30px;
    }
    .row-view {
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .col-view label {
        margin-bottom: 10px;
        display: block;
    }
    .row-view label {
        display: block;
        width: 120px;
        min-width: 120px;
    }
    .alignment-icon {
        display: flex;
        width: 19px;
        height: 13px;
        align-items: center;
        flex-wrap: wrap;
    }
    .alignment-icon > span {
        height: 1px;
        background: #ffffff;
    }
    .alignment-icon .top, .alignment-icon .bottom {
        width: 100%;
    }
    .alignment-icon .center {
        width: 60%;
    }
    .alignment-icon.left {
        justify-content: flex-start;
    }
    .alignment-icon.center {
        justify-content: center;
    }
    .alignment-icon.right {
        justify-content: flex-end;
    }
</style>

<style>
    .upload-image .el-upload-dragger, .upload-image .el-upload {
        max-width: 100%;
    }
    .upload-image .el-upload-dragger {
        background: rgba(0,0,0,0);
        border-color: #51ACFD;
        border-radius: 0;
    }
    .upload-image .el-upload-dragger .el-icon-upload {
        color: #51ACFD;
        font-size: 32px;
        line-height: 32px;
        margin: 60px 0 5px;
    }
    .upload-image .image-preview {
        max-height: 100%;
        max-width: 100%;
        padding: 10px;
    }
    .colorpicker-box .el-input__prefix {
        display: flex;
        align-items: center;
    }
    .colorpicker-box .el-color-picker__trigger {
        border: none;
    }
    .colorpicker-box .el-input--prefix .el-input__inner {
        padding-left: 40px;
    }
    .alignment-box .el-radio__input {
        display: none;
    }
    .alignment-box .el-radio__label {
        padding: 0;
    }
    .alignment-box .is-checked .alignment-icon > span {
        background: #51ACFD;
    }
    .alignment-box label.el-radio {
        width: 19px;
        min-width: 19px;
        margin-right: 15px;
    }
    .range-box .el-slider__runway {
        height: 1px;
        background: #C4C4C4;
    }
    .range-box .el-slider__bar {
        height: 1px;
    }
    .range-box .el-slider__button-wrapper {
        top: -17px;
    }


    .btn {
        margin-top: 20px;
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
        background-color: transparent;
    }
    .btn:hover {
        background-color: #51acfd;
        color: #fff;
    }
</style>