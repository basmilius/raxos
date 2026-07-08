import type { DefaultTheme } from 'vitepress';
import { guide } from './guide';
import { packages } from './packages';
import { foundation } from './foundation';
import { contract } from './contract';
import { error } from './error';
import { reflection } from './reflection';
import { container } from './container';
import { collection } from './collection';
import { datetime } from './datetime';
import { security } from './security';
import { http } from './http';
import { router } from './router';
import { oauth2 } from './oauth2';
import { rateLimit } from './rate-limit';
import { openapi } from './openapi';
import { database } from './database';
import { search } from './search';
import { cache } from './cache';
import { mail } from './mail';
import { messageBus } from './message-bus';
import { barcode } from './barcode';
import { wallet } from './wallet';
import { terminal } from './terminal';

export const sidebar: DefaultTheme.SidebarMulti = {
    '/guide/': guide,
    '/packages/': packages,
    '/foundation/': foundation,
    '/contract/': contract,
    '/error/': error,
    '/reflection/': reflection,
    '/container/': container,
    '/collection/': collection,
    '/datetime/': datetime,
    '/security/': security,
    '/http/': http,
    '/router/': router,
    '/oauth2/': oauth2,
    '/rate-limit/': rateLimit,
    '/openapi/': openapi,
    '/database/': database,
    '/search/': search,
    '/cache/': cache,
    '/mail/': mail,
    '/message-bus/': messageBus,
    '/barcode/': barcode,
    '/wallet/': wallet,
    '/terminal/': terminal
};
