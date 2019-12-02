<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <label class="checkbox-group__normalFont">
            <input type="checkbox" :name="name" :value="ticValue" :checked="content" @input="handleInput"> {{ ticLabel }}
        </label>
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputCheckbox",

        extends: InputBase,

        props: {
            value: {type: Boolean, default: false},
            ticValue: {type: String, default: ""},
            ticLabel: {type: String}
        },

        methods: {
            handleInput (e) {
                this.$emit('input', e.target.checked)
            }
        }
    }
</script>

<style scoped>

    .checkbox-group__normalFont{
        font-weight: normal;
    }

</style>