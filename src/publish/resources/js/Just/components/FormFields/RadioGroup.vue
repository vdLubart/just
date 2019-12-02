<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <div v-for="(radioLabel, radioValue) in radioButtons">
            <label class="radio-group__normalFont">
                <input type="radio" :name="name" :value="radioValue" :checked="radioValue === value" @input="handleInput"> {{ radioLabel }}
            </label>
        </div>
    </block>

</template>

<script>
    import InputBase from './InputBase';
    import _ from 'lodash';

    export default {
        name: "RadioGroup",

        extends: InputBase,

        props: {
            radioButtons: {type: Object, function (){ return {} }},                      // format should be {"radio value":"radio label"}
        },

        created(){
            if(!_.has(this.radioButtons, this.value)){
                this.content = null;
            }
        }
    }
</script>

<style scoped>

    .radio-group__normalFont{
        font-weight: normal;
    }

</style>