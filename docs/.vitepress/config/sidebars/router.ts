import type { DefaultTheme } from 'vitepress';

export const router: DefaultTheme.SidebarItem[] = [
    {
        text: 'Router',
        items: [
            {text: 'Introduction', link: '/router/'},
            {text: 'Installation', link: '/router/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Routing basics', link: '/router/routing-basics'},
            {text: 'Parameter mapping', link: '/router/parameter-mapping'},
            {text: 'Middleware and validation', link: '/router/middleware'},
            {text: 'Building responses', link: '/router/responses'},
            {text: 'Dynamic routing', link: '/router/dynamic-routing'},
            {text: 'Error handling', link: '/router/error-handling'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Router', link: '/router/api/Router'},
            {text: 'DynamicRouter', link: '/router/api/DynamicRouter'},
            {text: 'Runner', link: '/router/api/Runner'},
            {text: 'Attributes', link: '/router/api/Attributes'},
            {text: 'Responds', link: '/router/api/Responds'}
        ]
    }
];
