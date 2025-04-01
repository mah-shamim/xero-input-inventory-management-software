-- company table
ALTER TABLE companies
  ADD COLUMN code VARCHAR(255) NOT NULL;
ALTER TABLE companies
  ADD COLUMN title VARCHAR(255);
ALTER TABLE companies
  ADD COLUMN address1 VARCHAR(255) NOT NULL;
ALTER TABLE companies
  ADD COLUMN address2 VARCHAR(255);
ALTER TABLE companies
  ADD COLUMN web_url VARCHAR(255);
ALTER TABLE companies
  ADD COLUMN contact_name VARCHAR(255) NOT NULL;
ALTER TABLE companies
  ADD COLUMN contact_surname VARCHAR(255) NOT NULL;
ALTER TABLE companies
  ADD COLUMN contact_phone VARCHAR(255) NOT NULL;
ALTER TABLE companies
  DROP COLUMN address;

INSERT INTO companies (name, code, title, address1, contact_name, contact_surname, contact_phone, is_active)
VALUES ('MOIN', 'MOIN', 'MOIN', 'JESSORE', 'MOIN', 'MOIN', '01775414552', TRUE);

ALTER TABLE users
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE users
SET company_id = 1;
ALTER TABLE users
  ADD CONSTRAINT users_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE units
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE units
SET company_id = 1;
ALTER TABLE units
  ADD CONSTRAINT units_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE branches
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE branches
SET company_id = 1;
ALTER TABLE branches
  ADD CONSTRAINT branches_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE warehouses
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE warehouses
SET company_id = 1;
ALTER TABLE warehouses
  ADD CONSTRAINT warehouses_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE brands
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE brands
SET company_id = 1;
ALTER TABLE brands
  ADD CONSTRAINT brands_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE categories
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE categories
SET company_id = 1;
ALTER TABLE categories
  ADD CONSTRAINT categories_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE products
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE products
SET company_id = 1;
ALTER TABLE products
  ADD CONSTRAINT products_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE suppliers
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE suppliers
SET company_id = 1;
ALTER TABLE suppliers
  ADD CONSTRAINT suppliers_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE purchases
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE purchases
SET company_id = 1;
ALTER TABLE purchases
  ADD CONSTRAINT purchases_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE customers
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE customers
SET company_id = 1;
ALTER TABLE customers
  ADD CONSTRAINT customers_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE sales
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE sales
SET company_id = 1;
ALTER TABLE sales
  ADD CONSTRAINT sales_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE banks
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE banks
SET company_id = 1;
ALTER TABLE banks
  ADD CONSTRAINT banks_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE unit_conversions
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE unit_conversions
SET company_id = 1;
ALTER TABLE unit_conversions
  ADD CONSTRAINT unit_conversions_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE loans
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE loans
SET company_id = 1;
ALTER TABLE loans
  ADD CONSTRAINT loans_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE expenses
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE expenses
SET company_id = 1;
ALTER TABLE expenses
  ADD CONSTRAINT expenses_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE settings
  ADD company_id INT UNSIGNED NOT NULL;
UPDATE settings
SET company_id = 1;
ALTER TABLE settings
  ADD CONSTRAINT settings_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies (id)
  ON DELETE CASCADE;

ALTER TABLE settings
  DROP FOREIGN KEY settings_user_id_foreign;
ALTER TABLE settings
  DROP COLUMN user_id;

# --Done By Raz-
ALTER TABLE products
  MODIFY COLUMN buying_price DECIMAL(10, 4);

ALTER TABLE products
  MODIFY COLUMN price DECIMAL(10, 4);

ALTER TABLE product_warehouse
  MODIFY COLUMN quantity DECIMAL(36, 18) UNSIGNED NOT NULL;


ALTER TABLE purchases
  MODIFY COLUMN total DECIMAL(10, 4) NOT NULL;

ALTER TABLE product_purchase
  MODIFY COLUMN quantity DECIMAL(36, 18) UNSIGNED NOT NULL;

ALTER TABLE product_purchase
  MODIFY COLUMN price DECIMAL(10, 4) NOT NULL;

ALTER TABLE product_purchase
  MODIFY COLUMN subtotal DECIMAL(10, 4) NOT NULL;

ALTER TABLE sales
  MODIFY COLUMN total DECIMAL(10, 4) NOT NULL;

ALTER TABLE product_sale
  MODIFY COLUMN quantity DECIMAL(36, 18) UNSIGNED NOT NULL;

ALTER TABLE product_sale
  MODIFY COLUMN price DECIMAL(10, 4) NOT NULL;

ALTER TABLE product_sale
  MODIFY COLUMN subtotal DECIMAL(10, 4) NOT NULL;

ALTER TABLE product_warehouse
  MODIFY COLUMN quantity DOUBLE NOT NULL;

ALTER TABLE categories
  MODIFY COLUMN description TEXT;

ALTER TABLE unit_conversions
  MODIFY COLUMN conversion_factor DECIMAL(36, 18) UNSIGNED NOT NULL;

ALTER TABLE payments
  MODIFY COLUMN paid DECIMAL(10, 4) NOT NULL;

ALTER TABLE expenses
  MODIFY COLUMN amount DECIMAL(10, 4) NOT NULL;

ALTER TABLE products
  DROP INDEX products_slug_index;
ALTER TABLE products
  MODIFY COLUMN slug VARCHAR(191);