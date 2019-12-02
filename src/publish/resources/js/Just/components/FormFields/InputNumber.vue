<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <input type="number" :name="name" :id="name" :value="content" class="input-component__text" :placeholder="placeholder" @input="handleInput"/>
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "InputNumber",

        extends: InputBase,

        props: {
            value: {type: Number},
            placeholder: {type: String, default: ""},
            min: {type: Number},
            max: {type: Number}
        },

        methods: {
            handleInput (e) {
                this.content = parseInt(e.target.value);
                if(this.min != undefined && this.content < this.min){
                    this.content = this.min;
                }

                if(this.max != undefined && this.content > this.max){
                    this.content = this.max;
                }

                this.$emit('input', this.content);
            }
        }
    }
</script>

<style scoped>

    .input-component__text{
        border: 1px solid #dbdbdb;
        border-radius: 4px;
        padding: 5px;
    }

    .input-component__text:focus{
        border: 1px solid #77BAC0;
    }

</style>