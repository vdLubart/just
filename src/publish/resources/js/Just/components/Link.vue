<template>

    <a :href="href" @click.prevent="navigate">
        <slot></slot>
    </a>

</template>

<script>
    export default {
        name: "Link",

        props: {
            href: {type:String, default: "/"}
        },

        methods:{
            navigate() {
                return this.$root.navigate(this.href);
            }
        }
    }
</script>

<style scoped>

</style>