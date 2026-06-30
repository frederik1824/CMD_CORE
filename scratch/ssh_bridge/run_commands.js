const { Client } = require('ssh2');

const conn = new Client();

const commands = [
    'docker exec 0954dc03a8d6 touch database/database.sqlite',
    'docker exec 0954dc03a8d6 chmod 777 database/database.sqlite',
    'docker exec 0954dc03a8d6 php artisan migrate:fresh --force',
    'docker exec 0954dc03a8d6 php artisan db:seed --force',
    'docker exec 0954dc03a8d6 php artisan db:seed --class=CargaDemoCompletaSeeder --force',
    'docker exec 0954dc03a8d6 chmod -R 777 storage database'
];

conn.on('ready', () => {
    console.log('Client :: ready. Connected to VPS.');
    
    let currentIdx = 0;
    
    function runNextCommand() {
        if (currentIdx >= commands.length) {
            console.log('\nAll commands executed successfully!');
            conn.end();
            process.exit(0);
        }
        
        const cmd = commands[currentIdx];
        console.log(`\nExecuting: ${cmd}`);
        
        conn.exec(cmd, (err, stream) => {
            if (err) {
                console.error(`Error initiating command "${cmd}":`, err);
                conn.end();
                process.exit(1);
            }
            
            stream.on('close', (code, signal) => {
                console.log(`Command finished with exit code ${code}`);
                if (code !== 0) {
                    console.error(`Command failed with code ${code}. Aborting.`);
                    conn.end();
                    process.exit(code);
                }
                currentIdx++;
                runNextCommand();
            }).on('data', (data) => {
                process.stdout.write(data);
            }).stderr.on('data', (data) => {
                process.stderr.write(data);
            });
        });
    }
    
    runNextCommand();
}).on('error', (err) => {
    console.error('SSH Connection Error:', err);
    process.exit(1);
}).connect({
    host: '72.62.167.179',
    port: 22,
    username: 'root',
    password: 'oK6l+W35f;)BnO1u'
});
