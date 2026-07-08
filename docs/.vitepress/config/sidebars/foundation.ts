import type { DefaultTheme } from 'vitepress';

export const foundation: DefaultTheme.SidebarItem[] = [
    {
        text: 'Foundation',
        items: [
            {text: 'Introduction', link: '/foundation/'},
            {text: 'Installation', link: '/foundation/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Access traits', link: '/foundation/access-traits'},
            {text: 'Option type', link: '/foundation/option'},
            {text: 'Network: IP', link: '/foundation/network'},
            {text: 'Util classes', link: '/foundation/utilities'},
            {text: 'Singleton, Stopwatch and global functions', link: '/foundation/singleton-and-stopwatch'},
            {text: 'String parsable contract', link: '/foundation/string-parsable'},
            {text: 'Preloader', link: '/foundation/preloader'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Option / Some / None', link: '/foundation/api/Option'},
            {text: 'IP', link: '/foundation/api/IP'},
            {text: 'Singleton', link: '/foundation/api/Singleton'},
            {text: 'Stopwatch', link: '/foundation/api/Stopwatch'},
            {text: 'ArrayUtil', link: '/foundation/api/ArrayUtil'},
            {text: 'StringUtil', link: '/foundation/api/StringUtil'},
            {text: 'ColorUtil', link: '/foundation/api/ColorUtil'},
            {text: 'Preloader', link: '/foundation/api/Preloader'}
        ]
    }
];
