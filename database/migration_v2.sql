-- =============================================
-- Freshly Migration: Add new order statuses & product_id to messages
-- Run this on existing databases that were created before this update
-- =============================================

-- Add new order statuses (packing, ready_to_ship, cancelled)
ALTER TABLE orders 
MODIFY COLUMN status ENUM('pending', 'processing', 'packing', 'ready_to_ship', 'shipped', 'delivered', 'completed', 'cancelled') NOT NULL DEFAULT 'pending';

-- Add product_id to messages table for product context in chat
ALTER TABLE messages 
ADD COLUMN product_id INT DEFAULT NULL AFTER receiver_id,
ADD FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL;
