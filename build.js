import fs from 'node:fs';
import { execSync } from 'node:child_process';

const fluxCss = 'vendor/livewire/flux/dist/flux.css';

if (fs.existsSync(fluxCss)) {
    console.log('Vendor dependencies found. Compiling production assets with Vite...');
    execSync('npx vite build', { stdio: 'inherit' });
} else {
    console.log('Vendor directory not present (Vercel deployment environment). Using pre-compiled production assets in public/build.');
}
