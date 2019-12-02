
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

new Vue({
    el: '#app',

    data: {
        visible: false,
        settings: null
    },

    methods:{
        navigate(url){
            this.settings.visibility = true;
            this.settings.contentIsReady = false;

            axios.get(url).then((response) => {
                this.settings.caption = response.data.caption;
                this.settings.contentType = response.data.contentType;
                this.settings.content = JSON.parse(response.data.content);
                this.settings.contentIsReady = true;
            })
                .catch((response) => {
                    console.log("Cannot receive data.");
                    console.log(response.data);
                    this.showErrors(response.data);
                });
        },

        showErrors(data){

        }
    },

    mounted(){
        this.settings = this.$refs.settings;
    }
});