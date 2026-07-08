import type { DefaultTheme } from 'vitepress';

export const rateLimit: DefaultTheme.SidebarItem[] = [
    {
        text: 'Rate Limit',
        items: [
            {text: 'Introduction', link: '/rate-limit/'},
            {text: 'Installation', link: '/rate-limit/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Rate limiting core', link: '/rate-limit/rate-limiting'},
            {text: 'Router middleware', link: '/rate-limit/router-middleware'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Rate', link: '/rate-limit/api/Rate'},
            {text: 'RateLimiter', link: '/rate-limit/api/RateLimiter'},
            {text: 'RateLimitStatus', link: '/rate-limit/api/RateLimitStatus'},
            {text: 'RateLimited', link: '/rate-limit/api/RateLimited'},
            {text: 'RedisRateLimiterStore', link: '/rate-limit/api/RedisRateLimiterStore'}
        ]
    }
];
