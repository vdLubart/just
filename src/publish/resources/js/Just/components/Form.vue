<template>

    <form action="">

        <field v-for="(element, key) in form.unGrouppedElements" :element="element" :key="key"></field>

    </form>
    
</template>

<script>
    import Field from './FormField';

    export default {
        name: "Form",

        components: { Field },

        data(){
            return {
                form: this.$parent.$parent.content
            }
        }
    }
</script>

<style scoped>

</style>