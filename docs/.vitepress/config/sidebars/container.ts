import type { DefaultTheme } from 'vitepress';

export const container: DefaultTheme.SidebarItem[] = [
    {
        text: 'Container',
        items: [
            {text: 'Introduction', link: '/container/'},
            {text: 'Installation', link: '/container/installation'}
        ]
    },
    {
        text: 'Core concepts',
        collapsed: false,
        items: [
            {text: 'Binding and resolving', link: '/container/binding-and-resolving'},
            {text: 'Autowiring and attributes', link: '/container/autowiring'},
            {text: 'Calling callables', link: '/container/calling-callables'},
            {text: 'Errors and dependency chains', link: '/container/errors'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Container', link: '/container/api/Container'},
            {text: 'PsrContainerAdapter', link: '/container/api/PsrContainerAdapter'},
            {text: 'Attributes', link: '/container/api/Attributes'},
            {text: 'DependencyChain', link: '/container/api/DependencyChain'}
        ]
    }
];
