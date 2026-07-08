import type { DefaultTheme } from 'vitepress';

export const packages: DefaultTheme.SidebarItem[] = [
    {
        text: 'Packages',
        items: [
            {text: 'Overview', link: '/packages/'}
        ]
    },
    {
        text: 'Core',
        collapsed: false,
        items: [
            {text: 'Foundation', link: '/foundation/'},
            {text: 'Contract', link: '/contract/'},
            {text: 'Error', link: '/error/'},
            {text: 'Reflection', link: '/reflection/'},
            {text: 'Container', link: '/container/'},
            {text: 'Collection', link: '/collection/'},
            {text: 'DateTime', link: '/datetime/'},
            {text: 'Security', link: '/security/'}
        ]
    },
    {
        text: 'HTTP & Web',
        collapsed: false,
        items: [
            {text: 'HTTP', link: '/http/'},
            {text: 'Router', link: '/router/'},
            {text: 'OAuth2', link: '/oauth2/'},
            {text: 'Rate Limit', link: '/rate-limit/'},
            {text: 'OpenAPI', link: '/openapi/'}
        ]
    },
    {
        text: 'Data',
        collapsed: false,
        items: [
            {text: 'Database', link: '/database/'},
            {text: 'Search', link: '/search/'},
            {text: 'Cache', link: '/cache/'}
        ]
    },
    {
        text: 'Integrations & Output',
        collapsed: false,
        items: [
            {text: 'Mail', link: '/mail/'},
            {text: 'Message Bus', link: '/message-bus/'},
            {text: 'Barcode', link: '/barcode/'},
            {text: 'Wallet', link: '/wallet/'},
            {text: 'Terminal', link: '/terminal/'}
        ]
    }
];
