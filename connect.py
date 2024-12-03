import mysql.connector

# Sambungkan ke database
conn = mysql.connector.connect(
    host="localhost",    # Alamat host database
    user="root",         # Username database
    password="password", # Password database
    database="database_name" # Nama database
)

# Buat cursor untuk eksekusi query
cursor = conn.cursor()

# Eksekusi query
cursor.execute("SELECT * FROM table_name")

# Ambil hasil query
results = cursor.fetchall()
for row in results:
    print(row)

# Tutup koneksi
cursor.close()
conn.close()
