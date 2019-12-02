<template>

    <div class="settings-component" v-show="visibility" v-on:keyup.esc="closeModal">
        <div class="settings-component__background" @click="closeModal"></div>
        <div class="settings-component__card">
            <header class="settings-component__card__header">
                <span class="settings-component__card__header__title" v-html="caption"></span>
                <a href="#"  class="settings-component__card__header__close"  @click.prevent="closeModal" title='settings.actions.close'><i class="fa fa-times"></i></a>
            </header>
            <alert v-if="Object.keys(alertNotes).length" :notes="alertNotes" :status="alertStatus" :renderHtml="alertRenderHtml"></alert>

            <content-view :type="contentType"></content-view>
        </div>
    </div>

</template>

<script>
    import Alert from './Alert';
    import ContentView from './Content';

    export default {
        name: "Settings",

        components: { Alert, ContentView },

        props: {
            title: {type: String},                                      // modal caption
            visible: {type: Boolean, default: false},                   // modal visibility
            alert: {type: Object, default: () => {return {};} },
            alertStatus: {type: String, default: "success"},
            alertHtml: {type: Boolean, default: false}
        },

        data() {
            return {
                content: "Loading data...",
                contentType: 'list',
                caption: this.title,
                visibility: this.visible,
                alertNotes: this.alert,
                alertRenderHtml: this.alertHtml,
                contentIsReady: false
            }
        },

        methods: {
            closeModal(){
                this.visibility = false;
            }
        },

        watch: {
            title(val){
                this.caption = val;
            },

            visible(val){
                this.visibility = val;
            },

            alert(val){
                this.alertNotes = val;
            }
        },
    }
</script>

<style scoped>

    .settings-component{
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
        position: fixed;
        z-index: 100;
        bottom: 0;
        left: 0;
        right: 0;
        top: 0;
    }

    .settings-component__background{
        background-color: rgba(10,10,10,0.5);
        bottom: 0;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
    }

    .settings-component__card{
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 40px);
        overflow: hidden;
        z-index: 100;
        min-width: 33%;
    }

    .settings-component__card__header{
        font-weight: bold;
        font-size: large;
        border-bottom: 1px solid #dbdbdb;
        align-items: center;
        background-color: #f5f5f5;
        display: flex;
        flex-shrink: 0;
        justify-content: space-between;
        padding: 20px;
        position: relative;
    }

    .settings-component__card__body{
        background-color: #fff;
        flex-grow: 1;
        flex-shrink: 1;
        overflow: auto;
        padding: 20px;
    }

</style>