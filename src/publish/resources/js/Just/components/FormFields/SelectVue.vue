<template>
    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <v-select :value="{'label': options[content], 'value':content}" :options="labeledOptions" v-bind="parameters" :multiple="multiple" :placeholder="placeholder" @input="handleInput"></v-select>
    </block>

</template>

<script>
    import Block from './Block';
    import vSelect from 'vue-select';

    export default {
        name: "SelectVue",

        components: { Block, vSelect },

        props:{
            name: {type: String},
            required: {type: Boolean, default: false},
            noWrap: {type: Boolean, default: false},
            withoutLabel: {type: Boolean, default: false},
            value: [String, Number, Array],
            label: {type: String, default: ""},
            multiple: {type: Boolean, default: false},
            parameters: {type: Object},
            placeholder: {type: String, default: "Choose an option"}
        },

        data(){
            return {
                content: this.value,
                options: this.$parent.element.options,
                labeledOptions: []
            }
        },

        methods:{
            handleInput (e) {
                this.content = e.value;

                this.$emit('input', this.content);
            }
        },

        mounted(){
            Object.keys(this.options).forEach(key => {
                this.labeledOptions.push({'label': this.options[key], 'value':key});
            });
        }
    }
</script>

<style>

    .vs__dropdown-toggle{
        border-radius: 4px;
        height: 34px;
        background: #fff;
        border-color: #dbdbdb;
    }

    .vs--open .vs__dropdown-toggle,
    .vs__dropdown-menu{
        border: 1px solid #77bac0;
    }

    .vs__dropdown-option--highlight {
        background: #77bac0 !important;
        color: #fff;
    }

    .v-select{
        width:95%;
    }

</style>