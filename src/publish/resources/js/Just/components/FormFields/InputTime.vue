<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel" >
        <timeselector display-format="HH:mm" return-format="HH:mm" v-model="time" @input="handleInput"></timeselector>
    </block>

</template>

<script>
    import Timeselector from 'vue-timeselector';
    import Block from './Block';
    import InputBase from './InputBase';

    export default {
        name: "InputTime",

        extends: InputBase,

        components: { Timeselector, Block },

        data(){
            return {
                time: this.value
            }
        },

        created(){
            let today = new Date();
            today.setHours(_.split(this.value, ":")[0]);
            today.setMinutes(_.split(this.value, ":")[1]);
            this.time = today;
        },

        methods: {
            handleInput (e) {
                let time = new Date(this.time);
                this.content = this.leadingZero(time.getHours()) + ":" + this.leadingZero(time.getMinutes());
                this.$emit('input', this.content);
            },

            leadingZero(num){
                if(num < 10){
                    num = '0' + num;
                }

                return num;
            }
        }
    }
</script>

<style>

    .timeselector__box__item--is-selected{
        background-color: #77BAC0;
    }

    .vtimeselector__box__item:not(.timeselector__box__item--is-disabled):not(.timeselector__box__item--is-selected):hover {
        background: #eee;
    }

    .vtimeselector__clear{
        right: 5%;
    }

    .vtimeselector__box{
        width:95%;
    }

</style>