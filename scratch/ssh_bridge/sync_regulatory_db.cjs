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
    
    // Lista de contenedores viejos
    const oldContainers = ['5635b507c9d6', 'b55c389efd47', 'f4eb4652a79f'];
    let containerId = null;
    let attempts = 0;
    
    while (!containerId && attempts < 150) {
        attempts++;
        console.log(`Searching for the LATEST active Docker container (Attempt ${attempts}/150)...`);
        
        const res = await executeRemote("docker ps --format '{{.ID}}\t{{.Image}}\t{{.CreatedAt}}' | grep cmdcore-cmdcore");
        if (res.code === 0 && res.stdout.trim()) {
            const lines = res.stdout.trim().split('\n');
            const parts = lines[0].split('\t');
            const foundId = parts[0].trim();
            
            if (oldContainers.includes(foundId)) {
                console.log(`Container ${foundId} is in the list of old containers. Waiting for Dockploy deploy...`);
                await new Promise(r => setTimeout(r, 6000));
            } else {
                // Verificar si el contenedor tiene el archivo de migración de SISALRIL
                console.log(`Checking if container ${foundId} has the regulatory migration...`);
                const checkRes = await executeRemote(`docker exec ${foundId} ls database/migrations/2026_07_01_000001_create_regulatory_sisalril_tables.php`);
                
                if (checkRes.code === 0) {
                    containerId = foundId;
                    console.log(`Found NEW DEFINITIVE container with regulatory migration: ${containerId}`);
                } else {
                    console.log(`Container ${foundId} does not have the migration yet. Skipping...`);
                    oldContainers.push(foundId);
                    await new Promise(r => setTimeout(r, 6000));
                }
            }
        } else {
            console.log('No container found yet. Waiting 6 seconds for Dockploy deploy...');
            await new Promise(r => setTimeout(r, 6000));
        }
    }
    
    if (!containerId) {
        console.error('Error: Could not find definitive active container.');
        conn.end();
        process.exit(1);
    }
    
    const commands = [
        `docker exec ${containerId} php artisan migrate --force`,
        `docker exec ${containerId} php artisan db:seed --class=SimonCatalogsSeeder --force`,
        `docker exec ${containerId} php artisan db:seed --class=RegulatorySchemasSeeder --force`,
        `docker exec ${containerId} php artisan db:seed --class=RegulatoryDemoDataSeeder --force`,
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
    
    console.log('\nProduction Database updated with SISALRIL/SIMON structures and catalogs!');
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
