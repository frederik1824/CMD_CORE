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
    
    // 1. Ver contenedores corriendo
    const psRes = await executeRemote("docker ps --format '{{.ID}}\t{{.Image}}\t{{.Status}}\t{{.Names}}'");
    console.log('Docker PS Result:');
    console.log(psRes.stdout);
    
    // Buscar contenedores de cmdcore
    const lines = psRes.stdout.trim().split('\n');
    for (const line of lines) {
        if (line.includes('cmdcore')) {
            const parts = line.split('\t');
            const cId = parts[0].trim();
            console.log(`\nChecking container: ${cId}`);
            
            // Ver si existe el archivo de base de datos
            const lsRes = await executeRemote(`docker exec ${cId} ls -la /app/database/`);
            console.log(`ls -la /app/database/:`);
            console.log(lsRes.stdout || lsRes.stderr);
            
            // Ver la variable de entorno DB_DATABASE en el contenedor
            const envRes = await executeRemote(`docker exec ${cId} php artisan env`);
            console.log(`artisan env:`, envRes.stdout.trim());

            const configRes = await executeRemote(`docker exec ${cId} php artisan config:show database`);
            console.log(`database config:`);
            console.log(configRes.stdout || configRes.stderr);
        }
    }
    
    conn.end();
}).on('error', (err) => {
    console.error('SSH Error:', err);
}).connect({
    host: '72.62.167.179',
    port: 22,
    username: 'root',
    password: 'oK6l+W35f;)BnO1u'
});
