import type { DefaultTheme } from 'vitepress';

export const barcode: DefaultTheme.SidebarItem[] = [
    {
        text: 'Barcode',
        items: [
            {text: 'Introduction', link: '/barcode/'},
            {text: 'Installation', link: '/barcode/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Creating barcodes', link: '/barcode/creating-barcodes'},
            {text: 'Rendering to PNG or SVG', link: '/barcode/rendering'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Barcode', link: '/barcode/api/Barcode'},
            {text: 'QRCode', link: '/barcode/api/QRCode'},
            {text: 'PDF417', link: '/barcode/api/PDF417'},
            {text: 'PNGRenderer', link: '/barcode/api/PNGRenderer'},
            {text: 'SVGRenderer', link: '/barcode/api/SVGRenderer'}
        ]
    }
];
