<template>
    <header>
        <div class="left-part">
            <a href="/"><img src="/wp-content/themes/academe/assets/img/logo.svg" class="h-6" /></a>
        </div>
        <div v-if="store.active_page == 'slides'" class="central-part">
            <h1 class="lesson-title">
                {{store.meta.title}}
            </h1>
        </div>
        <div class="right-part">

            <el-button type="default" size="medium" @click="saveLesson()">Save</el-button>

            <el-button type="primary" size="medium"
                :disabled="!store.meta.accept"
                @click="store.active_page = 'slides'">
                Create
            </el-button>

            <el-dropdown trigger="click">
                <i class="el-icon-menu" style="font-size:28px; margin-left: 40px; cursor: pointer;"></i>

                <el-dropdown-menu slot="dropdown">
                    <a href="/"><el-dropdown-item>Back To Academe</el-dropdown-item></a>
                    <el-dropdown-item v-if="store.active_page == 'slides'" @click.native="store.active_page='meta'">Edit Metadata</el-dropdown-item>
                    <el-dropdown-item v-if="store.active_page == 'meta'" @click.native="store.active_page='slides'">Edit Slides</el-dropdown-item>
                    <a href="#" target="_blank" id="previewButton"><el-dropdown-item>Preview</el-dropdown-item></a>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
    </header>
</template>

<script>
    import saveLessonService from "../../../save-lesson-service";

    export default {
        name: "Header",
        computed: {
            store() {
                return this.$store.state.LessonEditor;
            }
        },
        mounded() {

        },
        methods: {
            saveLesson() {
                saveLessonService.initSave();
            }
        }
    }
</script>

<style scoped>
    header > div {
        flex: 1;
    }
    header .lesson-title {
        color: #FFFFFF;
        font-size: 18px;
        font-weight: 500;
        text-transform: uppercase;
    }

    header .central-part {
        display: flex;
        justify-content: center;
    }
    header .right-part {
        display: flex;
        justify-content: flex-end;
    }
</style>