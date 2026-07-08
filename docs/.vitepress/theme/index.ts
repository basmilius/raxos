import type { Theme } from 'vitepress';
import DefaultTheme from 'vitepress/theme';
import 'virtual:group-icons.css';
import './custom.css';
import LinkCards from './components/LinkCards.vue';
import PackageBadge from './components/PackageBadge.vue';

export default {
    extends: DefaultTheme,
    enhanceApp({app}) {
        app.component('LinkCards', LinkCards);
        app.component('PackageBadge', PackageBadge);
    }
} satisfies Theme;
