import type { DefaultTheme } from 'vitepress';

export const http: DefaultTheme.SidebarItem[] = [
    {
        text: 'HTTP',
        items: [
            {text: 'Introduction', link: '/http/'},
            {text: 'Installation', link: '/http/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Requests and responses', link: '/http/requests-and-responses'},
            {text: 'Headers and status codes', link: '/http/headers-and-status-codes'},
            {text: 'Request validation', link: '/http/validation'},
            {text: 'HTTP client', link: '/http/http-client'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'HttpRequest', link: '/http/api/HttpRequest'},
            {text: 'HttpResponse', link: '/http/api/HttpResponse'},
            {text: 'HttpFile', link: '/http/api/HttpFile'},
            {text: 'HttpSendFile', link: '/http/api/HttpSendFile'},
            {text: 'HttpMethod', link: '/http/api/HttpMethod'},
            {text: 'HttpResponseCode', link: '/http/api/HttpResponseCode'},
            {text: 'HttpHeader', link: '/http/api/HttpHeader'},
            {text: 'UserAgent', link: '/http/api/UserAgent'},
            {text: 'HttpValidator', link: '/http/api/HttpValidator'},
            {text: 'HttpClient', link: '/http/api/HttpClient'},
            {text: 'HttpClientResponse', link: '/http/api/HttpClientResponse'}
        ]
    }
];
