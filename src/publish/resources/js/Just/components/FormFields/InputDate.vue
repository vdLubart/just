<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel">
        <date-picker lang="en" :inputName="name" :format="format" :placeholder="placeholder" v-model="content" @input="handleInput"></date-picker>
    </block>

</template>

<script>
    import InputBase from './InputBase';
    import DatePicker from 'vue2-datepicker';
    import moment from 'moment';

    export default {
        name: "InputDate",

        extends: InputBase,

        components: { DatePicker },

        props: {
            value: {type: String},                                  // format should be "YYYY-MM-DD"
            placeholder: {type: String, default: "Select Date"},
            format: {type: String, default: "MMMM Do YYYY"},         // value is similar to moment.js
        },

        created(){
            if(this.value !== undefined && this.value !== null && this.value != ""){
                this.content = new Date(this.value);
            }
        },

        methods: {
            handleInput (e) {
                let date = new Date(this.content);
                this.$emit('input', moment.unix(date.getTime()/1000).format('YYYY-MM-DD'));
            }
        }
    }
</script>

<style scoped>

</style>