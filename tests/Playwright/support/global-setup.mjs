import { access, copyFile, mkdir, rm } from 'node:fs/promises';
import { constants as fsConstants } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';
import { setTimeout as delay } from 'node:timers/promises';
import { spawnSync } from 'node:child_process';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../..');
const hotFile = path.join(root, 'public', 'hot');
const backupDirectory = path.join(root, 'storage', 'framework', 'testing');
const hotBackupFile = path.join(backupDirectory, 'playwright.hot');

function run(command, args) {
    const result = spawnSync(command, args, {
        cwd: root,
        stdio: 'inherit',
    });

    if (result.status !== 0) {
        throw new Error(`${command} ${args.join(' ')} failed with exit code ${result.status ?? 'unknown'}.`);
    }
}

async function fileExists(filePath) {
    try {
        await access(filePath, fsConstants.F_OK);
        return true;
    } catch {
        return false;
    }
}

async function waitForApp() {
    for (let attempt = 1; attempt <= 30; attempt += 1) {
        try {
            const response = await fetch('http://127.0.0.1:8000/login');

            if (response.ok) {
                return;
            }
        } catch {
            // Retry until nginx is ready.
        }

        await delay(1000);
    }

    throw new Error('The nginx runtime did not become ready at http://127.0.0.1:8000/login.');
}

export default async function globalSetup() {
    await mkdir(backupDirectory, { recursive: true });

    if (await fileExists(hotFile)) {
        await copyFile(hotFile, hotBackupFile);
        await rm(hotFile);
    } else if (await fileExists(hotBackupFile)) {
        await rm(hotBackupFile);
    }

    run('npx', ['playwright', 'install', 'chromium']);

    const isCI = !!process.env.CI;
    if (!isCI) {
        run('docker', ['compose', 'up', '-d', 'nginx']);
        run('docker', ['compose', 'run', '--rm', 'app', 'php', 'artisan', 'migrate', '--force']);
        run('docker', ['compose', 'run', '--rm', 'app', 'php', 'artisan', 'db:seed', '--force']);
        run('docker', ['compose', 'run', '--rm', 'app', 'npm', 'run', 'build']);
    }

    await waitForApp();
}
