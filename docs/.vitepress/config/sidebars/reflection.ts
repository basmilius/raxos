import type { DefaultTheme } from 'vitepress';

export const reflection: DefaultTheme.SidebarItem[] = [
    {
        text: 'Reflection',
        items: [
            {text: 'Introduction', link: '/reflection/'},
            {text: 'Installation', link: '/reflection/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Reflectors', link: '/reflection/reflectors'},
            {text: 'Reading attributes', link: '/reflection/attributes'},
            {text: 'Working with types', link: '/reflection/types'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'ClassReflector', link: '/reflection/api/ClassReflector'},
            {text: 'MethodReflector', link: '/reflection/api/MethodReflector'},
            {text: 'PropertyReflector', link: '/reflection/api/PropertyReflector'},
            {text: 'ParameterReflector', link: '/reflection/api/ParameterReflector'},
            {text: 'FunctionReflector', link: '/reflection/api/FunctionReflector'},
            {text: 'TypeReflector', link: '/reflection/api/TypeReflector'},
            {text: 'Attributable', link: '/reflection/api/Attributable'}
        ]
    }
];
