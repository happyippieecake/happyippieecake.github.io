<?php
/**
 * PaymentGateway - Class untuk menangani pembayaran Bank Transfer dan QRIS
 * HappyippieCake Payment System
 */

class PaymentGateway
{
    private $conn;
    
    // Bank account information - dapat diubah sesuai kebutuhan
    private static $bankAccounts = [
        'bca' => [
            'bank_name' => 'Bank Central Asia (BCA)',
            'account_number' => '1234567890',
            'account_name' => 'HappyippieCake',
            'bank_code' => '014',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg'
        ],
        'mandiri' => [
            'bank_name' => 'Bank Mandiri',
            'account_number' => '0987654321',
            'account_name' => 'HappyippieCake',
            'bank_code' => '008',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg'
        ],
        'bri' => [
            'bank_name' => 'Bank Rakyat Indonesia (BRI)',
            'account_number' => '1122334455',
            'account_name' => 'HappyippieCake',
            'bank_code' => '002',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/6/68/BANK_BRI_logo.svg'
        ]
    ];
    
    // QRIS Information
    private static $qrisData = [
        'merchant_name' => 'HappyippieCake',
        'merchant_id' => 'ID1234567890',
        'qris_image' => 'gambar/qris_happyippiecake.png'
    ];
    
    public function __construct($conn = null)
    {
        $this->conn = $conn;
    }
    
    /**
     * Get all available bank accounts
     * @return array
     */
    public static function getBankAccounts(): array
    {
        return self::$bankAccounts;
    }
    
    /**
     * Get specific bank account by key
     * @param string $bankKey
     * @return array|null
     */
    public static function getBankAccount(string $bankKey): ?array
    {
        return self::$bankAccounts[$bankKey] ?? null;
    }
    
    /**
     * Get QRIS data
     * @return array
     */
    public static function getQrisData(): array
    {
        return self::$qrisData;
    }
    
    /**
     * Generate unique order ID
     * @return string
     */
    public static function generateOrderId(): string
    {
        $prefix = 'HPC';
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        return $prefix . '-' . $timestamp . '-' . $random;
    }
    
    /**
     * Get available payment methods
     * @return array
     */
    public static function getPaymentMethods(): array
    {
        return [
            'bank_bca' => 'Transfer Bank BCA',
            'bank_mandiri' => 'Transfer Bank Mandiri',
            'bank_bri' => 'Transfer Bank BRI',
            'qris' => 'QRIS (Scan QR Code)'
        ];
    }
    
    /**
     * Create new payment record
     * @param string $orderId
     * @param int $pesananId
     * @param float $amount
     * @param string $method
     * @return int|false Payment ID or false on failure
     */
    public function createPayment(string $orderId, int $pesananId, float $amount, string $method)
    {
        if (!$this->conn) {
            return false;
        }
        
        $validMethods = ['bank_bca', 'bank_mandiri', 'bank_bri', 'qris'];
        if (!in_array($method, $validMethods)) {
            return false;
        }
        
        $stmt = $this->conn->prepare(
            "INSERT INTO payments (order_id, pesanan_id, amount, payment_method, status, created_at) 
             VALUES (?, ?, ?, ?, 'pending', NOW())"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sids", $orderId, $pesananId, $amount, $method);
        
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        
        return false;
    }
    
    /**
     * Get payment by ID
     * @param int $paymentId
     * @return array|null
     */
    public function getPayment(int $paymentId): ?array
    {
        if (!$this->conn) {
            return null;
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE id = ?");
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $paymentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get payment by order ID
     * @param string $orderId
     * @return array|null
     */
    public function getPaymentByOrderId(string $orderId): ?array
    {
        if (!$this->conn) {
            return null;
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE order_id = ?");
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("s", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }

    /**
     * Get payment by pesanan ID
     * @param int $pesananId
     * @return array|null
     */
    public function getPaymentByPesananId(int $pesananId): ?array
    {
        if (!$this->conn) {
            return null;
        }
        
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE pesanan_id = ?");
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $pesananId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get payment status
     * @param int $paymentId
     * @return string
     */
    public function getPaymentStatus(int $paymentId): string
    {
        $payment = $this->getPayment($paymentId);
        return $payment['status'] ?? 'unknown';
    }
    
    /**
     * Confirm payment (mark as paid)
     * @param int $paymentId
     * @return bool
     */
    public function confirmPayment(int $paymentId): bool
    {
        if (!$this->conn) {
            return false;
        }
        
        $stmt = $this->conn->prepare(
            "UPDATE payments SET status = 'confirmed', confirmed_at = NOW() WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $paymentId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
    /**
     * Cancel payment
     * @param int $paymentId
     * @return bool
     */
    public function cancelPayment(int $paymentId): bool
    {
        if (!$this->conn) {
            return false;
        }
        
        $stmt = $this->conn->prepare(
            "UPDATE payments SET status = 'cancelled' WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $paymentId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
    /**
     * Upload bukti transfer
     * @param int $paymentId
     * @param string $filePath
     * @return bool
     */
    public function uploadBuktiTransfer(int $paymentId, string $filePath): bool
    {
        if (!$this->conn) {
            return false;
        }
        
        $stmt = $this->conn->prepare(
            "UPDATE payments SET bukti_transfer = ? WHERE id = ?"
        );
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("si", $filePath, $paymentId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
    /**
     * Get all pending payments
     * @return array
     */
    public function getPendingPayments(): array
    {
        if (!$this->conn) {
            return [];
        }
        
        $result = $this->conn->query(
            "SELECT p.*, ps.nama_pemesan, ps.alamat 
             FROM payments p 
             LEFT JOIN pesanan ps ON p.pesanan_id = ps.id 
             WHERE p.status = 'pending' 
             ORDER BY p.created_at DESC"
        );
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get all confirmed payments
     * @return array
     */
    public function getConfirmedPayments(): array
    {
        if (!$this->conn) {
            return [];
        }
        
        $result = $this->conn->query(
            "SELECT p.*, ps.nama_pemesan, ps.alamat 
             FROM payments p 
             LEFT JOIN pesanan ps ON p.pesanan_id = ps.id 
             WHERE p.status = 'confirmed' 
             ORDER BY p.confirmed_at DESC"
        );
        
        if (!$result) {
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Format currency to Indonesian Rupiah
     * @param float $amount
     * @return string
     */
    public static function formatRupiah(float $amount): string
    {
        return 'Rp' . number_format($amount, 0, ',', '.');
    }
    
    /**
     * Validate payment method
     * @param string $method
     * @return bool
     */
    public static function isValidPaymentMethod(string $method): bool
    {
        $validMethods = ['bank_bca', 'bank_mandiri', 'bank_bri', 'qris'];
        return in_array($method, $validMethods);
    }
}
