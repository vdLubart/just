<template>
    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <v-select :value="selectValue" :options="labeledOptions" :multiple="isMultiple" :placeholder="placeholder" v-bind="parameters" @input="handleInput"></v-select>
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
                isMultiple: this.multiple || this.parameters.multiple,
                selectValue: null,
                options: this.$parent.element.options,
                labeledOptions: []
            }
        },

        methods:{
            handleInput (e) {
                if(this.isMultiple){
                    this.selectValue = e;
                    this.content = [];
                    e.forEach(val => {
                        this.content.push(parseInt(val.value));
                    });
                }
                else {
                    this.selectValue = {'label': this.options[e.value], 'value':e.value};
                    this.content = e.value;
                }

                this.$emit('input', this.content);
            }
        },

        mounted(){
            Object.keys(this.options).forEach(key => {
                this.labeledOptions.push({'label': this.options[key], 'value':key});
            });

            if(this.isMultiple){
                this.selectValue = [];

                if(!_.isEmpty(this.content)) {
                    Object.values(this.content).forEach(key => {
                        this.selectValue.push({'label': this.options[key], 'value': key});
                    });
                }
            }
            else{
                this.selectValue = {'label': this.options[this.content], 'value':this.content};
            }
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
