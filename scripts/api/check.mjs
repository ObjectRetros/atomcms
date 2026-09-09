import { execFileSync } from 'node:child_process';
import { mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';
import { contract, compile } from './contract.mjs';

for (const schema of Object.values(contract.components.schemas)) compile(schema);
for (const item of Object.values(contract.paths)) {
    for (const operation of Object.values(item)) {
        for (const response of Object.values(operation.responses)) {
            for (const media of Object.values(response.content ?? {})) compile(media.schema);
        }
        for (const media of Object.values(operation.requestBody?.content ?? {})) compile(media.schema);
    }
}
const normalize = (uri) => '/' + uri.replace(/\{([^}:]+):[^}]+\}/g, '{$1}').replace(/\/$/, '');
const documented = new Set(Object.entries(contract.paths).flatMap(([path, methods]) => Object.keys(methods).map((method) => `${method} ${path}`)));
const cache = mkdtempSync(join(tmpdir(), 'atom-api-routes-'));
try {
    for (const mode of ['full', 'headless']) {
        const routes = JSON.parse(execFileSync('php', ['artisan', 'route:list', '--json', '--no-interaction'], {
            encoding: 'utf8', cwd: new URL('../../', import.meta.url),
            env: { ...process.env, ATOM_MODE: mode, APP_CONFIG_CACHE: join(cache, 'config.php'), APP_ROUTES_CACHE: join(cache, 'routes.php') },
        }));
        const actual = new Set(routes.flatMap((route) => route.method.split('|').filter((method) => method !== 'HEAD').map((method) => `${method.toLowerCase()} ${normalize(route.uri)}`)));
        for (const operation of actual) {
            if (operation.includes(' /api/v1/') && !documented.has(operation)) throw new Error(`${mode}: route missing from contract: ${operation}`);
        }
        for (const operation of documented) {
            if (!actual.has(operation)) throw new Error(`${mode}: contract references a missing route: ${operation}`);
        }
        const names = new Set(routes.map((route) => route.name));
        if (names.has('welcome') !== (mode === 'full')) throw new Error(`${mode}: incorrect public route registration`);
        if (!names.has('filament.housekeeping.auth.login')) throw new Error(`${mode}: housekeeping login missing`);
    }
} finally {
    rmSync(cache, { recursive: true, force: true });
}
console.log(`Validated ${documented.size} operations, schemas, and Laravel route coverage in both modes.`);
