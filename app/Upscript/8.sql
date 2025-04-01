INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-149', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-150', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-151', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-152', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return/{return}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-153', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return/{return}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-154', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return/{return}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-155', 'ROLE_103_1', 'api/inventory/sale/{sale_id}/return/{return}/edit', 1, 'GET', FALSE, now(),
        now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_65', 'CREATE SALE RETURN', 'INV-150,INV-151', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_66', 'UPDATE SALE RETURN', 'INV-152,INV-153', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_67', 'SHOW SALE RETURN', 'INV-152', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_68', 'DELETE SALE RETURN', 'INV-153', 1, now(), now());

CREATE OR REPLACE VIEW view_sales AS
  SELECT
    s.id,
    s.ref,
    s.biller,
    s.total,
    CASE WHEN s.status = 1
      THEN 'Cash'
    WHEN s.status = 2
      THEN 'Credit Card'
    WHEN s.status = 3
      THEN 'Cheque'
    ELSE '' END AS status,
    CASE WHEN s.payment_status = 1
      THEN 'Paid'
    WHEN s.payment_status = 2
      THEN 'Partial'
    WHEN s.payment_status = 3
      THEN 'Due'
    ELSE '' END AS payment_status,
    s.sales_date,
    s.created_at,
    s.company_id,
    u.name         customer_name,
    SUM(p.paid) AS paid
  FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN payments p ON p.paymentable_id = s.id AND p.paymentable_type LIKE '%Sale'
  GROUP BY s.id;

CREATE OR REPLACE VIEW view_purchases AS
  SELECT
    p.id,
    p.ref,
    p.total,
    CASE WHEN p.status = 1
      THEN 'Cash'
    WHEN p.status = 2
      THEN 'Credit Card'
    WHEN p.status = 3
      THEN 'Cheque'
    ELSE '' END   AS status,
    CASE WHEN p.payment_status = 1
      THEN 'Paid'
    WHEN p.payment_status = 2
      THEN 'Partial'
    WHEN p.payment_status = 3
      THEN 'Due'
    ELSE '' END   AS payment_status,
    p.purchase_date,
    p.created_at,
    p.company_id,
    u.name           supplier_name,
    SUM(pay.paid) AS paid
  FROM purchases p
    LEFT JOIN suppliers s ON p.supplier_id = s.id
    LEFT JOIN users u ON s.user_id = u.id
    LEFT JOIN payments pay ON pay.paymentable_id = p.id AND pay.paymentable_type LIKE '%Purchase'
  GROUP BY p.id;

DROP TABLE IF EXISTS purchase_returns;
DROP TABLE IF EXISTS sales_returns;

-- auto-generated definition
CREATE TABLE returns
(
  id              INT UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  returnable_id   INT UNSIGNED                    NOT NULL,
  returnable_type VARCHAR(191)                    NOT NULL,
  product_id      INT UNSIGNED                    NOT NULL,
  unit_id         INT UNSIGNED                    NOT NULL,
  warehouse_id    INT UNSIGNED                    NOT NULL,
  quantity        DECIMAL(20, 4) DEFAULT '0.0000' NOT NULL,
  amount          DECIMAL(20, 4) DEFAULT '0.0000' NOT NULL,
  terms           TEXT                            NULL,
  company_id      INT UNSIGNED                    NOT NULL,
  deleted_at      TIMESTAMP                       NULL,
  created_at      TIMESTAMP                       NULL,
  updated_at      TIMESTAMP                       NULL,
  CONSTRAINT returns_product_id_foreign
  FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE,
  CONSTRAINT returns_unit_id_foreign
  FOREIGN KEY (unit_id) REFERENCES units (id)
    ON DELETE CASCADE,
  CONSTRAINT returns_warehouse_id_foreign
  FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
    ON DELETE CASCADE,
  CONSTRAINT returns_company_id_foreign
  FOREIGN KEY (company_id) REFERENCES companies (id)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX returns_id_index
  ON returns (id);

CREATE INDEX returns_product_id_index
  ON returns (product_id);

CREATE INDEX returns_unit_id_index
  ON returns (unit_id);

CREATE INDEX returns_warehouse_id_index
  ON returns (warehouse_id);

CREATE INDEX returns_company_id_index
  ON returns (company_id);