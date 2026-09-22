-- ==============================================================================
-- DATA AWAL REALISTIS PT NANDYA KARYA PERKASA (SEEDERS)
-- ==============================================================================

USE `nkp_inventaris`;

-- 1. DATA PENGGUNA (Password: password123)
INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `department`) VALUES
(1, 'Budi Santoso (Admin IT)', 'budi', 'password123', 'admin', 'Information Technology'),
(2, 'Ir. Hendra Gunawan (SPV)', 'hendra', 'password123', 'supervisor', 'Stamping & Press Plant'),
(3, 'Siti Rahmawati (Purchasing)', 'siti', 'password123', 'purchasing', 'Procurement & Purchasing'),
(4, 'Agus Setiawan (Warehouse)', 'agus', 'password123', 'warehouse', 'Logistics & Warehouse'),
(5, 'Rizky Pratama (Teknisi)', 'rizky', 'password123', 'requester', 'Maintenance & Tooling'),
(6, 'Doni Hermawan (Purchasing)', 'doni', 'password123', 'purchasing', 'Procurement & Purchasing');

-- 2. DATA VENDOR / SUPPLIER REKANAN PT NKP
INSERT INTO `suppliers` (`id`, `supplier_code`, `company_name`, `contact_person`, `phone`, `email`, `address`, `payment_term`, `is_active`) VALUES
(1, 'VND-001', 'PT Krakatau Steel Tbk (Coil Supplier)', 'Bambang Sudibyo', '021-3855511', 'sales.coil@krakatausteel.com', 'Kawasan Industri Cilegon Kav. 12, Banten', 'Net 60', 1),
(2, 'VND-002', 'PT Omron Automation Indonesia', 'Dewi Lestari', '021-8977112', 'order@omron.co.id', 'Wisma 46 Kota BNI Lt. 8, Jakarta Pusat', 'Net 30', 1),
(3, 'VND-003', 'PT Shell Lubricants Industrial', 'Ahmad Farhan', '021-5044432', 'industrial.id@shell.com', 'Pondok Indah Office Tower 3, Jakarta Selatan', 'Net 30', 1),
(4, 'VND-004', 'PT Kawan Lama Sejahtera (Industrial Tools)', 'Herry Susanto', '021-5828282', 'procurement@kawanlama.com', 'Jl. Puri Kencana No. 1, Kembangan, Jakarta Barat', 'Net 30', 1);

-- 3. DATA MASTER BARANG & SUKU CADANG MANUFAKTUR OTOMOTIF
INSERT INTO `items` (`id`, `item_code`, `name`, `category`, `unit`, `stock`, `min_stock`, `location_rack`, `unit_price`) VALUES
-- Mechanical & Dies Suku Cadang Mesin Press
(1, 'SPR-PRS-001', 'Punch Pin Dies Upper Punch M8 (SKD11)', 'Mechanical', 'Pcs', 3, 10, 'Rak-D01-A', 350000.00), -- KRITIS (3 <= 10)
(2, 'SPR-PRS-002', 'Hydraulic Cylinder Seal Kit Aida 250T', 'Mechanical', 'Set', 1, 4, 'Rak-D02-B', 1850000.00), -- KRITIS (1 <= 4)
(3, 'SPR-PRS-003', 'Heavy Duty Coil Spring Brown TF 40x80', 'Mechanical', 'Pcs', 18, 12, 'Rak-D01-C', 145000.00),

-- Electrical & Sensor
(4, 'ELC-SEN-001', 'Proximity Sensor Inductive Omron E2B-M12', 'Electrical', 'Pcs', 2, 8, 'Rak-E03-A', 420000.00), -- KRITIS (2 <= 8)
(5, 'ELC-SEN-002', 'Photoelectric Sensor Banner QS18', 'Electrical', 'Pcs', 12, 6, 'Rak-E03-B', 680000.00),

-- Raw Material (Plat Otomotif Honda Bracket)
(6, 'RAW-STL-001', 'Steel Sheet SPHC 1.2mm x 1219mm x 2438mm', 'Raw Material', 'Sheet', 45, 20, 'Gudang-Plat-01', 320000.00),
(7, 'RAW-STL-002', 'Cold Rolled Steel Coil SPCC-SD 1.0mm (Kg)', 'Raw Material', 'Kg', 1250, 500, 'Gudang-Coil-A', 18500.00),

-- Consumables & Pelumas Mesin
(8, 'CSM-LUB-001', 'Oli Hidrolik Shell Tellus S2 MX 68 (Drum 209L)', 'Consumables', 'Drum', 1, 3, 'Palet-Oli-02', 7800000.00), -- KRITIS (1 <= 3)
(9, 'CSM-WLD-001', 'Elektroda Spot Welding CuCrZr Tipe Cap Tip', 'Consumables', 'Pcs', 35, 20, 'Rak-W01-A', 85000.00),

