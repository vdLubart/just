<template>

    <input type="hidden" :name="name" :id="name" class="input-component__hidden" :value="content" @input="handleInput" />

</template>

<script>
    export default {
        name: "InputHidden",

        props: {
            name: {type: String},
            value: {type: String, default: ""},
        },

        data(){
            return {
                content: this.value
            }
        },

        watch: {
            value(val){
                this.content = val;
            }
        },

        methods: {
            handleInput (e) {
                this.content = e.target.value;
                this.$emit('input', this.content);
            }
        }
    }
</script>