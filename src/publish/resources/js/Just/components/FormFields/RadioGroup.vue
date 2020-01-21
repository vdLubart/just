<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <label v-for="(radioLabel, radioValue) in options" class="radio-group__normalFont">
            <input type="radio" :name="name" :value="radioValue" :checked="content == radioValue" @input="handleInput" v-bind="parameters"> {{ radioLabel }}
        </label>
    </block>

</template>

<script>
    import InputBase from './InputBase';

    export default {
        name: "RadioGroup",

        extends: InputBase,

        data(){
            return {
                options: this.$parent.element.options
            }
        },

        created(){
            if(!_.has(this.options, this.value)){
                this.content = null;
            }
        }
    }
</script>

<style scoped>

    .radio-group__normalFont{
        display: block;
        font-weight: normal;
    }

</style>