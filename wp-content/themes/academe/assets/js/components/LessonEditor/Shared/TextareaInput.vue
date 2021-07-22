<template>
    <div>
        <label v-if="label" class="block" :for="id">{{ label }}:</label>
        <textarea :id="id" ref="input" v-bind="$attrs" class="" :class="{ error: error }" :value="value" @input="$emit('input', $event.target.value)" />
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            id: {
                type: String,
                default() {
                    return `textarea-input-${this._uid}`
                },
            },
            type: {
                type: String,
                default: 'text',
            },
            value: String,
            label: String,
            error: String,
            placeholder: String,
        },
        methods: {
            focus() {
                this.$refs.input.focus()
            },
            select() {
                this.$refs.input.select()
            },
            setSelectionRange(start, end) {
                this.$refs.input.setSelectionRange(start, end)
            },
        },
    }
</script>

<style scoped>
    textarea {
        width: 100%;
        height: 150px;
        border: 1px solid #FFFFFF;
        background: #333333;
        color: #FFFFFF;
        padding: 6px 12px;
    }
</style>
