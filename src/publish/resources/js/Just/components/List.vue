<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <div v-else class="settings-list-component">
            <div class="thumbnail" v-for="(item, key) in content" :key="key">
                <div class="caption">
                    <slink :href="'/settings/' + key">
                        <h1 class='featureItem'>
                            <i :class="'fa fa-' + item.icon"></i>
                        </h1>

                        <strong>{{ item.label }}</strong>
                    </slink>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import Link from './Link';

    export default {
        name: "List",

        components: {Link},

        props: ['content', 'isWaitingData']
    }
</script>

<style scoped>

    .settings-list-component{
        display: flex;
        flex-wrap: wrap;
    }

    .settings-list-component > div{
        flex: 1 0 25%;
        margin: 5px;
    }

</style>