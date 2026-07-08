import type { DefaultTheme } from 'vitepress';

export const contract: DefaultTheme.SidebarItem[] = [
    {
        text: 'Contract',
        items: [
            {text: 'Introduction', link: '/contract/'},
            {text: 'Installation', link: '/contract/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Package organization', link: '/contract/organization'},
            {text: 'Exception contracts', link: '/contract/exceptions'},
            {text: 'Extension points', link: '/contract/extension-points'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'ExceptionInterface', link: '/contract/api/ExceptionInterface'},
            {text: 'DebuggableInterface', link: '/contract/api/DebuggableInterface'},
            {text: 'SerializableInterface', link: '/contract/api/SerializableInterface'},
            {text: 'ArrayableInterface', link: '/contract/api/ArrayableInterface'},
            {text: 'ContainerInterface', link: '/contract/api/ContainerInterface'},
            {text: 'CasterInterface', link: '/contract/api/CasterInterface'},
            {text: 'MiddlewareInterface (Router)', link: '/contract/api/MiddlewareInterface'},
            {text: 'HandlerInterface (Message Bus)', link: '/contract/api/HandlerInterface'},
            {text: 'MessageInterface (Message Bus)', link: '/contract/api/MessageInterface'},
            {text: 'PolicyInterface (Search)', link: '/contract/api/PolicyInterface'},
            {text: 'MutationListenerInterface (Database)', link: '/contract/api/MutationListenerInterface'},
            {text: 'ConstraintAttributeInterface (Http)', link: '/contract/api/ConstraintAttributeInterface'},
            {text: 'TransformerInterface (Http)', link: '/contract/api/TransformerInterface'}
        ]
    }
];
