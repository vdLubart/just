<template>

    <div>
        <component v-if="!!parameters.vueComponent" :is="parameters.vueComponent" v-bind="parameters.vueComponentAttrs"></component>
        <div v-else :id="name" v-html="content" v-bind="parameters"></div>
    </div>

</template>

<script>
    export default {
        name: "HtmlBlock",

        props:{
            name: {type: String},
            value: {type: [String, Array]},
            parameters: {type: Object}
        },

        data(){
            return {
                content: this.value
            }
        },

        watch:{
            content(){
                this.$emit('input', this.content);
            }
        }
    }
</script>

<style scoped>

</style>
