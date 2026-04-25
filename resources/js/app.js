import './bootstrap'
import '../css/app.css'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Toast, {
                position: 'top-right',
                timeout: 3000,
                closeOnClick: true,
            })
            .mount(el)
    },
})