import type { DefaultTheme } from 'vitepress';

export const collection: DefaultTheme.SidebarItem[] = [
    {
        text: 'Collection',
        items: [
            {text: 'Introduction', link: '/collection/'},
            {text: 'Installation', link: '/collection/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Array lists', link: '/collection/array-lists'},
            {text: 'Typed lists', link: '/collection/typed-lists'},
            {text: 'Maps', link: '/collection/maps'},
            {text: 'Pagination', link: '/collection/pagination'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'ArrayList', link: '/collection/api/ArrayList'},
            {text: 'ReadonlyArrayList', link: '/collection/api/ReadonlyArrayList'},
            {text: 'StringArrayList', link: '/collection/api/StringArrayList'},
            {text: 'IntArrayList', link: '/collection/api/IntArrayList'},
            {text: 'NumberArrayList', link: '/collection/api/NumberArrayList'},
            {text: 'Map', link: '/collection/api/Map'},
            {text: 'CacheMap', link: '/collection/api/CacheMap'},
            {text: 'ReadonlyMap', link: '/collection/api/ReadonlyMap'},
            {text: 'Paginated', link: '/collection/api/Paginated'}
        ]
    }
];
