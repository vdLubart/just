<template>
    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <select :multiple="multiple" @input="handleInput">
            <slot></slot>
        </select>
    </block>

</template>

<script>
    require('chosen-js');

    import Block from './Block';

    export default {
        name: "SelectChosen",

        components: { Block },

        props:{
            name: {type: String},
            required: {type: Boolean, default: false},
            noWrap: {type: Boolean, default: false},
            withoutLabel: {type: Boolean, default: false},
            value: [String, Array],
            label: {type: String, default: ""},
            multiple: {type: Boolean, default: false},
            chosen: {type: Boolean, default: true}
        },

        data(){
            return {
                content: this.value
            }
        },

        methods:{
            applyChosen(){
                $(this.$el)
                    .val(this.value)
                    .chosen({})
                    .on("change", e => this.$emit('onChange', $(this.$el).val()));
            },

            handleInput (e) {
                this.content = e.target.value;
                this.$emit('input', this.content);
            }
        },

        mounted(){
            if(this.chosen){
                this.applyChosen();
            }
        },

        watch:{
            value(val){
                $(this.$el).val(val).trigger('chosen:updated');
            },

            chosen(val){
                if(val){
                    this.applyChosen();
                }
                else{
                    $(this.$el).chosen('destroy');
                }

            }
        },

        destroyed() {
            $(this.$el).chosen('destroy');
        }
    }
</script>

<style>

    /* Chosen override */

    .chosen-container-single .chosen-single{
        border-radius: initial;
        height: 29px;
        background: #fff;
    }

    .chosen-container .chosen-results li.highlighted {
        background-color: #77bac0;
        background-image: none;
        color: #fff;
    }

    .chosen-container-active .chosen-single {
        border: 1px solid #77bac0;
    }

    .chosen-container-active .chosen-choices{
        border: 1px solid #77bac0;
    }

    /* END Chosen override */

</style>