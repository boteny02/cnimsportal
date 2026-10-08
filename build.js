import fs from 'node:fs';
import { execSync } from 'node:child_process';

// In Vercel deployment environments, use the committed pre-compiled production assets
if ((process.env.VERCEL || process.env.CI) && fs.existsSync('public/build/manifest.json')) {
    console.log('Production assets verified in public/build. Skipping remote compilation in Vercel environment.');
    process.exit(0);
}

console.log('Compiling production assets with Vite...');
execSync('npx vite build', { stdio: 'inherit' });
