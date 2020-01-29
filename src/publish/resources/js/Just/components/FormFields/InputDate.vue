<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <lubart-date :name="name" :placeholder="placeholder" v-model="content" @input="handleInput" v-bind="parameters" :no-wrap="true"></lubart-date>
    </block>

</template>

<script>
    import { InputDate as LubartDate } from 'lubart-vue-input-component';
    import Block from './Block';
    import InputBase from './InputBase';

    export default {
        name: "InputDate",

        extends: InputBase,

        components: {Block, LubartDate},

        props: {
            parameters: { type: Object },
            placeholder: {type: String},
        },

        methods: {
            handleInput (e) {
                this.$emit('input', this.content);
            }
        }
    }
</script>

<style>

    span.mx-input-append{
        right: 5%;
    }

    .mx-calendar-content .cell.actived{
        background-color: #77BAC0;
    }

    .mx-panel-date td.today{
        color: #77BAC0;
    }

</style>