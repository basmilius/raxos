import { defineConfig } from 'vitepress';
import { groupIconMdPlugin, groupIconVitePlugin } from 'vitepress-plugin-group-icons';
import { nav } from './nav';
import { sidebar } from './sidebars';

export default defineConfig({
    title: 'Raxos',
    titleTemplate: ':title — Raxos',
    description: 'Documentation for Raxos: a PHP 8.5 monorepo of 21 independent libraries for HTTP, routing, database, security and more.',
    cleanUrls: true,
    lastUpdated: true,
    sitemap: {
        hostname: 'https://raxos.dev'
    },
    head: [
        ['link', {rel: 'icon', type: 'image/svg+xml', href: '/favicon.svg'}],
        ['link', {rel: 'stylesheet', href: 'https://font.bmcdn.nl/css2?family=inter-variable|jetbrains-mono'}],
        ['meta', {property: 'og:type', content: 'website'}],
        ['meta', {property: 'og:url', content: 'https://raxos.dev/'}]
    ],
    markdown: {
        config(md) {
            md.use(groupIconMdPlugin);
        }
    },
    vite: {
        plugins: [
            groupIconVitePlugin() as any
        ],
        server: {
            allowedHosts: [
                'frontend-warp.bmnw.nl'
            ]
        }
    },
    themeConfig: {
        logo: '/logo.svg',
        nav,
        sidebar,
        search: {
            provider: 'local',
            options: {
                detailedView: true
            }
        },
        socialLinks: [
            {icon: 'github', link: 'https://github.com/basmilius/raxos'}
        ],
        editLink: {
            pattern: 'https://github.com/basmilius/raxos/edit/main/docs/:path',
            text: 'Edit this page on GitHub'
        },
        footer: {
            message: 'Released under the <a href="https://github.com/basmilius/raxos/blob/main/LICENSE">MIT License</a>.',
            copyright: 'Copyright © 2024–present <a href="https://github.com/basmilius">Bas Milius</a>'
        },
        outline: {
            level: [2, 3]
        }
    }
});
