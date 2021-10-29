<template>
    <header>
        <div class="left-part">
            <img @click="saveConfirm()" src="/wp-content/themes/academe/assets/img/logo.svg" class="h-6" />
        </div>
        <div v-if="store.active_page == 'slides'" class="central-part">
            <h1 class="lesson-title">
                {{store.meta.title}}
            </h1>
        </div>
        <div class="right-part">

            <el-button v-if="store.status === 'publish' || store.status === 'private'" plain type="default" size="medium" @click="unpublishLesson()">
                Unpublish
            </el-button>
            <el-button v-else plain type="default" size="medium" @click="publishLesson()" :disabled="store.status === 'auto-draft'">
                Publish
            </el-button>


            <el-dropdown trigger="click">
                <div class="hamburger-menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M23 12.9795H1C0.447998 12.9795 0 12.5315 0 11.9795C0 11.4275 0.447998 10.9795 1 10.9795H23C23.552 10.9795 24 11.4275 24 11.9795C24 12.5315 23.552 12.9795 23 12.9795Z" fill="white"/>
                        <path d="M23 5.3125H1C0.447998 5.3125 0 4.8645 0 4.3125C0 3.7605 0.447998 3.3125 1 3.3125H23C23.552 3.3125 24 3.7605 24 4.3125C24 4.8645 23.552 5.3125 23 5.3125Z" fill="white"/>
                        <path d="M23 20.6455H1C0.447998 20.6455 0 20.1975 0 19.6455C0 19.0935 0.447998 18.6455 1 18.6455H23C23.552 18.6455 24 19.0935 24 19.6455C24 20.1975 23.552 20.6455 23 20.6455Z" fill="white"/>
                    </svg>
                </div>

                <el-dropdown-menu slot="dropdown">
                    <span @click="saveConfirm()"><el-dropdown-item>Back To Academe</el-dropdown-item></span>
                        <!--<el-dropdown-item v-if="store.active_page == 'slides'" @click.native="store.active_page='meta'">Edit Metadata</el-dropdown-item>-->
                        <!--<el-dropdown-item v-if="store.active_page == 'meta'" @click.native="store.active_page='slides'">Edit Slides</el-dropdown-item>-->
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
            publishLesson() {
                if(this.store.status !== 'draft'){
                    this.$notify.error({
                        title: 'Publish error',
                        message: 'Please save the lesson first!'
                    });
                } else {
                    saveLessonService.initPublish();
                }
            },
            unpublishLesson() {
                saveLessonService.initUnpublish();
            },
            saveConfirm() {
                this.$confirm('Want to save your changes?', 'Warning', {
                    distinguishCancelAndClose: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, exit the editor',
                    type: 'warning'
                }).then(() => {
                    if(!this.store.meta.accept){ // show error if terms not accepted
                        this.$notify.error({
                            title: 'Save error',
                            message: 'Please accept the Terms Of Use and Privacy Policy!'
                        });
                    } else { // save lesson if terms accepted
                        saveLessonService.initSave({
                            'type': 'manual'
                        }).then(res => {
                            window.location.href = '/';
                        });
                    }
                }).catch(action => {
                    if (action === 'cancel') {
                        window.location.href = '/';
                    } // else stay here
                });
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
    header .hamburger-menu {
        cursor: pointer;
        margin-left: 24px;
    }
</style>