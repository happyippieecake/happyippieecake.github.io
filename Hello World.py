import mysql.connector
from mysql.connector import Error

def create_database():
    try:
        # Koneksi ke MySQL
        connection = mysql.connector.connect(
            host='127.0.0.1',  # Sesuaikan dengan host Anda
            user='root',       # Sesuaikan dengan username Anda
            password=''         # Sesuaikan dengan password Anda
        )
        
        if connection.is_connected():
            cursor = connection.cursor()
            
            # Membuat database baru
            cursor.execute("CREATE DATABASE IF NOT EXISTS happycake_db")
            print("Database 'happycake_db' berhasil dibuat.")
            
            cursor.close()
            connection.close()

    except Error as e:
        print(f"Error: {e}")

# Panggil fungsi untuk membuat database
create_database()
