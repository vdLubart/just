<template>

    <div>
        <span v-if="isWaitingData">Loading data...</span>
        <span v-else-if="emptyContent">There are no items found...</span>
        <div v-else class="settings-list-component">
            <div class="thumbnail" v-for="(item, key) in content" :key="key" :class="'w'+ item.width + (item.isActive?'':' inactive')">
                <slink v-if="!isEmpty(item.image)" :href="'/settings/' + key">
                    <img :src="item.image" />
                </slink>

                <slink v-if="!isEmpty(item.featureIcon)" :href="'/settings/' + key">
                    <h1 v-if="!isEmpty(item.featureIcon)" class='featureItem' v-html="item.featureIcon"></h1>
                </slink>

                <div v-if="!isEmpty(item.text)">
                    {{ item.text }}
                </div>

                <div class="caption">
                    {{ !item.isActive ? '[' + __('common.deactivated') + ']' : '' }}
                    <slink :href="'/settings/' + key" v-if="!isEmpty(item.caption)">
                        <strong>{{ item.caption }}</strong>
                    </slink><br/>

                    <edit-item :url="key" :activating="activating" :moving="moving" :inactive="!item.isActive"></edit-item>
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
                itemName: '',
                activating: true,
                moving: true,
                emptyContent: false
            }
        },

        methods:{
            isEmpty(condition){
                return _.isEmpty(condition);
            }
        },

        mounted() {
            if(Object.keys(this.content).length){
                this.itemName = _.first(_.first(Object.keys(this.content)).split('/')),
                this.activating = !_.includes(['layout', 'page'], this.itemName),
                this.moving = !_.includes(['layout', 'page'], this.itemName)

                if(this.itemName === 'block' && this.$root.settings.responseParameters.blockType === 'events'){
                    this.moving = false;
                }
            }
            else{
                this.emptyContent = true;
            }
        }
    }
</script>

<style>

    .settings-list-component{
        display: flex;
        flex-wrap: wrap;
    }

    .settings-list-component > div{
        flex: 1 0 98%;
        margin: 1%;
    }

    .settings-list-component > div.w3{
        flex: 1 0 23%; /* (100% - 2x4x1%) / 4 = 23% */
    }

    .settings-list-component > div.w4{
        flex: 1 0 31%; /* (100% - 2x3x1%) / 3 = 31.3% */
    }

    .settings-list-component > div.w6{
        flex: 1 0 48%; /* (100% - 2x2x1%) / 2 = 48% */
    }

    .settings-list-component > div.w8{
        flex: 1 0 65%;
    }

    .settings-list-component > div.w9{
        flex: 1 0 73%;
    }

    .settings-list-component > div.w12{
        flex: 1 0 98%;
    }

    .settings-list-component .inactive{
        background-color: #ccc;
    }

</style>