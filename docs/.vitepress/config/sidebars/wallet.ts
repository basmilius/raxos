import type { DefaultTheme } from 'vitepress';

export const wallet: DefaultTheme.SidebarItem[] = [
    {
        text: 'Wallet',
        items: [
            {text: 'Introduction', link: '/wallet/'},
            {text: 'Installation', link: '/wallet/installation'}
        ]
    },
    {
        text: 'Pass model',
        collapsed: false,
        items: [
            {text: 'Building a pass', link: '/wallet/pass-structure'},
            {text: 'Fields, barcodes and components', link: '/wallet/fields-and-components'}
        ]
    },
    {
        text: 'Signing & delivery',
        collapsed: false,
        items: [
            {text: 'Signing and packaging', link: '/wallet/signing-and-packaging'},
            {text: 'Bundles and localization', link: '/wallet/bundles-and-localization'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Pass', link: '/wallet/api/Pass'},
            {text: 'PassFields', link: '/wallet/api/PassFields'},
            {text: 'PassFieldContent', link: '/wallet/api/PassFieldContent'},
            {text: 'Barcode', link: '/wallet/api/Barcode'},
            {text: 'Color', link: '/wallet/api/Color'},
            {text: 'Location', link: '/wallet/api/Location'},
            {text: 'Beacon', link: '/wallet/api/Beacon'},
            {text: 'NFC', link: '/wallet/api/NFC'},
            {text: 'RelevantDate', link: '/wallet/api/RelevantDate'},
            {text: 'SemanticTags', link: '/wallet/api/SemanticTags'},
            {text: 'PKPass', link: '/wallet/api/PKPass'},
            {text: 'PKPassBundle', link: '/wallet/api/PKPassBundle'},
            {text: 'Identity', link: '/wallet/api/Identity'},
            {text: 'Strings', link: '/wallet/api/Strings'}
        ]
    }
];
