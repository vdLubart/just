<template>

    <div :id="name" v-html="content" v-bind="parameters"></div>

</template>

<script>
    export default {
        name: "HtmlBlock",

        props:{
            name: {type: String},
            value: String,
            parameters: {type: Object}
        },

        data(){
            return {
                content: this.value
            }
        },
    }
</script>

<style scoped>

</style>