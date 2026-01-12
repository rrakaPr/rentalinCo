-- =====================================================
-- VHRent - Migration: Update Transaksi for Online Orders
-- Run this script to add online order support
-- =====================================================

-- Add new status values and make admin_id nullable
-- Note: MySQL doesn't support direct ENUM modification, so we need to ALTER the column

-- Step 1: Modify the status column to include new values
ALTER TABLE transaksi 
MODIFY COLUMN status ENUM('Menunggu', 'Disewa', 'Dikembalikan', 'Terlambat', 'Dibatalkan', 'Ditolak') DEFAULT 'Menunggu';

-- Step 2: Make admin_id nullable for online orders
ALTER TABLE transaksi 
MODIFY COLUMN admin_id INT NULL;

-- Step 3: Add approved_at column if not exists
ALTER TABLE transaksi 
ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL AFTER admin_id;

-- Step 4: Update foreign key constraint (may need to drop and recreate)
-- Only run if you encounter foreign key issues:
-- ALTER TABLE transaksi DROP FOREIGN KEY transaksi_ibfk_3;
-- ALTER TABLE transaksi ADD CONSTRAINT transaksi_ibfk_3 FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL;

SELECT 'Migration completed successfully!' as status;
