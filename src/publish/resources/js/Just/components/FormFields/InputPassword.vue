<template>

    <block :no-wrap="noWrap" :id="name" :required="required" :label="label" :withoutLabel="withoutLabel" >
        <lubart-password :name="name" :placeholder="placeholder" v-model="content" @input="handleInput" :no-wrap="true" v-bind="parameters"></lubart-password>
    </block>

</template>

<script>
    import { InputPassword as lubartPassword} from 'lubart-vue-input-component';
    import InputBase from './InputBase';

    export default {
        name: "InputPassword",

        extends: InputBase,

        components: {lubartPassword},

        props: {
            parameters: { type: Object },
            placeholder: {type: String},
            value: {}
        },

        data(){
            return {
                content: null
            }
        },

        methods: {
            handleInput (e) {
                this.$emit('input', this.content);
            }
        },

        watch: {
            content(val){
                this.$emit('input', val);
            }
        }
    }
</script>
