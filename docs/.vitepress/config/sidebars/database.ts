import type { DefaultTheme } from 'vitepress';

export const database: DefaultTheme.SidebarItem[] = [
    {
        text: 'Database',
        items: [
            {text: 'Introduction', link: '/database/'},
            {text: 'Installation', link: '/database/installation'}
        ]
    },
    {
        text: 'Query builder',
        collapsed: false,
        items: [
            {text: 'Connections', link: '/database/connections'},
            {text: 'Query builder', link: '/database/query-builder'},
            {text: 'Transactions, caching and logging', link: '/database/transactions-and-logging'}
        ]
    },
    {
        text: 'ORM',
        collapsed: false,
        items: [
            {text: 'Models', link: '/database/orm/models'},
            {text: 'Querying models', link: '/database/orm/querying'},
            {text: 'Relations', link: '/database/orm/relations'},
            {text: 'Casters, embeddables and polymorphic models', link: '/database/orm/casters-and-embeddables'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Db', link: '/database/api/Db'},
            {text: 'Connection, MySql, MariaDb and SQLite', link: '/database/api/Connection'},
            {text: 'Query', link: '/database/api/Query'},
            {text: 'Statement', link: '/database/api/Statement'},
            {text: 'Model', link: '/database/api/Model'},
            {text: 'ModelArrayList', link: '/database/api/ModelArrayList'},
            {text: 'Expr', link: '/database/api/Expr'},
            {text: 'ORM attributes', link: '/database/api/Attributes'}
        ]
    }
];
