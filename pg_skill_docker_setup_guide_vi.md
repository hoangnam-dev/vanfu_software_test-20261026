# HƯỚNG DẪN CHẠY PROJECT PG-SKILL

# 1. Tổng quan

Project này dùng để test:

* PHP thuần
* CakePHP 5.1
* Laravel 11.38
* Python Flask API
* ReactJS
* MySQL
* Docker

Sau khi chạy Docker, toàn bộ source code sẽ tự động được cài đặt và start.

Bao gồm:

* Sample PHP
* CakePHP
* Laravel
* Python Flask
* ReactJS
* MySQL

---

# 2. Yêu cầu môi trường

Cần cài:

* Docker Desktop

Kiểm tra:

```bash
docker -v
docker compose version
```

---

# 3. Cấu trúc project

```text
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

# 4. Chạy project lần đầu

Di chuyển vào thư mục project:

```bash
cd "C:\Users\user\pg-skill"
```

Build project:

```bash
docker-compose up --build
```

Hoặc:

```bash
docker-compose up -d --build
```

---

# 5. Chạy project từ lần thứ 2

Sau khi setup thành công lần đầu:

```bash
docker-compose up -d
```

Không cần build lại.

---

# 6. Xem log realtime

```bash
docker-compose logs -f
```

---

# 7. Stop project

```bash
docker-compose down
```

---

# 8. Reset database

```bash
docker-compose down -v
```

Sau đó:

```bash
docker-compose up --build
```

---

# 9. Kiểm tra container

```bash
docker ps
```

Kết quả đúng:

```text
pg-skill-db-1
pg-skill-nginx-1
pg-skill-php-1
pg-skill-react-1
```

---

# 10. Truy cập project

## Sample PHP

```text
http://sample.localhost:8080
```

---

## CakePHP

```text
http://cakephp.localhost:8080
```

---

## Laravel

```text
http://laravel.localhost:8080
```

---

## Python Flask API

```text
http://python.localhost:8080
```

---

## ReactJS

```text
http://react.localhost:8080
```

---

# 11. Database MySQL

Thông tin kết nối:

```text
Host: db
Port: 3306
Database: pg-skill_db
User: root
Password: pw
```

---

# 12. SQL init

File:

```text
db/initdb.d/pg-skill.sql
```

File này sẽ tự động:

* tạo table
* insert data mẫu

khi MySQL start lần đầu.

---

# 13. Test DB với PHP thuần

Tạo file:

```text
app/sample/test.php
```

Nội dung:

```php
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

Truy cập:

```text
http://sample.localhost:8080/test.php
```

---

# 14. Test DB với CakePHP

Tạo file:

```text
app/cakephp/src/Controller/TestDbController.php
```

Nội dung:

```php
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

Truy cập:

```text
http://cakephp.localhost:8080/test-db
```

---

# 15. Test DB với Laravel

Mở file:

```text
app/laravel/routes/web.php
```

Thêm:

```php
use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {

    $users = DB::select("SELECT * FROM users");

    echo "LARAVEL DB CONNECT OK<br><br>";

    foreach ($users as $user) {

        echo $user->email . "<br>";
    }
});
```

Truy cập:

```text
http://laravel.localhost:8080/test-db
```

---

# 16. Test DB với Python Flask

Tạo file:

```text
app/python/app.py
```

Nội dung:

```python
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

Truy cập:

```text
http://python.localhost:8080/test-db
```

Kết quả đúng:

```json
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

ReactJS sẽ tự động:

* create app
* npm install
* npm start

Link truy cập:

```text
http://react.localhost:8080
```

---

# 18. Vào MySQL bằng terminal

```bash
docker exec -it pg-skill-db-1 mysql -uroot -ppw
```

Hoặc:

```bash
docker-compose exec db mysql -uroot -ppw
```

---

# 19. Các lệnh SQL test

## Hiển thị database

```sql
SHOW DATABASES;
```

---

## Chọn database

```sql
USE pg-skill_db;
```

---

## Hiển thị table

```sql
SHOW TABLES;
```

---

## Xem dữ liệu users

```sql
SELECT * FROM users;
```

---

# 20. Kết quả cuối cùng

Sau khi setup đúng:

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
