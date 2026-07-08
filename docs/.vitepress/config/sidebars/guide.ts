import type { DefaultTheme } from 'vitepress';

export const guide: DefaultTheme.SidebarItem[] = [
    {
        text: 'Guide',
        items: [
            {text: 'Introduction', link: '/guide/'},
            {text: 'Getting started', link: '/guide/getting-started'},
            {text: 'Conventions', link: '/guide/conventions'},
            {text: 'Monorepo', link: '/guide/monorepo'}
        ]
    }
];
