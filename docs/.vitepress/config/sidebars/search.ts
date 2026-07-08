import type { DefaultTheme } from 'vitepress';

export const search: DefaultTheme.SidebarItem[] = [
    {
        text: 'Search',
        items: [
            {text: 'Introduction', link: '/search/'},
            {text: 'Installation', link: '/search/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Query syntax', link: '/search/query-syntax'},
            {text: 'Filters', link: '/search/filters'},
            {text: 'Scoring', link: '/search/scoring'},
            {text: 'Policies', link: '/search/policies'},
            {text: 'Select options', link: '/search/select-options'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'SearchProvider', link: '/search/api/SearchProvider'},
            {text: 'SearchModel', link: '/search/api/SearchModel'},
            {text: 'SearchResult', link: '/search/api/SearchResult'},
            {text: 'Filter classes', link: '/search/api/Filters'},
            {text: 'ScoreExpression', link: '/search/api/ScoreExpression'},
            {text: 'Policy', link: '/search/api/Policy'},
            {text: 'Attributes', link: '/search/api/Attributes'}
        ]
    }
];
