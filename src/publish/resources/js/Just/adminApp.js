
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

//require('./bootstrap');

import Vue from 'vue';
import Link from './components/Link';
import Settings from './components/Settings';

Vue.config.devtools = true;

Vue.component('slink', Link);
Vue.component('settings', Settings);

export const eventBus = new Vue();

Vue.mixin(require('./settingsTranslations'));

window.App = new Vue({
    el: '#app',

    data: {
        visible: false,
        settings: null,
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },

    methods:{
        async navigate(url){
            this.settings.visibility = true;
            this.settings.contentIsReady = false;

            await axios.get(url).then((response) => {
                this.settings.caption = response.data.caption;
                this.settings.contentType = response.data.contentType;
                this.settings.content = JSON.parse(response.data.content);
                this.settings.contentIsReady = true;

                this.settings.alertNotes = {};
                this.settings.isAlertVisible = false;
                this.settings.alertType = 'success';
            })
                .catch((response) => {
                    console.log("Cannot receive data.");
                    console.log(response.data);
                    this.showErrors(response.data);
                });


        }
    },

    mounted(){
        this.settings = this.$refs.settings;
    }
});