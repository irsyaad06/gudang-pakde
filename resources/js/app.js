import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Gudang Pakde';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    defaults: {
        visitOptions: (href, options) => {
            if (options && typeof options.viewTransition === 'boolean') {
                return { viewTransition: options.viewTransition };
            }
            if (typeof window === 'undefined') {
                return {};
            }
            let targetPath = href;
            try {
                targetPath = new URL(href, window.location.origin).pathname;
            } catch (e) {
                targetPath = href.split('?')[0].split('#')[0];
            }
            return {
                viewTransition: targetPath !== window.location.pathname,
            };
        },
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#059669',
    },
});
