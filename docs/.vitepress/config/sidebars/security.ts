import type { DefaultTheme } from 'vitepress';

export const security: DefaultTheme.SidebarItem[] = [
    {
        text: 'Security',
        items: [
            {text: 'Introduction', link: '/security/'},
            {text: 'Installation', link: '/security/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Encoding, signing and tokens', link: '/security/utilities'},
            {text: 'Identifiers', link: '/security/identifiers'},
            {text: 'JSON Web Tokens', link: '/security/jwt'},
            {text: 'Two factor authentication', link: '/security/two-factor-auth'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Base64', link: '/security/api/Base64'},
            {text: 'Hmac', link: '/security/api/Hmac'},
            {text: 'TokenGenerator', link: '/security/api/TokenGenerator'},
            {text: 'TimingAttackPrevention', link: '/security/api/TimingAttackPrevention'},
            {text: 'NanoId', link: '/security/api/NanoId'},
            {text: 'Ulid', link: '/security/api/Ulid'},
            {text: 'Jwt', link: '/security/api/Jwt'},
            {text: 'JwtAlgorithm', link: '/security/api/JwtAlgorithm'},
            {text: 'TwoFactorAuth', link: '/security/api/TwoFactorAuth'},
            {text: 'TwoFactorAuthAlgorithm', link: '/security/api/TwoFactorAuthAlgorithm'}
        ]
    }
];
