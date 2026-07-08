import type { DefaultTheme } from 'vitepress';

export const openapi: DefaultTheme.SidebarItem[] = [
    {
        text: 'OpenAPI',
        items: [
            {text: 'Introduction', link: '/openapi/'},
            {text: 'Installation', link: '/openapi/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Generating a specification', link: '/openapi/generating-a-spec'},
            {text: 'Documenting endpoints', link: '/openapi/documenting-endpoints'},
            {text: 'Documenting schemas', link: '/openapi/documenting-schemas'},
            {text: 'Document metadata and security', link: '/openapi/spec-metadata'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'OpenAPI', link: '/openapi/api/OpenAPI'},
            {text: 'RouterBuilder', link: '/openapi/api/RouterBuilder'},
            {text: 'SchemaBuilder', link: '/openapi/api/SchemaBuilder'},
            {text: 'Schema', link: '/openapi/api/Schema'},
            {text: '#[Endpoint]', link: '/openapi/api/Endpoint'},
            {text: '#[Response]', link: '/openapi/api/Response'},
            {text: '#[Parameter]', link: '/openapi/api/Parameter'},
            {text: '#[Model] and #[Property]', link: '/openapi/api/Model'},
            {text: '#[FilterParams]', link: '/openapi/api/FilterParams'},
            {text: '#[Hidden]', link: '/openapi/api/Hidden'}
        ]
    }
];
