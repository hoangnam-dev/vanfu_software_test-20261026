#!/bin/bash

set -e

echo "=== AUTO SETUP START ==="

export COMPOSER_ALLOW_SUPERUSER=1

cd /var/www

# =========================================
# WAIT FOR MYSQL
# =========================================
echo "Waiting for MySQL..."

until php -r "
try {
    new PDO('mysql:host=db;dbname=pg-skill_db', 'root', 'pw');
    echo 'DB OK';
} catch (Exception \$e) {
    exit(1);
}
"; do
    sleep 2
done

echo ""
echo "MySQL READY"

# =========================================
# CHECK FIRST SETUP
# =========================================

if [ ! -f /var/www/.setup_done ]; then

    # =========================================
    # LARAVEL
    # =========================================
    echo ""
    echo ">>> Laravel setup..."

    if [ ! -f laravel/artisan ]; then

        echo "Installing Laravel..."

        composer create-project laravel/laravel:^11.0 laravel

    fi

    cd /var/www/laravel

    # force rewrite .env
    cat > .env <<EOF
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=pg-skill_db
DB_USERNAME=root
DB_PASSWORD=pw

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=file

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
EOF

    composer install --no-interaction

    php artisan key:generate
    php artisan config:clear
    php artisan cache:clear || true

    # fix permission
    chown -R www-data:www-data /var/www/laravel
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache

    echo "Laravel OK"

    # =========================================
    # CAKEPHP
    # =========================================
    echo ""
    echo ">>> CakePHP setup..."

    cd /var/www

    if [ ! -f cakephp/bin/cake ]; then

        echo "Installing CakePHP..."

        rm -rf cakephp

        composer create-project cakephp/app cakephp "5.1.*" --prefer-dist

    fi

    cd /var/www/cakephp

    composer install --no-interaction

    # auto config DB
    if [ -f config/app_local.php ]; then

        sed -i "s/'host' => .*/'host' => 'db',/" config/app_local.php
        sed -i "s/'username' => .*/'username' => 'root',/" config/app_local.php
        sed -i "s/'password' => .*/'password' => 'pw',/" config/app_local.php
        sed -i "s/'database' => .*/'database' => 'pg-skill_db',/" config/app_local.php

    fi

    echo "CakePHP OK"

    # =========================================
    # SAMPLE
    # =========================================
    echo ""
    echo ">>> Sample setup..."

    mkdir -p /var/www/sample

    if [ ! -f /var/www/sample/index.php ]; then

cat > /var/www/sample/index.php <<EOF
<?php

echo "<h1>Sample PHP OK</h1>";
echo "<a href='test.php'>Test DB</a>";
EOF

    fi

    echo "Sample OK"

    # =========================================
    # CREATE SETUP FLAG
    # =========================================

    touch /var/www/.setup_done

    echo ""
    echo "=== FIRST SETUP DONE ==="

else

    echo ""
    echo "Setup already completed. Skipping..."

fi

# =========================================
# PYTHON
# =========================================
echo ""
echo ">>> Python setup..."

mkdir -p /var/www/python

pip3 install flask pymysql flask-cors --break-system-packages

if [ ! -f /var/www/python/app.py ]; then

cat > /var/www/python/app.py <<EOF
from flask import Flask, jsonify
from flask_cors import CORS
import pymysql

app = Flask(__name__)

CORS(app)

@app.route('/test-db')
def test_db():

    conn = pymysql.connect(
        host='db',
        user='root',
        password='pw',
        database='pg-skill_db',
        port=3306
    )

    cursor = conn.cursor()

    cursor.execute("SELECT * FROM users")

    rows = cursor.fetchall()

    data = []

    for row in rows:
        data.append({
            "id": row[0],
            "email": row[1]
        })

    conn.close()

    return jsonify(data)

if __name__ == '__main__':
    app.run(
        host='0.0.0.0',
        port=5000,
        debug=True
    )
EOF

fi

echo "Python OK"

# =========================================
# REACTJS
# =========================================
echo ""
echo ">>> ReactJS setup..."

cd /var/www

if [ ! -f /var/www/frontend-react/package.json ]; then

    echo "Installing ReactJS..."

    rm -rf /var/www/frontend-react

    npx create-react-app frontend-react

fi

echo "ReactJS OK"

# =========================================
# DONE
# =========================================

# =========================================
# START PYTHON API
# =========================================

cd /var/www/python

python3 app.py &

# =========================================
# START PHP-FPM
# =========================================

php-fpm