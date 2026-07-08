import type { DefaultTheme } from 'vitepress';

export const cache: DefaultTheme.SidebarItem[] = [
    {
        text: 'Cache',
        items: [
            {text: 'Introduction', link: '/cache/'},
            {text: 'Installation', link: '/cache/installation'}
        ]
    },
    {
        text: 'Core concepts',
        collapsed: false,
        items: [
            {text: 'Basic usage', link: '/cache/basic-usage'},
            {text: 'Command groups', link: '/cache/command-groups'},
            {text: 'Tagged caching', link: '/cache/tagged-cache'},
            {text: 'Error handling', link: '/cache/error-handling'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'RedisCache', link: '/cache/api/RedisCache'},
            {text: 'RedisTaggedCache', link: '/cache/api/RedisTaggedCache'},
            {text: 'RedisUtil', link: '/cache/api/RedisUtil'}
        ]
    }
];
