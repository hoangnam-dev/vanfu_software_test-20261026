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
