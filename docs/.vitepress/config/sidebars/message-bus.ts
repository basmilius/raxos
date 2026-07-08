import type { DefaultTheme } from 'vitepress';

export const messageBus: DefaultTheme.SidebarItem[] = [
    {
        text: 'Message Bus',
        items: [
            {text: 'Introduction', link: '/message-bus/'},
            {text: 'Installation', link: '/message-bus/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Messages and handlers', link: '/message-bus/messages-and-handlers'},
            {text: 'Publishing and consuming', link: '/message-bus/publishing-and-consuming'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'MessageBus', link: '/message-bus/api/MessageBus'},
            {text: 'MessageBusQueue', link: '/message-bus/api/MessageBusQueue'},
            {text: 'Handler', link: '/message-bus/api/Handler'},
            {text: 'MessagePriority', link: '/message-bus/api/MessagePriority'},
            {text: 'Exceptions', link: '/message-bus/api/Exceptions'}
        ]
    }
];
