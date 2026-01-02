-- Migration: Create payments table for Payment Gateway
-- Database: happyippiecake
-- Run this SQL in your MySQL/phpMyAdmin to create the payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) NOT NULL,
    pesanan_id INT NOT NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_method ENUM('bank_bca', 'bank_mandiri', 'bank_bri', 'qris') NOT NULL,
    status ENUM('pending', 'confirmed', 'expired', 'cancelled') DEFAULT 'pending',
    bukti_transfer VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;