# PG-SKILL PROJECT SETUP GUIDE

# 1. Overview

This project is used to test:

* Plain PHP
* CakePHP 5.1
* Laravel 11.38
* Python Flask API
* ReactJS
* MySQL
* Docker

After running Docker, all source code will be automatically installed and started.

Including:

* Sample PHP
* CakePHP
* Laravel
* Python Flask
* ReactJS
* MySQL

---

# 2. Environment Requirements

Required:

* Docker Desktop

Check installation:

```bash id="n0nd2f"
docker -v
docker compose version
```

---

# 3. Project Structure

```text id="5tb9mt"
pg-skill/
├── app/
│   ├── sample/
│   ├── cakephp/
│   ├── laravel/
│   ├── python/
│   └── frontend-react/
│
├── db/
│   └── initdb.d/
│       └── pg-skill.sql
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   │
│   └── php/
│       ├── Dockerfile
│       └── entrypoint.sh
│
├── php/
│   ├── php.ini
│   └── log/
│
└── docker-compose.yml
```

---

# 4. First Time Running Project

Move to project folder:

```bash id="4e53y5"
cd "C:\Users\user\pg-skill"
```

Build project:

```bash id="8gvj1j"
docker-compose up --build
```

Or:

```bash id="ph0wc9"
docker-compose up -d --build
```

---

# 5. Running Project After First Setup

After successful first setup:

```bash id="n7bll5"
docker-compose up -d
```

No rebuild required.

---

# 6. View Realtime Logs

```bash id="h9m6c0"
docker-compose logs -f
```

---

# 7. Stop Project

```bash id="tvnrfz"
docker-compose down
```

---

# 8. Reset Database

```bash id="k7ay3k"
docker-compose down -v
```

Then:

```bash id="ewimkt"
docker-compose up --build
```

---

# 9. Check Containers

```bash id="w1j00k"
docker ps
```

Expected result:

```text id="a9r6j2"
pg-skill-db-1
pg-skill-nginx-1
pg-skill-php-1
pg-skill-react-1
```

---

# 10. Access URLs

## Sample PHP

```text id="6fgmfu"
http://sample.localhost:8080
```

---

## CakePHP

```text id="kof2fe"
http://cakephp.localhost:8080
```

---

## Laravel

```text id="kw57vw"
http://laravel.localhost:8080
```

---

## Python Flask API

```text id="v4ur7t"
http://python.localhost:8080
```

---

## ReactJS

```text id="7cqpj7"
http://react.localhost:8080
```

---

# 11. MySQL Database

Database connection info:

```text id="10d1q7"
Host: db
Port: 3306
Database: pg-skill_db
User: root
Password: pw
```

---

# 12. SQL Init File

File:

```text id="byl3n7"
db/initdb.d/pg-skill.sql
```

This file will automatically:

* create tables
* insert sample data

when MySQL starts for the first time.

---

# 13. Test DB with Plain PHP

Create file:

```text id="83q9mk"
app/sample/test.php
```

Content:

```php id="jqlf08"
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('db', 'root', 'pw', 'pg-skill_db');

if ($conn->connect_error) {

    die("CONNECT FAIL");
}

$result = $conn->query("SELECT * FROM users");

while ($row = $result->fetch_assoc()) {

    echo $row['email'] . "<br>";
}
```

Access:

```text id="5k2p94"
http://sample.localhost:8080/test.php
```

---

# 14. Test DB with CakePHP

Create file:

```text id="bksv5m"
app/cakephp/src/Controller/TestDbController.php
```

Content:

```php id="utjlwm"
<?php

namespace App\Controller;

use Cake\Datasource\ConnectionManager;

class TestDbController extends AppController
{
    public function index()
    {
        try {

            $conn = ConnectionManager::get('default');

            $rows = $conn
                ->execute('SELECT * FROM users')
                ->fetchAll('assoc');

            echo "CAKEPHP DB CONNECT OK<br><br>";

            foreach ($rows as $row) {

                echo $row['email'] . "<br>";
            }

        } catch (\Exception $e) {

            echo $e->getMessage();
        }

        exit;
    }
}
```

Access:

```text id="0aq15y"
http://cakephp.localhost:8080/test-db
```

---

# 15. Test DB with Laravel

Open file:

```text id="uxotqf"
app/laravel/routes/web.php
```

Add:

```php id="rfynow"
use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {

    $users = DB::select("SELECT * FROM users");

    echo "LARAVEL DB CONNECT OK<br><br>";

    foreach ($users as $user) {

        echo $user->email . "<br>";
    }
});
```

Access:

```text id="d0n2gx"
http://laravel.localhost:8080/test-db
```

---

# 16. Test DB with Python Flask

Create file:

```text id="pjm7um"
app/python/app.py
```

Content:

```python id="6c9gg6"
from flask import Flask, jsonify

import pymysql

app = Flask(__name__)

def get_conn():

    return pymysql.connect(
        host='db',
        user='root',
        password='pw',
        database='pg-skill_db',
        cursorclass=pymysql.cursors.DictCursor
    )

@app.route('/test-db')
def test_db():

    conn = get_conn()

    cursor = conn.cursor()

    sql = "SELECT * FROM users"

    cursor.execute(sql)

    rows = cursor.fetchall()

    conn.close()

    return jsonify(rows)

if __name__ == '__main__':

    app.run(
        host='0.0.0.0',
        port=5000,
        debug=True
    )
```

Access:

```text id="ec30mr"
http://python.localhost:8080/test-db
```

Expected result:

```json id="1tr9r0"
[
  {
    "id": 1,
    "email": "test1@example.com"
  },
  {
    "id": 2,
    "email": "test2@example.com"
  }
]
```

---

# 17. ReactJS

ReactJS will automatically:

* create app
* npm install
* npm start

Access URL:

```text id="d5g0hs"
http://react.localhost:8080
```

---

# 18. Access MySQL via Terminal

```bash id="rtngpj"
docker exec -it pg-skill-db-1 mysql -uroot -ppw
```

Or:

```bash id="hzj3q4"
docker-compose exec db mysql -uroot -ppw
```

---

# 19. SQL Test Commands

## Show databases

```sql id="rb4d75"
SHOW DATABASES;
```

---

## Select database

```sql id="79ybfi"
USE pg-skill_db;
```

---

## Show tables

```sql id="3t7i2t"
SHOW TABLES;
```

---

## View users data

```sql id="n98o6e"
SELECT * FROM users;
```

---

# 20. Final Result

After successful setup:

| Stack        | Status |
| ------------ | ------ |
| Sample PHP   | OK     |
| CakePHP      | OK     |
| Laravel      | OK     |
| Python Flask | OK     |
| ReactJS      | OK     |
| MySQL        | OK     |
| Docker       | OK     |

---
