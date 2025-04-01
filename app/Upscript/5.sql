INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-139', 'PERMIT_ALL', 'api/featureRole/getSidebarFeatureAccess', 1, 'POST', FALSE, now(), now());

ALTER TABLE products
  MODIFY COLUMN buying_price DECIMAL(20, 4);
ALTER TABLE products
  MODIFY COLUMN price DECIMAL(20, 4);

ALTER TABLE purchases
  MODIFY COLUMN total DECIMAL(20, 4);
ALTER TABLE product_purchase
  MODIFY COLUMN price DECIMAL(20, 4);
ALTER TABLE product_purchase
  MODIFY COLUMN subtotal DECIMAL(20, 4);

ALTER TABLE sales
  MODIFY COLUMN total DECIMAL(20, 4);
ALTER TABLE product_sale
  MODIFY COLUMN price DECIMAL(20, 4);
ALTER TABLE product_sale
  MODIFY COLUMN subtotal DECIMAL(20, 4);

ALTER TABLE payments
  MODIFY COLUMN paid DECIMAL(20, 4);
ALTER TABLE expenses
  MODIFY COLUMN amount DECIMAL(20, 4);