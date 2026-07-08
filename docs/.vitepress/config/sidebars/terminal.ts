import type { DefaultTheme } from 'vitepress';

export const terminal: DefaultTheme.SidebarItem[] = [
    {
        text: 'Terminal',
        items: [
            {text: 'Introduction', link: '/terminal/'},
            {text: 'Installation', link: '/terminal/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Commands', link: '/terminal/commands'},
            {text: 'Middleware', link: '/terminal/middleware'},
            {text: 'The Printer', link: '/terminal/printer'},
            {text: 'Errors and reporting', link: '/terminal/errors'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Terminal', link: '/terminal/api/Terminal'},
            {text: 'Printer', link: '/terminal/api/Printer'},
            {text: '#[Command]', link: '/terminal/api/Command'},
            {text: '#[Argument]', link: '/terminal/api/Argument'},
            {text: '#[Option]', link: '/terminal/api/Option'}
        ]
    }
];
