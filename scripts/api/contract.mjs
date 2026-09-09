import { readFileSync } from 'node:fs';
import Ajv2020 from 'ajv/dist/2020.js';
import addFormats from 'ajv-formats';

export const contract = JSON.parse(readFileSync(new URL('../../docs/api/openapi.json', import.meta.url), 'utf8'));
const ajv = new Ajv2020({ allErrors: true, strict: true });
addFormats(ajv);
ajv.addFormat('binary', true);

export function compile(schema) {
    return ajv.compile({ ...schema, components: contract.components });
}
// Components are OpenAPI's ref container, not a JSON Schema validation keyword.
ajv.addKeyword({ keyword: 'components', schemaType: 'object' });

export function operationFor(method, path) {
    const pathname = path.split('?')[0].replace(/\/$/, '') || '/';
    // Literal routes (users/online) take precedence over parameter routes.
    const paths = Object.keys(contract.paths).sort((a, b) => a.includes('{') - b.includes('{'));
    const template = paths.find((candidate) => {
        const expression = candidate.split('/').map((segment) => segment.startsWith('{') ? '[^/]+' : segment.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('/');
        return new RegExp(`^${expression}$`).test(pathname);
    });
    const operation = contract.paths[template]?.[method.toLowerCase()];
    if (!operation) throw new Error(`Undocumented operation: ${method} ${pathname}`);
    return operation;
}

export function validateResponse({ method, path, status, body }) {
    const operation = operationFor(method, path);
    const response = operation.responses[String(status)] ?? (status >= 400 ? operation.responses.default : undefined);
    if (!response) throw new Error(`Undocumented success status ${status}: ${method} ${path}`);
    const schema = response.content?.['application/json']?.schema;
    if (!schema) {
        if (body !== null && body !== undefined && body !== '') throw new Error(`Expected empty response: ${method} ${path} (${status})`);
        return;
    }
    const validate = compile(schema);
    if (!validate(body)) throw new Error(`${method} ${path} (${status}): ${ajv.errorsText(validate.errors, { separator: '\n' })}`);
}
