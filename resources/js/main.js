import { createApp } from 'vue'

import AdminApp from "./AdminApp.vue";

function mountApp(component, selector) {
    const el = document.querySelector(selector)
    if (el) {
        const app = createApp(component)
        app.mount(selector)
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('my-vue-admin-app')) {
        mountApp(AdminApp, '#my-vue-admin-app')
    } else {
        mountApp(AdminApp, '#my-vue-admin-app')
    }
})

