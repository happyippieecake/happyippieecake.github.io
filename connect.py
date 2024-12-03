import mysql.connector

db = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="yourpassword",
    database="yourdatabase"
)

cursor = db.cursor()
cursor.execute("CREATE DATABASE dbhappycake")
print("database berhasil dibuat")
 