import type { DefaultTheme } from 'vitepress';

export const oauth2: DefaultTheme.SidebarItem[] = [
    {
        text: 'OAuth2',
        items: [
            {text: 'Introduction', link: '/oauth2/'},
            {text: 'Installation', link: '/oauth2/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Server setup', link: '/oauth2/server'},
            {text: 'Authorization flow', link: '/oauth2/authorization-flow'},
            {text: 'Protecting routes', link: '/oauth2/middleware'},
            {text: 'Error handling', link: '/oauth2/errors'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'OAuth2Server', link: '/oauth2/api/OAuth2Server'},
            {text: 'OAuth2Controller', link: '/oauth2/api/OAuth2Controller'},
            {text: 'OAuth2Middleware', link: '/oauth2/api/OAuth2Middleware'},
            {text: 'ClientInterface', link: '/oauth2/api/ClientInterface'},
            {text: 'ScopeInterface', link: '/oauth2/api/ScopeInterface'},
            {text: 'TokenFactoryInterface', link: '/oauth2/api/TokenFactoryInterface'},
            {text: 'GrantTypeInterface', link: '/oauth2/api/GrantTypeInterface'},
            {text: 'ResponseTypeInterface', link: '/oauth2/api/ResponseTypeInterface'},
            {text: 'OAuth2ServerException', link: '/oauth2/api/OAuth2ServerException'}
        ]
    }
];
