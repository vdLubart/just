<template>

    <div class="settings-component" v-show="visibility" v-on:keyup.esc="closeModal">
        <div class="settings-component__background" @click="closeModal"></div>
        <div class="settings-component__card">
            <header class="settings-component__card__header">
                <div>
                    <span v-for="(captionLabel, url, index) in caption" :key="index">
                        <slink :href="url">
                            <span class="settings-component__card__header__title" v-html="captionLabel"></span>
                        </slink><span v-if="(index+1) < Object.keys(caption).length"> :: </span>
                    </span>
                </div>
                <a href="#"  class="settings-component__card__header__close"  @click.prevent="closeModal" title='settings.actions.close'><i class="fa fa-times"></i></a>
            </header>
            <alert v-if="isAlertVisible" :notes="alertNotes" :status="alertType" :renderHtml="alertRenderHtml" :with-confirmation="confirmation" :confirmation-action="confirmationAction"></alert>

            <block-tabs v-if="!!responseParameters.blockTabs" :tab="responseParameters.blockTabs" :block-id="responseParameters.blockId"></block-tabs>

            <item-tabs v-if="!!responseParameters.itemTabs" :tab="responseParameters.itemTabs" :block-id="responseParameters.blockId" :item-id="responseParameters.itemId"></item-tabs>

            <content-view :type="contentType"></content-view>

            <footer v-if="contentType === 'form'" class="settings-component__card__footer">
                <input-button :disabled="false" :label="__('actions.save')" @click="submitForm"></input-button>
            </footer>
        </div>
    </div>

</template>

<script>
    import Alert from './Alert';
    import ContentView from './Content';
    import {eventBus} from "../adminApp";
    import Slink from './Link';
    import {InputButton} from 'lubart-vue-input-component';
    import BlockTabs from './BlockTabs';
    import ItemTabs from './ItemTabs';

    export default {
        name: "Settings",

        components: { Alert, ContentView, Slink, InputButton, BlockTabs, ItemTabs },

        props: {
            title: {type: Array},                                      // modal caption
            visible: {type: Boolean, default: false},                   // modal visibility
            alert: {type: Object, default: () => {return {};} },
            alertStatus: {type: String, default: "success"},
            alertHtml: {type: Boolean, default: false}
        },

        data() {
            return {
                content: null,
                contentType: null,
                responseParameters: [],
                caption: this.title,
                visibility: this.visible,
                isAlertVisible: false,
                alertType: this.alertStatus,
                alertNotes: this.alert,
                alertRenderHtml: this.alertHtml,
                contentIsReady: false,
                confirmation: false,
                confirmationAction: () => false,
            }
        },

        methods: {
            closeModal(){
                this.visibility = false;
            },

            submitForm(){
                eventBus.$emit('submitForm');
            },

            showErrors(errorData, status){
                this.resetAlert();
                this.alertType = "danger";

                if(errorData.errors !== undefined){
                    this.alertRenderHtml = false;
                    Object.keys(errorData.errors).forEach(field => {
                        errorData.errors[field].forEach((error, index) => {
                            this.alertNotes[field+"_"+index] = error;
                        });
                    });
                }
                else if(status === 401){
                    this.alertRenderHtml = true;
                    let htmlError = '<h2>Unauthenticated</h2>';
                    htmlError += '<h4>Please <a href="/login">log in</a> to access admin panel.</h4>';

                    this.alertNotes.exception = htmlError;
                }
                else{
                    this.alertRenderHtml = true;
                    let htmlError = '<h2>'+errorData.exception+'</h2>';
                    htmlError += '<h5>'+errorData.file+' line '+errorData.line+'</h5>';
                    htmlError += '<h4>'+errorData.message+'</h4>';

                    this.alertNotes.exception = htmlError;
                }

                this.isAlertVisible = !!Object.keys(this.alertNotes).length;
            },

            showSuccessMessage(message){
                this.resetAlert();
                this.alertType = "success";
                this.alertRenderHtml = false;

                this.alertNotes.success = message;

                this.isAlertVisible = !!Object.keys(this.alertNotes).length;
            },

            askConfirmation(question, confirmedCallback){
                this.resetAlert();
                this.alertType = "warning";
                this.alertRenderHtml = false;

                this.alertNotes.warning = question;

                this.isAlertVisible = !!Object.keys(this.alertNotes).length;
                this.confirmation = true;
                this.confirmationAction = confirmedCallback;
            },

            resetAlert(){
                this.alertNotes = {};
                this.alertType = 'success';
                this.alertRenderHtml = false;
                this.confirmation = false;
                this.confirmationAction = () => false;

                this.isAlertVisible = false;
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
            },

            content(val){
                eventBus.$emit('contentReceived', val);
            }
        }
    }
</script>

<style scoped>

    .settings-component{
        display: flex;
        align-items: center;
        flex-direction: column;
        justify-content: center;
        overflow: scroll;
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
        max-height: calc(100vh - 120px);
        z-index: 100;
        width:80%;

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

    .settings-component__card__footer{
        background-color: #f5f5f5;
        display: flex;
        flex-shrink: 0;
        justify-content: flex-end;
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
