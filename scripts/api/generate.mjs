import { readFile, writeFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import openapiTS, { astToString } from 'openapi-typescript';

const root = new URL('../../', import.meta.url);
const schema = new URL('docs/api/openapi.json', root);
const generated = astToString(await openapiTS(schema));
const target = new URL('docs/api/schema.d.ts', root);
if (process.argv.includes('--check')) {
    const actual = await readFile(target, 'utf8');
    if (actual !== generated) {
        throw new Error(`${fileURLToPath(target)} is stale. Run npm run api:generate.`);
    }
} else {
    await writeFile(target, generated);
}
