-- auto-generated definition
CREATE TABLE orders
(
  id               INT UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  order_no         VARCHAR(191)                NOT NULL,
  status           VARCHAR(191)                NOT NULL,
  biller           VARCHAR(191)                NOT NULL,
  customer_id      INT UNSIGNED                NOT NULL,
  salesman_code    VARCHAR(191)                NULL,
  shipping_cost    VARCHAR(191)                NULL,
  order_date       VARCHAR(191)                NOT NULL,
  overall_discount DOUBLE(8, 2)                NULL,
  total            DECIMAL(20, 4)              NOT NULL,
  note             TEXT                        NULL,
  is_canceled      BOOLEAN DEFAULT FALSE       NOT NULL,
  company_id       INT UNSIGNED                NOT NULL,
  created_at       TIMESTAMP                   NULL,
  updated_at       TIMESTAMP                   NULL,
  CONSTRAINT orders_order_no_unique
  UNIQUE (order_no),
  CONSTRAINT orders_customer_id_foreign
  FOREIGN KEY (customer_id) REFERENCES customers (id)
    ON DELETE CASCADE,
  CONSTRAINT orders_company_id_foreign
  FOREIGN KEY (company_id) REFERENCES companies (id)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX orders_customer_id_index
  ON orders (customer_id);

CREATE INDEX orders_company_id_index
  ON orders (company_id);

-- auto-generated definition
CREATE TABLE order_product
(
  product_id   INT UNSIGNED             NOT NULL,
  order_id     INT UNSIGNED             NOT NULL,
  warehouse_id INT UNSIGNED             NOT NULL,
  quantity     DECIMAL(36, 18) UNSIGNED NOT NULL,
  price        DECIMAL(20, 4)           NOT NULL,
  discount     DOUBLE(8, 2)             NOT NULL,
  subtotal     DECIMAL(20, 4)           NOT NULL,
  unit_id      INT                      NOT NULL,
  created_at   TIMESTAMP                NULL,
  updated_at   TIMESTAMP                NULL,
  CONSTRAINT order_product_product_id_foreign
  FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE,
  CONSTRAINT order_product_order_id_foreign
  FOREIGN KEY (order_id) REFERENCES orders (id)
    ON DELETE CASCADE,
  CONSTRAINT order_product_warehouse_id_foreign
  FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX order_product_product_id_index
  ON order_product (product_id);

CREATE INDEX order_product_order_id_index
  ON order_product (order_id);

CREATE INDEX order_product_warehouse_id_index
  ON order_product (warehouse_id);

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-156', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-157', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-158', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-159', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders/{order}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-160', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders/{order}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-161', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders/{order}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-162', 'ROLE_101_1,ROLE_103_1', 'api/inventory/orders/{order}/edit', 1, 'GET', FALSE, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_69', 'CREATE ORDER', 'INV-158,INV-157', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_70', 'UPDATE ORDER', 'INV-159,INV-162', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_71', 'SHOW ORDER LIST', 'INV-156', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_72', 'DELETE ORDER', 'INV-161', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_69', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_70', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_71', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_72', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_69', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_70', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_71', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_72', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_69', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_70', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_71', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_72', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_69', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_70', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_71', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_72', 1, now(), now());

CREATE OR REPLACE VIEW view_orders AS
  SELECT
    o.id,
    o.order_no,
    o.biller,
    o.total,
    CASE WHEN o.status = 1
      THEN 'New'
    WHEN o.status = 2
      THEN 'Processing'
    WHEN o.status = 3
      THEN 'Completed'
    ELSE '' END AS status,
    o.order_date,
    o.created_at,
    o.company_id,
    o.is_canceled,
    u.name         customer_name
  FROM orders o
    LEFT JOIN customers c ON o.customer_id = c.id
    LEFT JOIN users u ON c.user_id = u.id
  GROUP BY o.id;

