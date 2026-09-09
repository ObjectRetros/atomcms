import test from 'node:test';
import assert from 'node:assert/strict';
import { validateResponse } from './contract.mjs';

const publicUser = { id: 1, username: 'Alice', motto: 'Hello', look: 'hd-180-1', online: false };
const profile = (data) => ({ method: 'GET', path: '/api/v1/users/Alice', status: 200, body: { data } });

test('public projection rejects private fields and incorrect scalar types', () => {
    assert.doesNotThrow(() => validateResponse(profile(publicUser)));
    assert.throws(() => validateResponse(profile({ ...publicUser, mail: 'alice@example.com' })), /additional properties/);
    assert.throws(() => validateResponse(profile({ ...publicUser, id: '1' })), /integer/);
    const { look, ...missingLook } = publicUser;
    assert.throws(() => validateResponse(profile(missingLook)), /look/);
});

test('literal online route wins over user binding, with query strings ignored', () => {
    assert.doesNotThrow(() => validateResponse({ method: 'GET', path: '/api/v1/users/online?page=1', status: 200, body: { data: [publicUser] } }));
});

test('undocumented success status and body on no-content responses fail', () => {
    assert.throws(() => validateResponse({ ...profile(publicUser), status: 201 }), /Undocumented success status/);
    assert.throws(() => validateResponse({ method: 'PUT', path: '/api/v1/me/account', status: 204, body: { data: true } }), /Expected empty response/);
});

test('API errors require stable code and constrain validation values', () => {
    const sample = { method: 'GET', path: '/api/v1/me', status: 422, body: { code: 'validation_failed', message: 'Invalid input', errors: { mail: ['Invalid address'] } } };
    assert.doesNotThrow(() => validateResponse(sample));
    assert.throws(() => validateResponse({ ...sample, body: { message: 'No code' } }), /code/);
    assert.throws(() => validateResponse({ ...sample, body: { ...sample.body, errors: { mail: 'Invalid address' } } }), /array/);
});

test('home widget discriminator requires matching content', () => {
    const sample = { method: 'GET', path: '/api/v1/homes/Alice/widgets/1', status: 200, body: { data: { id: 1, type: 'my-profile', supported: true, content: publicUser } } };
    assert.doesNotThrow(() => validateResponse(sample));
    assert.throws(() => validateResponse({ ...sample, body: { data: { ...sample.body.data, type: 'my-rating' } } }), /oneOf/);
});
