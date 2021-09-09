<template>
    <div class="corners-wrap">
        <div class="corner left-top-corner"></div>
        <div class="corner right-top-corner"></div>
        <div class="corner right-bottom-corner"></div>
        <div class="corner left-bottom-corner"></div>
    </div>
</template>

<script>
    export default {
        name: "SlideBlockCorners"
    }
</script>

<style scoped>
    .corners-wrap {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        border: 3px solid #51acfd;
    }
    .corners-wrap .corner {
        height: 16px;
        width: 16px;
        background: white;
        border: 2px solid #51acfd;
        position: absolute;
    }
    .corners-wrap .left-top-corner {
        top: -8px;
        left: -8px;
    }
    .corners-wrap .right-top-corner {
        top: -8px;
        right: -8px;
    }
    .corners-wrap .right-bottom-corner {
        bottom: -8px;
        right: -8px;
    }
    .corners-wrap .left-bottom-corner {
        bottom: -8px;
        left: -8px;
    }
</style>