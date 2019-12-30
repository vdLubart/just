<template>
    <div :class="'alert-component alert-component__' + status" class="alert-structure">
        <ul>
            <li v-if="renderHtml" v-for="note in messages" v-html="note"></li>

            <li v-if="!renderHtml" v-for="note in messages">{{ note }}</li>
        </ul>

        <div v-if="withConfirmation">
            <input-button :label="__('common.no')" @click="reset"></input-button>
            <input-button :label="__('common.yes')" @click="confirmAction"></input-button>
        </div>
    </div>
</template>

<script>
    import {InputButton} from 'lubart-vue-input-component';

    export default {
        name: "Alert",

        components: { InputButton },

        props: {
            status: {type: String, default: "success"}, // available values are: danger, success, info, warning
            notes: {type: Object},
            renderHtml: {type: Boolean, default: false},
            withConfirmation: {type: Boolean, default: false},
            confirmationAction: {type:Function, default: ()=>false}
        },

        data(){
            return {
                messages: this.notes
            }
        },

        methods:{
            confirmAction(){
                this.confirmationAction();

                this.reset();
            },

            reset(){
                this.$parent.resetAlert();
            }
        },

        watch:{
            notes(val){
                this.messages = val;
            }
        }
    }
</script>

<style scoped>

    .alert-structure{
        display: flex;
    }

    .alert-structure ul{
        flex-grow: 5;
    }

    .alert-structure div{
        flex-grow: 1;
        text-align: right;
    }

    .alert-component{
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
        font-weight: 400;
        border-left-width: 4px;
    }

    .alert-component__danger {
        border-color: #a94442;
        background-color: #f2dede;
        color: #a94442;
    }

    .alert-component__success {
        background-color: #dff0d8;
        border-color: #d0e9c6;
        color: #3c763d;
    }

    .alert-component__info{
        background-color: #d9edf7;
        border-color: #bcdff1;
        color: #31708f;
    }

    .alert-component__warning{
        background-color: #fcf8e3;
        border-color: #faf2cc;
        color: #8a6d3b;
    }

</style>