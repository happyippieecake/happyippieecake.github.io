import mysql.connector

try:
    conn = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="password"
    )
    cursor = conn.cursor()
    database_name = "db_menu"
    cursor.execute(f"CREATE DATABASE IF NOT EXISTS {database_name}")
    print(f"Database '{database_name}' berhasil dibuat!")
except mysql.connector.Error as err:
    print(f"Error: {err}")
except Exception as ex:
    print(f"Unexpected error: {ex}")
finally:
    if 'conn' in locals() and conn.is_connected():
        conn.close()
