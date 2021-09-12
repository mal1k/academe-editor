<template>
    <div>
        <slot name="header"></slot>
        <slot name="editor"></slot>
    </div>
</template>

<script>
    export default {
        name: "Layout"
    }
</script>

<style>
    /*.dropdown.ui.dark {*/
    /*    background: #333333;*/
    /*    color: #ffffff;*/
    /*    border: 1px solid #ffffff;*/
    /*    border-radius: 0;*/
    /*}*/
    /*.ui.dropdown.dark>.dropdown.icon:before {*/
    /*    color: #FFFFFF;*/
    /*}*/
    body.page-template-page-lesson-editor {
        background: #232323;
    }
    .el-dialog {
        border: 3px solid #FFFFFF;
        border-radius: 10px
    }
    .el-dialog__title {
        font-weight: 600;
        color: #FFFFFF;
    }
    .el-dialog__body {
        padding-top: 10px;
    }
    .el-dialog__header {
        padding-top: 40px;
    }
    .el-dropdown-menu {
        border-radius: 4px;
    }
    .el-dropdown-menu .popper__arrow {
        display: none;
    }
    .el-textarea__inner, .el-input__inner {
        background: rgba(0,0,0,0);
    }
    .el-select-dropdown__item.is-disabled {
        opacity: .5;
    }
</style>