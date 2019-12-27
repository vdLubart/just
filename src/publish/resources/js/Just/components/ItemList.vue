<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <div v-else class="settings-list-component">
            <div class="thumbnail" v-for="(item, key) in content" :key="key">
                <slink v-if="item.image !== undefined" :href="'/settings/' + key">
                    <img :src="item.image" />
                </slink>

                <h1 v-if="item.featureIcon !== undefined" class='featureItem'>
                    {{ item.featureIcon }}
                </h1>

                <div v-if="item.text !== undefined">
                    {{ item.text }}
                </div>

                <div class="caption">
                    <slink :href="'/settings/' + key">
                        <strong>{{ item.caption }}</strong>
                    </slink><br/>

                    <edit-item :url="key" :activating="activating" :moving="moving"></edit-item>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import List from './List';
    import EditItem from './EditItem';

    export default {
        name: "ItemList",

        components: { EditItem },

        extends: List,

        data(){
            return {
                itemName: _.first(_.first(Object.keys(this.content)).split('/')),
                activating: _.includes(['layout'], this.itemName),
                moving: _.includes(['layout'], this.itemName)
            }
        }
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