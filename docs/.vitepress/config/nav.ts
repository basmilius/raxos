import type { DefaultTheme } from 'vitepress';

export const nav: DefaultTheme.NavItem[] = [
    {
        text: 'Guide',
        link: '/guide/',
        activeMatch: '^/guide/'
    },
    {
        text: 'Packages',
        activeMatch: '^/(packages|foundation|contract|error|reflection|container|collection|datetime|security|http|router|oauth2|rate-limit|openapi|database|search|cache|mail|message-bus|barcode|wallet|terminal)/',
        items: [
            {text: 'Overview', link: '/packages/'},
            {
                text: 'Core',
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
                items: [
                    {text: 'Database', link: '/database/'},
                    {text: 'Search', link: '/search/'},
                    {text: 'Cache', link: '/cache/'}
                ]
            },
            {
                text: 'Integrations & Output',
                items: [
                    {text: 'Mail', link: '/mail/'},
                    {text: 'Message Bus', link: '/message-bus/'},
                    {text: 'Barcode', link: '/barcode/'},
                    {text: 'Wallet', link: '/wallet/'},
                    {text: 'Terminal', link: '/terminal/'}
                ]
            }
        ]
    },
    {
        text: 'Links',
        items: [
            {text: 'GitHub', link: 'https://github.com/basmilius/raxos'},
            {text: 'Packagist', link: 'https://packagist.org/packages/raxos/'}
        ]
    }
];
