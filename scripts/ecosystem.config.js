const path = require('path');

module.exports = {
  apps: [
    {
      name: 'thechir-artisan-serve',
      script: 'artisan',
      interpreter: 'php',
      args: 'serve --host=127.0.0.1 --port=8000',
      cwd: path.resolve(__dirname, '..'),
      watch: false,
      autorestart: true,
      max_memory_restart: '256M',
    },
    {
      name: 'thechir-queue-worker',
      script: 'artisan',
      interpreter: 'php',
      args: 'queue:work --tries=1 --sleep=3 --memory=256 --timeout=60',
      cwd: path.resolve(__dirname, '..'),
      watch: false,
      autorestart: true,
      max_memory_restart: '512M',
    }
  ]
};
