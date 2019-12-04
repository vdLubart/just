<template>

    <ul>
        <li v-for="(label, key) in list" :key="key">
            <slink :href="'/settings/layout/' + key">{{ label }}</slink>
        </li>
    </ul>

</template>

<script>
    import Link from './Link';

    export default {
        name: "ListView",

        components: {Link},

        data(){
            return {
                list: this.$parent.$parent.content
            }
        }
    }
</script>

<style scoped>

</style>