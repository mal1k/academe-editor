<template>
    <div>
        <div class="header-placeholder"></div>
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
    .el-button--medium {
        padding: 7px 20px;
    }
    .el-message-box .el-button {
        border-radius: 4px;
        color: #FFFFFF;
        padding: 7px 15px;
    }
    .el-message-box .el-button:hover {
        background: rgba(0,0,0,0);
        color: #51ACFD;
        border-color: #51ACFD;
    }
    .el-message-box .el-button--primary:hover {
        background: #51ACFD;
        color: #FFFFFF;
    }
    .el-select__input {
        color: #ffffff;
    }
    .el-tag.el-tag--info .el-tag__close {
        color: #232323;
    }
    .el-checkbox.accept {
        display: flex;
    }
    .el-checkbox.accept .el-checkbox__input {
        margin-top: 4px;
    }
    .el-checkbox.accept .el-checkbox__label {
        line-height: 22px;
    }
    .el-loading-mask {
        z-index: 2010;
    }

</style>