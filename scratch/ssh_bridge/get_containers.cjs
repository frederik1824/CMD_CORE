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
    const res = await executeRemote("docker ps --format '{{.ID}}\t{{.Image}}\t{{.CreatedAt}}' | grep cmdcore-cmdcore");
    console.log(res.stdout);
    conn.end();
    process.exit(0);
}).on('error', (err) => {
    console.error(err);
    process.exit(1);
}).connect({
    host: '72.62.167.179',
    port: 22,
    username: 'root',
    password: 'oK6l+W35f;)BnO1u'
});
