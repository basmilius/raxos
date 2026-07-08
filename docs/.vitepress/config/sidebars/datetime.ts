import type { DefaultTheme } from 'vitepress';

export const datetime: DefaultTheme.SidebarItem[] = [
    {
        text: 'DateTime',
        items: [
            {text: 'Introduction', link: '/datetime/'},
            {text: 'Installation', link: '/datetime/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Date, Time and DateTime', link: '/datetime/value-objects'},
            {text: 'Enums and utilities', link: '/datetime/enums-and-utilities'},
            {text: 'ORM casters', link: '/datetime/orm-casters'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Date', link: '/datetime/api/Date'},
            {text: 'DateTime', link: '/datetime/api/DateTime'},
            {text: 'Time', link: '/datetime/api/Time'},
            {text: 'DateTimeUtil', link: '/datetime/api/DateTimeUtil'}
        ]
    }
];
