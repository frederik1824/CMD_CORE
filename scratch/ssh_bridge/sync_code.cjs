const { Client } = require('ssh2');

const conn = new Client();

function executeRemote(cmd) {
    return new Promise((resolve, reject) => {
        conn.exec(cmd, (err, stream) => {
            if (err) return reject(err);
            let stdout = '';
            let stderr = '';
            stream.on('close', (code) => {
                resolve({ code, stdout, stderr });
            }).on('data', (data) => {
                stdout += data.toString();
            }).stderr.on('data', (data) => {
                stderr += data.toString();
            });
        });
    });
}

conn.on('ready', async () => {
    console.log('Connected to VPS.');
    
    // Encontrar el ID del contenedor activo de cmdcore
    const psRes = await executeRemote("docker ps --format '{{.ID}}\t{{.Image}}' | grep cmdcore-cmdcore");
    if (psRes.code !== 0 || !psRes.stdout.trim()) {
        console.error('No cmdcore container found.');
        conn.end();
        process.exit(1);
    }
    
    const containerId = psRes.stdout.trim().split('\t')[0].trim();
    console.log(`Targeting Active Container for hot-sync: ${containerId}`);
    
    const commands = [
        `docker exec ${containerId} git config --global --add safe.directory /app`,
        `docker exec ${containerId} git fetch --all`,
        `docker exec ${containerId} git reset --hard origin/main`,
        `docker exec ${containerId} php artisan route:clear`,
        `docker exec ${containerId} php artisan config:clear`,
        `docker exec ${containerId} php artisan view:clear`,
        `docker exec ${containerId} chmod -R 777 storage database`
    ];
    
    for (const cmd of commands) {
        console.log(`\nExecuting: ${cmd}`);
        const res = await executeRemote(cmd);
        console.log(`Exit code: ${res.code}`);
        if (res.stdout) process.stdout.write(res.stdout);
        if (res.stderr) process.stderr.write(res.stderr);
    }
    
    console.log('\nProduction code hot-synced successfully!');
    conn.end();
    process.exit(0);
}).on('error', (err) => {
    console.error('SSH Error:', err);
    process.exit(1);
}).connect({
    host: '72.62.167.179',
    port: 22,
    username: 'root',
    password: 'oK6l+W35f;)BnO1u'
});
