
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');

import Vue from 'vue';
import App from '@/App.vue';
import '@/static-loader'
import {http} from '@/services/http'
import router from '@/router/router.js'
import store from '@/store/store.js'
import filters from '@/plugins/filters.js'


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 new Vue({
 	el: '#app',
 	router,
 	store,
 	components: { App },
 	template: '<App/>',
 	created() {
        http.init()
    }
})
