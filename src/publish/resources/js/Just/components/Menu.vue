<template>

    <div>
        <div class="settings-menu-component thumbnail">
            <div :class="{'inactive': !item.item.isActive}" class="level1" v-for="(item, key) in content" :key="key">
                <div class="settings-menu-component__line">
                    <div>{{ !item.item.isActive ? '[' + __('common.deactivated') + ']' : '' }} {{ item.item.title }}</div>
                    <edit-item :url="key" :activating="true" :moving="true" :inactive="!item.item.isActive"></edit-item>
                </div>
                <div :class="{'inactive': !subItem.item.isActive}" class="level2" v-for="(subItem, subKey) in item.sub" :key="key+'-'+subKey">
                    <div class="settings-menu-component__line">
                        <div>{{ !subItem.item.isActive ? '[' + __('common.deactivated') + ']' : '' }} {{ subItem.item.title }}</div>
                        <edit-item :url="subKey" :activating="true" :moving="true" :inactive="!subItem.item.isActive"></edit-item>
                    </div>
                    <div :class="{'inactive': !sub2Item.item.isActive}" class="level3" v-for="(sub2Item, sub2Key) in subItem.sub" :key="key+'-'+subKey+'-'+sub2Key">
                        <div class="settings-menu-component__line">
                            <div>{{ !sub2Item.item.isActive ? '[' + __('common.deactivated') + ']' : '' }} {{ sub2Item.item.title }}</div>
                            <edit-item :url="sub2Key" :activating="true" :moving="true" :inactive="!sub2Item.item.isActive"></edit-item>
                        </div>
                        <div :class="{'inactive': !sub3Item.item.isActive}" class="level4" v-for="(sub3Item, sub3Key) in sub2Item.sub" :key="key+'-'+subKey+'-'+sub2Key+'-'+sub3Key">
                            <div class="settings-menu-component__line">
                                <div>{{ !sub3Item.item.isActive ? '[' + __('common.deactivated') + ']' : '' }} {{ sub3Item.item.title }}</div>
                                <edit-item :url="sub3Key" :activating="true" :moving="true" :inactive="!sub3Item.item.isActive"></edit-item>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import EditItem from './EditItem';

export default {
    name: "Menu",

    components: { EditItem },

    props: ['content', 'isWaitingData'],

    created() {
        console.log(this.content);
    }
}
</script>

<style scoped>
    .settings-menu-component{
        display: flex;
        flex-wrap: wrap;
    }

    .settings-menu-component > div{
        flex: 1 0 98%;
    }

    .settings-menu-component__line{
        display: flex;
        flex-wrap: wrap;
    }

    .settings-menu-component .inactive .settings-menu-component__line:hover{
        background-color: #ddd;
    }

    .settings-menu-component__line:hover{
        background-color: #fff;
    }

    .settings-menu-component__line > div{
        flex: 1 0 48%;
        margin: 1%;
    }

    .level2 .settings-menu-component__line div:first-child{
        padding-left: 30px;
    }

    .level3 .settings-menu-component__line div:first-child{
        padding-left: 60px;
    }

    .level4 .settings-menu-component__line div:first-child{
        padding-left: 90px;
    }

    .settings-menu-component .inactive{
        background-color: #ccc;
    }
</style>
