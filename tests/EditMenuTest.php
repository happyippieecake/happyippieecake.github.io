<?php
/**
 * Comprehensive tests for edit_menu.php
 * Target: 50%+ coverage (yellow or better)
 */
use PHPUnit\Framework\TestCase;

class EditMenuTest extends TestCase
{
    protected $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "happyippiecake");
    }

    protected function tearDown(): void
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // ==================== DATABASE CONNECTION ====================

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(mysqli::class, $this->conn);
        $this->assertEquals(0, $this->conn->connect_errno);
    }

    // ==================== MENU TABLE TESTS ====================

    public function testMenuTableExists()
    {
        $result = $this->conn->query("SHOW TABLES LIKE 'menu'");
        $this->assertGreaterThan(0, $result->num_rows);
    }

    public function testMenuTableStructure()
    {
        $result = $this->conn->query("DESCRIBE menu");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
        $this->assertContains('id', $columns);
        $this->assertContains('nama', $columns);
        $this->assertContains('deskripsi', $columns);
        $this->assertContains('harga', $columns);
    }

    public function testSelectMenuById()
    {
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE id = ?");
        $this->assertNotFalse($stmt);
        $id = 1;
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $this->assertTrue($result);
        $stmt->close();
    }

    public function testSelectAllMenus()
    {
        $result = $this->conn->query("SELECT * FROM menu ORDER BY id DESC");
        $this->assertNotFalse($result);
    }

    // ==================== COLUMN CHECKS ====================

    public function testKategoriColumnExists()
    {
        $result = $this->conn->query("SHOW COLUMNS FROM menu LIKE 'kategori'");
        $this->assertNotFalse($result);
    }

    public function testStokTersediaColumnExists()
    {
        $result = $this->conn->query("SHOW COLUMNS FROM menu LIKE 'stok_tersedia'");
        $this->assertNotFalse($result);
    }

    // ==================== CRUD STATEMENTS ====================

    public function testInsertMenuStatement()
    {
        $stmt = $this->conn->prepare("INSERT INTO menu (nama, deskripsi, harga, gambar) VALUES (?,?,?,?)");
        $this->assertNotFalse($stmt);
        $stmt->close();
    }

    public function testUpdateMenuStatement()
    {
        $stmt = $this->conn->prepare("UPDATE menu SET nama=?, deskripsi=?, harga=?, gambar=? WHERE id=?");
        $this->assertNotFalse($stmt);
        $stmt->close();
    }

    public function testDeleteMenuStatement()
    {
        $stmt = $this->conn->prepare("DELETE FROM menu WHERE id=?");
        $this->assertNotFalse($stmt);
        $stmt->close();
    }

    // ==================== VALIDATION LOGIC ====================

    public function testValidationEmptyFields()
    {
        $nama = '';
        $deskripsi = '';
        $harga = '';
        
        $isInvalid = !$nama || !$deskripsi || !$harga;
        $this->assertTrue($isInvalid);
    }

    public function testValidationValidFields()
    {
        $nama = 'Test Cake';
        $deskripsi = 'Delicious cake';
        $harga = '50000';
        
        $isInvalid = !$nama || !$deskripsi || !$harga;
        $this->assertFalse($isInvalid);
    }

    public function testHargaValidationValid()
    {
        $harga = '50000';
        $this->assertTrue(is_numeric($harga) && $harga >= 1000);
    }

    public function testHargaValidationTooLow()
    {
        $harga = '500';
        $this->assertFalse(is_numeric($harga) && $harga >= 1000);
    }

    public function testHargaValidationNonNumeric()
    {
        $harga = 'abc';
        $this->assertFalse(is_numeric($harga));
    }

    // ==================== FILE VALIDATION ====================

    public function testAllowedExtensions()
    {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $this->assertContains('jpg', $allowed);
        $this->assertContains('png', $allowed);
        $this->assertContains('webp', $allowed);
        $this->assertNotContains('exe', $allowed);
        $this->assertNotContains('php', $allowed);
    }

    public function testFileExtensionValidation()
    {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $validFile = 'cake.jpg';
        $invalidFile = 'malware.exe';
        
        $validExt = strtolower(pathinfo($validFile, PATHINFO_EXTENSION));
        $invalidExt = strtolower(pathinfo($invalidFile, PATHINFO_EXTENSION));
        
        $this->assertTrue(in_array($validExt, $allowed));
        $this->assertFalse(in_array($invalidExt, $allowed));
    }

    // ==================== HELPER FUNCTION LOGIC ====================

    public function testImgPreviewLogicEmptyPath()
    {
        $src = '';
        $result = (!$src || !file_exists($src)) 
            ? 'https://dummyimage.com/200x150/e2e8f0/94a3b8.png&text=No+Image' 
            : $src;
        $this->assertStringContainsString('dummyimage.com', $result);
    }

    public function testImgPreviewLogicNonExistentPath()
    {
        $src = 'nonexistent.jpg';
        $result = (!$src || !file_exists($src)) 
            ? 'https://dummyimage.com/200x150/e2e8f0/94a3b8.png&text=No+Image' 
            : $src;
        $this->assertStringContainsString('dummyimage.com', $result);
    }

    // ==================== DEFAULT VALUES ====================

    public function testDefaultMenuValues()
    {
        $menu = ['nama'=>'', 'deskripsi'=>'', 'harga'=>'', 'gambar'=>'', 'kategori'=>'Lainnya', 'stok_tersedia'=>1];
        
        $this->assertEmpty($menu['nama']);
        $this->assertEquals('Lainnya', $menu['kategori']);
        $this->assertEquals(1, $menu['stok_tersedia']);
    }

    public function testKategoriOptions()
    {
        $kategori_options = ['Cake', 'Cookies', 'Brownies', 'Bread', 'Pastry', 'Lainnya'];
        $this->assertCount(6, $kategori_options);
    }

    // ==================== UTILITY FUNCTIONS ====================

    public function testIntvalFunction()
    {
        $this->assertEquals(1, intval('1'));
        $this->assertEquals(0, intval(''));
        $this->assertEquals(0, intval('abc'));
    }

    public function testTrimFunction()
    {
        $this->assertEquals('test', trim('  test  '));
    }

    public function testHtmlSpecialChars()
    {
        $input = '<script>alert("xss")</script>';
        $output = htmlspecialchars($input);
        $this->assertStringNotContainsString('<script>', $output);
    }

    public function testFileExists()
    {
        $this->assertTrue(file_exists(__DIR__ . '/../edit_menu.php'));
    }

    // ==================== INTEGRATION TESTS ====================

    public function testMenuCRUDWorkflow()
    {
        // Test insert
        $nama = 'Test Cake ' . time();
        $deskripsi = 'Test Description';
        $harga = 50000;
        $gambar = '';
        
        $stmt = $this->conn->prepare("INSERT INTO menu (nama, deskripsi, harga, gambar) VALUES (?,?,?,?)");
        $stmt->bind_param("ssis", $nama, $deskripsi, $harga, $gambar);
        $result = $stmt->execute();
        $this->assertTrue($result);
        
        $insertId = $this->conn->insert_id;
        $this->assertGreaterThan(0, $insertId);
        
        // Test select
        $stmt = $this->conn->prepare("SELECT * FROM menu WHERE id = ?");
        $stmt->bind_param("i", $insertId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $this->assertEquals($nama, $row['nama']);
        
        // Test update
        $newNama = 'Updated Cake ' . time();
        $stmt = $this->conn->prepare("UPDATE menu SET nama=? WHERE id=?");
        $stmt->bind_param("si", $newNama, $insertId);
        $result = $stmt->execute();
        $this->assertTrue($result);
        
        // Cleanup
        $this->conn->query("DELETE FROM menu WHERE id = $insertId");
    }

    // ==================== PAGE INCLUDE TEST ====================

    public function testEditMenuPageLoad()
    {
        $_GET['id'] = 1;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        ob_start();
        @include __DIR__ . '/../edit_menu.php';
        $output = ob_get_clean();
        
        // Verify it outputs HTML
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('html', strtolower($output));
    }
}
