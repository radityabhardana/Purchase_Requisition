-- ==============================================================================
-- SKEMA BASIS DATA SIP-NKP (Sistem Informasi Purchasing & Inventaris PT NKP)
-- Engine: MySQL 8.0+ / MariaDB 10.4+
-- Collation: utf8mb4_unicode_ci
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `nkp_inventaris` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nkp_inventaris`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `delivery_note_items`;
DROP TABLE IF EXISTS `delivery_notes`;
DROP TABLE IF EXISTS `stock_mutations`;
DROP TABLE IF EXISTS `gr_items`;
DROP TABLE IF EXISTS `goods_receipts`;
DROP TABLE IF EXISTS `po_items`;
DROP TABLE IF EXISTS `purchase_orders`;
DROP TABLE IF EXISTS `pr_items`;
DROP TABLE IF EXISTS `purchase_requisitions`;
DROP TABLE IF EXISTS `items`;
DROP TABLE IF EXISTS `suppliers`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. TABEL PENGGUNA & HAK AKSES
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'supervisor', 'purchasing', 'warehouse', 'requester') NOT NULL,
    `department` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL MASTER SUPPLIER / VENDOR REKANAN
CREATE TABLE `suppliers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `supplier_code` VARCHAR(20) NOT NULL UNIQUE,
    `company_name` VARCHAR(150) NOT NULL,
    `contact_person` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `address` TEXT NOT NULL,
    `payment_term` VARCHAR(50) NOT NULL DEFAULT 'Net 30',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL MASTER BARANG & SUKU CADANG (INVENTARIS)
CREATE TABLE `items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `item_code` VARCHAR(30) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `category` ENUM('Mechanical', 'Electrical', 'Raw Material', 'Consumables', 'Safety/APD') NOT NULL,
    `unit` VARCHAR(20) NOT NULL DEFAULT 'Pcs',
    `stock` INT NOT NULL DEFAULT 0,
    `min_stock` INT NOT NULL DEFAULT 5,
    `location_rack` VARCHAR(50) NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL PURCHASE REQUISITION (PR / PERMINTAAN PEMBELIAN)
CREATE TABLE `purchase_requisitions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pr_number` VARCHAR(40) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `approved_by` INT NULL,
    `pr_date` DATE NOT NULL,
    `target_date` DATE NOT NULL,
    `priority` ENUM('Normal', 'Urgent', 'Emergency') NOT NULL DEFAULT 'Normal',
    `status` ENUM('Pending', 'Approved', 'Rejected', 'PO Issued') NOT NULL DEFAULT 'Pending',
    `rejection_notes` TEXT NULL,
    `general_notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_pr_approver` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABEL RINCIAN ITEM PR
CREATE TABLE `pr_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pr_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `qty_requested` INT NOT NULL,
    `remarks` VARCHAR(255) NULL,
    CONSTRAINT `fk_pritem_pr` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pritem_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABEL PURCHASE ORDER (PO / SURAT PESANAN RESMI)
CREATE TABLE `purchase_orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `po_number` VARCHAR(40) NOT NULL UNIQUE,
    `pr_id` INT NOT NULL,
    `supplier_id` INT NOT NULL,
    `created_by` INT NOT NULL,
    `po_date` DATE NOT NULL,
    `delivery_deadline` DATE NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tax_percent` DECIMAL(5,2) NOT NULL DEFAULT 11.00,
    `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('Issued', 'Partial Received', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Issued',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_po_pr` FOREIGN KEY (`pr_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_po_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TABEL RINCIAN ITEM PO
CREATE TABLE `po_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `po_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `qty_ordered` INT NOT NULL,
    `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT `fk_poitem_po` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_poitem_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. TABEL GOODS RECEIPT (GR / BUKTI PENERIMAAN BARANG DI GUDANG)
CREATE TABLE `goods_receipts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gr_number` VARCHAR(40) NOT NULL UNIQUE,
    `po_id` INT NOT NULL,
    `received_by` INT NOT NULL,
    `delivery_note_no` VARCHAR(100) NOT NULL,
    `received_date` DATE NOT NULL,
    `status` ENUM('Completed', 'Partial') NOT NULL DEFAULT 'Completed',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_gr_po` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_gr_receiver` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. TABEL RINCIAN ITEM GR
CREATE TABLE `gr_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `gr_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `qty_received` INT NOT NULL,
    `qc_status` ENUM('Passed', 'Rejected', 'Rework') NOT NULL DEFAULT 'Passed',
    `notes` VARCHAR(255) NULL,
    CONSTRAINT `fk_gritem_gr` FOREIGN KEY (`gr_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_gritem_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. TABEL MUTASI KARTU STOK (AUDIT TRAIL INVENTARIS)
CREATE TABLE `stock_mutations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `item_id` INT NOT NULL,
    `mutation_type` ENUM('IN', 'OUT') NOT NULL,
    `reference_no` VARCHAR(50) NOT NULL,
    `qty_in` INT NOT NULL DEFAULT 0,
    `qty_out` INT NOT NULL DEFAULT 0,
    `balance` INT NOT NULL,
    `notes` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_mutation_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. TABEL SURAT JALAN / PENGIRIMAN BARANG (DELIVERY NOTES)
CREATE TABLE `delivery_notes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sj_number` VARCHAR(40) NOT NULL UNIQUE,
    `created_by` INT NOT NULL,
    `recipient_type` ENUM('Customer', 'Vendor/Subcont', 'Internal Plant') NOT NULL DEFAULT 'Customer',
    `recipient_name` VARCHAR(150) NOT NULL,
    `recipient_address` TEXT NOT NULL,
    `customer_po_no` VARCHAR(100) NULL,
    `vehicle_no` VARCHAR(30) NOT NULL,
    `driver_name` VARCHAR(100) NOT NULL,
    `delivery_date` DATE NOT NULL,
    `status` ENUM('Draft', 'Shipped', 'Delivered', 'Cancelled') NOT NULL DEFAULT 'Shipped',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_dn_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. TABEL RINCIAN ITEM SURAT JALAN
CREATE TABLE `delivery_note_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `delivery_note_id` INT NOT NULL,
    `item_id` INT NOT NULL,
    `qty_shipped` INT NOT NULL,
    `packaging` VARCHAR(50) NOT NULL DEFAULT 'Box / Pallet',
    `remarks` VARCHAR(255) NULL,
    CONSTRAINT `fk_dnitem_dn` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_dnitem_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

