import type { DefaultTheme } from 'vitepress';

export const mail: DefaultTheme.SidebarItem[] = [
    {
        text: 'Mail',
        items: [
            {text: 'Introduction', link: '/mail/'},
            {text: 'Installation', link: '/mail/installation'}
        ]
    },
    {
        text: 'Concepts',
        collapsed: false,
        items: [
            {text: 'Composing a mail', link: '/mail/composing-mail'},
            {text: 'Sending mail', link: '/mail/sending-mail'},
            {text: 'Email addresses and suggestions', link: '/mail/email-addresses'}
        ]
    },
    {
        text: 'API reference',
        collapsed: false,
        items: [
            {text: 'Mail', link: '/mail/api/Mail'},
            {text: 'Sender', link: '/mail/api/Sender'},
            {text: 'Recipient', link: '/mail/api/Recipient'},
            {text: 'Attachment', link: '/mail/api/Attachment'},
            {text: 'Email', link: '/mail/api/Email'},
            {text: 'EmailSuggester', link: '/mail/api/EmailSuggester'},
            {text: 'Mailgun', link: '/mail/api/Mailgun'},
            {text: 'Postmark', link: '/mail/api/Postmark'},
            {text: 'SMTP', link: '/mail/api/SMTP'}
        ]
    }
];
