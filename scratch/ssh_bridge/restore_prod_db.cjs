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
    console.log(`Targeting Active Container: ${containerId}`);
    
    const commands = [
        `docker exec ${containerId} touch /app/database/database.sqlite`,
        `docker exec ${containerId} chmod 777 /app/database/database.sqlite`,
        `docker exec ${containerId} php artisan migrate:fresh --force`,
        `docker exec ${containerId} php artisan db:seed --force`,
        `docker exec ${containerId} php artisan db:seed --class=CargaDemoCompletaSeeder --force`,
        `docker exec ${containerId} php artisan db:seed --class=DemoCoreOperationalSeeder --force`,
        `docker exec ${containerId} chmod -R 777 storage database`
    ];
    
    for (const cmd of commands) {
        console.log(`\nExecuting: ${cmd}`);
        const res = await executeRemote(cmd);
        console.log(`Exit code: ${res.code}`);
        if (res.stdout) process.stdout.write(res.stdout);
        if (res.stderr) process.stderr.write(res.stderr);
        
        if (res.code !== 0) {
            console.error(`Command failed. Aborting.`);
            conn.end();
            process.exit(res.code);
        }
    }
    
    console.log('\nProduction Database restored successfully on active container!');
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
