<template>

    <div class="blockTab">
        <div v-for="(tabContent, key) in content" class="tab" :class="active === key ? 'active':''">
            <slink :href="tabContent.url"><i :class="'fa fa-' + tabContent.icon"></i> {{ __('blockTabs.'+key) }}</slink>
        </div>
    </div>

</template>

<script>
    import Slink from './Link';

    export default {
        name: "BlockTabs",

        components: { Slink },

        props: {
            tab: {type: String},
            blockId: {type: String}
        },

        data(){
            return this.initData();
        },

        methods:{
            initData(){
                return {
                    content: {
                        content: {
                            icon: 'list',
                            url: '/settings/block/' + this.blockId
                        },
                        blockSettings: {
                            icon: 'cogs',
                            url: '/settings/block/' + this.blockId + '/settings'
                        },
                        blockCustomization: {
                            icon: 'sliders-h',
                            url: '/settings/block/' + this.blockId + '/customization'
                        },
                        createItem: {
                            icon: 'plus',
                            url: '/settings/block/' + this.blockId + '/item/0'
                        }
                    },
                    active: this.tab,
                    id: this.blockId
                }
            }
        },

        watch:{
            tab(){
                this.active = this.initData().active;
            },

            blockId(){
                this.content = this.initData().content;
                this.id = this.initData().id;
            }
        }
    }
</script>

<style scoped>
    div.blockTab{
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
