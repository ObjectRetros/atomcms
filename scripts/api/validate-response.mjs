import { readFileSync } from 'node:fs';
import { validateResponse } from './contract.mjs';

const samples = JSON.parse(readFileSync(0, 'utf8'));
for (const sample of Array.isArray(samples) ? samples : [samples]) validateResponse(sample);
