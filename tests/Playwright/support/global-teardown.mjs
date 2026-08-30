import { access, copyFile, readFile, rm } from 'node:fs/promises';
import { constants as fsConstants } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../..');
const hotFile = path.join(root, 'public', 'hot');
const hotBackupFile = path.join(root, 'storage', 'framework', 'testing', 'playwright.hot');
const runtimeServerPidFile = path.join(root, 'storage', 'framework', 'testing', 'playwright-runtime-server.pid');

async function fileExists(filePath) {
    try {
        await access(filePath, fsConstants.F_OK);
        return true;
    } catch {
        return false;
    }
}

export default async function globalTeardown() {
    if (await fileExists(runtimeServerPidFile)) {
        const serverPid = Number.parseInt(await readFile(runtimeServerPidFile, 'utf8'), 10);

        if (Number.isInteger(serverPid)) {
            try {
                process.kill(serverPid);
            } catch (error) {
                if (error.code !== 'ESRCH') {
                    throw error;
                }
            }
        }

        await rm(runtimeServerPidFile, { force: true });
    }

    if (!await fileExists(hotBackupFile)) {
        return;
    }

    await copyFile(hotBackupFile, hotFile);
    await rm(hotBackupFile);
}
