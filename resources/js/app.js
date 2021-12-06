/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

const { default: Echo } = require("laravel-echo");

require("./bootstrap");

window.Vue = require("vue").default;

import moment from "moment";

Vue.prototype.moment = moment;
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component("chat-body", require("./components/ChatBody.vue").default);

Vue.component("search-field", require("./components/SearchField.vue").default);

Vue.component(
    "chat-user-header",
    require("./components/ChatUserHeader.vue").default
);
Vue.component(
    "pinned-message",
    require("./components/PinnedMessage.vue").default
);
Vue.component("chat-form", require("./components/ChatForm.vue").default);
Vue.component("message-actions", require("./components/MessageActions.vue").default);
Vue.component(
    "chat-messages",
    require("./components/ChatMessages.vue").default
);
Vue.component("chat-users", require("./components/ChatUsers.vue").default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app",
});
