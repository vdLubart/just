<template>

    <div class="itemTab">
        <div v-for="(tabContent, key) in content" class="tab" :class="active === key ? 'active':''">
            <slink :href="tabContent.url"><i :class="'fa fa-' + tabContent.icon"></i> {{ __('itemTabs.'+key) }}</slink>
        </div>
    </div>

</template>

<script>
    import Slink from './Link';

    export default {
        name: "ItemTabs",

        components: { Slink },

        props: {
            tab: {type: String},
            blockId: {type: String},
            itemId: {type: String}
        },

        data(){
            return {
                content: {
                    edit: {
                        icon: 'pencil-alt',
                        url: '/settings/block/' + this.blockId + '/item/' + this.itemId
                    },
                    createRelation: {
                        icon: 'plus',
                        url: '/settings/block/' + this.blockId + '/item/' + this.itemId + '/related/0'
                    },
                    relations: {
                        icon: 'link',
                        url: '/settings/block/' + this.blockId + '/item/' + this.itemId + '/related/list'
                    }
                },
                active: this.tab,
                id: this.blockId

            }
        },

        watch:{
            tab(val){
                this.active = val;
            }
        }
    }
</script>

<style scoped>
    div.itemTab{
        display: flex;
        align-items: flex-start;
        background-color: #fafafa;
    }

    div.tab{
        min-width: 50px;
        padding: 15px 10px;
        font-size: medium;
    }

    div.tab:hover{
        background-color: #f5f5f5;
    }

    div.tab.active{
        background-color: #eee;
    }
</style>