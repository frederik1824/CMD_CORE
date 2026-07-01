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
    console.log('Client :: ready. Connected to VPS.');
    
    // Lista de contenedores antiguos
    const oldContainers = ['126871f2b121', 'f9fbcbe87d72', 'dd46f5884dc6', '3d164e2769ec', 'e387ea95b741', '3ec23e477a7b', 'd86bf2b6c306', '705775c3b860', '0c7b1ee7418d', 'acd46d1b1572', '2feb35fa9f91', '0954dc03a8d6', 'ce975716f459', '589351a403e2', '90de14d56ac4'];
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
                // Verificar si el contenedor tiene la firma de DemoCoreOperationalSeeder
                console.log(`Checking if container ${foundId} has the DemoCoreOperationalSeeder...`);
                const checkRes = await executeRemote(`docker exec ${foundId} ls database/seeders/DemoCoreOperationalSeeder.php`);
                
                if (checkRes.code === 0) {
                    containerId = foundId;
                    console.log(`Found NEW DEFINITIVE container with DemoCoreOperationalSeeder: ${containerId}`);
                } else {
                    console.log(`Container ${foundId} is an intermediate version without the seeder. Skipping...`);
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
         console.error('Error: Could not find definitive active container after 900 seconds.');
         conn.end();
         process.exit(1);
     }
     
     // Ejecutar la secuencia de base de datos en el contenedor definitivo final
     const commands = [
         `docker exec ${containerId} php artisan migrate --force`,
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
     
     console.log('\nAll database operations executed successfully on the definitive container with all new modules deployed!');
     conn.end();
    process.exit(0);
}).on('error', (err) => {
    console.error('SSH Connection Error:', err);
    process.exit(1);
}).connect({
    host: '72.62.167.179',
    port: 22,
    username: 'root',
    password: 'oK6l+W35f;)BnO1u'
});
