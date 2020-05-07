<template>

    <div class="iconSet">

        <div class="bundleSelection">
            <v-select :value="bundles[iconSet]" :options="labeledOptions" @input="chooseBundle"></v-select>
        </div>

        <div class="chosenIcon">
            <component v-if="selectedIcon.icon_set != undefined" :is="selectedIcon.icon_set.tag" :class="selectedIcon.icon_set.class + ' ' + selectedIcon.class"></component>
        </div>

        <div class="iconBundle">
            <a href="" @click.prevent="chooseIcon(item)" v-for="item in icons" :data-id="item.id" class='featureIcon'>
                <component :is="item.icon_set.tag" :class="item.icon_set.class + ' ' + item.class"></component>
            </a>

            <paginate
                    :page-count="pageCount"
                    :click-handler="getIcons"
                    :prev-text="'Prev'"
                    :next-text="'Next'"
                    class="pagination"
            >
            </paginate>
        </div>

    </div>

</template>

<script>
    import Paginate from 'vuejs-paginate';
    import vSelect from 'vue-select';

    export default {
        name: "IconSet",

        components: {Paginate, vSelect},

        props:{
            bundles: {type: Object},
            value: {type:Object}
        },

        data(){
            return {
                icons: {},
                page: 1,
                iconSet: 1,
                selectedIcon: {},
                pageCount: 1,
                labeledOptions: []
            }
        },

        mounted(){
            Object.keys(this.bundles).forEach(key => {
                this.labeledOptions.push({'label': this.bundles[key], 'value':key});
            });
        },

        created() {
            this.iconSet = 1;
            this.getIcons(1);
            this.selectedIcon = _.isNull(this.value) ? {} : this.value;
        },

        methods:{
            async getIcons(page){
                await axios({
                    url: "/iconset/" + this.iconSet + '/' + page,
                    method: 'POST',
                    data: {
                        _token: $("meta[name=csrf-token]").attr('content')
                    }
                })
                    .then((response) => {
                        this.icons = response.data.data;
                        this.pageCount = response.data.last_page;
                    })
                    .catch( (data) => {
                        console.log("Cannot get icons data.")
                        console.log(data);
                    });
            },

            chooseIcon(item){
                this.selectedIcon = item;
                this.$parent.content = item.id.toString(10);
            },

            chooseBundle(e){
                this.iconSet = e.value;
                this.getIcons(1);
            }
        }
    }
</script>

<style lang="scss">

    a.featureIcon{
        padding: 10px;
        font-size: medium;
    }

    div.iconSet div.chosenIcon{
        font-size: 50pt;
        padding: 20px;
        flex-basis: 15%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .iconSet{
        display: flex;
        flex-wrap: wrap;
    }

    div.bundleSelection{
        flex-basis: 100%;
    }

    div.iconBundle{
        flex-basis: 85%;
        display: flex;
        flex-wrap: wrap;
    }

    .iconBundle a{
        flex-basis: 40px;
    }

</style>