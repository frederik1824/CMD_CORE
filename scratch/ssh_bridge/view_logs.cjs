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
    
    // Buscar la palabra 'consultar-cedula' en todos los logs del contenedor para ver su código de respuesta HTTP
    const cmd = 'docker logs 2feb35fa9f91 2>&1 | grep consultar-cedula';
    console.log(`Executing: ${cmd}`);
    
    const res = await executeRemote(cmd);
    console.log(`Exit code: ${res.code}`);
    if (res.stdout) console.log('STDOUT:\n', res.stdout);
    if (res.stderr) console.error('STDERR:\n', res.stderr);
    
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
