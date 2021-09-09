<template>
    <div>
        <label v-if="label" class="block" :for="id">{{ label }}:</label>
        <input :id="id" ref="input" v-bind="$attrs" class="" :class="{ error: error }" :type="type" :value="value" :placeholder="placeholder" @input="$emit('input', $event.target.value)" />
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            id: {
                type: String,
                default() {
                    return `text-input-${this._uid}`
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
    input {
        background: #333333;
        border: 1px solid #FFFFFF;
        padding: 6px 12px;
        color: #FFFFFF;
        width: 590px;
        max-width: 100%;
    }
</style>
