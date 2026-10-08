import fs from 'node:fs';
import { execSync } from 'node:child_process';

function prepareDist() {
    if (!fs.existsSync('dist')) {
        fs.mkdirSync('dist', { recursive: true });
    }
    // Copy compiled Vite assets into dist/build
    if (fs.existsSync('public/build')) {
        fs.cpSync('public/build', 'dist/build', { recursive: true });
    }
    // Copy public static root assets (excluding PHP scripts like index.php)
    const staticFiles = ['favicon.ico', 'favicon.svg', 'robots.txt', 'apple-touch-icon.png'];
    for (const file of staticFiles) {
        if (fs.existsSync(`public/${file}`)) {
            fs.copyFileSync(`public/${file}`, `dist/${file}`);
        }
    }
}

// In Vercel deployment environments, use the committed pre-compiled production assets
if ((process.env.VERCEL || process.env.CI) && fs.existsSync('public/build/manifest.json')) {
    console.log('Production assets verified in public/build. Staging static assets in dist...');
    prepareDist();
    console.log('Static assets staged in dist. Skipping remote compilation in Vercel environment.');
    process.exit(0);
}

console.log('Compiling production assets with Vite...');
execSync('npx vite build', { stdio: 'inherit' });
prepareDist();