-- Safety / APD Pabrik
(10, 'SFT-GLV-001', 'Sarung Tangan Safety Kevlar Anti Cut Level 5', 'Safety/APD', 'Pair', 8, 25, 'Lemari-APD-01', 65000.00); -- KRITIS (8 <= 25)

-- 4. KARTU STOK AWAL (STOCK MUTATIONS)
INSERT INTO `stock_mutations` (`item_id`, `mutation_type`, `reference_no`, `qty_in`, `qty_out`, `balance`, `notes`) VALUES
(1, 'IN', 'SALDO-AWAL-2026', 3, 0, 3, 'Inisialisasi Stok Gudang'),
(2, 'IN', 'SALDO-AWAL-2026', 1, 0, 1, 'Inisialisasi Stok Gudang'),
(3, 'IN', 'SALDO-AWAL-2026', 18, 0, 18, 'Inisialisasi Stok Gudang'),
(4, 'IN', 'SALDO-AWAL-2026', 2, 0, 2, 'Inisialisasi Stok Gudang'),
(5, 'IN', 'SALDO-AWAL-2026', 12, 0, 12, 'Inisialisasi Stok Gudang'),
(6, 'IN', 'SALDO-AWAL-2026', 45, 0, 45, 'Inisialisasi Stok Gudang'),
(7, 'IN', 'SALDO-AWAL-2026', 1250, 0, 1250, 'Inisialisasi Stok Gudang'),
(8, 'IN', 'SALDO-AWAL-2026', 1, 0, 1, 'Inisialisasi Stok Gudang'),
(9, 'IN', 'SALDO-AWAL-2026', 35, 0, 35, 'Inisialisasi Stok Gudang'),
(10, 'IN', 'SALDO-AWAL-2026', 8, 0, 8, 'Inisialisasi Stok Gudang');

-- 5. CONTOH TRANSAKSI PR (PURCHASE REQUISITION)
-- PR 1: Sudah Disetujui (Approved) - Siap diterbitkan PO oleh Purchasing
INSERT INTO `purchase_requisitions` (`id`, `pr_number`, `user_id`, `approved_by`, `pr_date`, `target_date`, `priority`, `status`, `general_notes`) VALUES
(1, 'PR/NKP/2026/09/0001', 5, 2, '2026-09-18', '2026-09-25', 'Urgent', 'Approved', 'Kebutuhan mendesak pergantian punch pin mesin press Komatsu 160T line 2.');

INSERT INTO `pr_items` (`pr_id`, `item_id`, `qty_requested`, `remarks`) VALUES
(1, 1, 10, 'Punch Pin Dies Upper Punch M8 aus parah');

-- PR 2: Baru Diajukan (Pending) - Menunggu approval Supervisor
INSERT INTO `purchase_requisitions` (`id`, `pr_number`, `user_id`, `approved_by`, `pr_date`, `target_date`, `priority`, `status`, `general_notes`) VALUES
(2, 'PR/NKP/2026/09/0002', 5, NULL, '2026-09-20', '2026-09-28', 'Normal', 'Pending', 'Safety stock oli hidrolik dan sarung tangan keselamatan menipis.');

INSERT INTO `pr_items` (`pr_id`, `item_id`, `qty_requested`, `remarks`) VALUES
(2, 8, 2, 'Oli hidrolik sisa 1 drum di pabrik'),
(2, 10, 30, 'APD sarung tangan untuk operator stamping');

-- 6. CONTOH TRANSAKSI PO (PURCHASE ORDER)
-- PO 1: Diterbitkan dari PR 1 ke Supplier Kawan Lama Sejahtera (Status: Issued, Siap di-GR oleh Gudang)
INSERT INTO `purchase_orders` (`id`, `po_number`, `pr_id`, `supplier_id`, `created_by`, `po_date`, `delivery_deadline`, `subtotal`, `tax_percent`, `tax_amount`, `grand_total`, `status`, `notes`) VALUES
(1, 'PO/NKP/2026/09/0001', 1, 4, 3, '2026-09-19', '2026-09-24', 3500000.00, 11.00, 385000.00, 3885000.00, 'Issued', 'Pengiriman ke Gudang Teknik Plant Cileungsi. Lampirkan sertifikat material SKD11.');

INSERT INTO `po_items` (`po_id`, `item_id`, `qty_ordered`, `unit_price`, `subtotal`) VALUES
(1, 1, 10, 350000.00, 3500000.00);
