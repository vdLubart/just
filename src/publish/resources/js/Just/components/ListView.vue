<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <ul v-else>
            <li v-for="(label, key) in content" :key="key">
                <slink :href="'/settings/layout/' + key">{{ label }}</slink>
            </li>
        </ul>
    </div>

</template>

<script>
    import Link from './Link';

    export default {
        name: "ListView",

        components: {Link},

        props: ['content', 'isWaitingData']
    }
</script>

<style scoped>

</style>