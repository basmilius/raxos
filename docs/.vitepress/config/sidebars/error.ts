import type { DefaultTheme } from 'vitepress';

export const error: DefaultTheme.SidebarItem[] = [
    {
        text: 'Error',
        items: [
            {text: 'Introduction', link: '/error/'},
            {text: 'Installation', link: '/error/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Building custom exceptions', link: '/error/custom-exceptions'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Exception', link: '/error/api/Exception'},
            {text: 'ExceptionId', link: '/error/api/ExceptionId'},
            {text: 'InvalidArgumentException', link: '/error/api/InvalidArgumentException'}
        ]
    }
];
