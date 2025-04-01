ALTER TABLE warehouses
  DROP INDEX warehouses_code_unique;
ALTER TABLE customers ADD COLUMN is_default BOOLEAN DEFAULT FALSE ;
ALTER TABLE warehouses ADD COLUMN is_default BOOLEAN DEFAULT FALSE ;
ALTER TABLE customers ADD COLUMN details TEXT DEFAULT NULL ;

ALTER TABLE orders ADD COLUMN is_default BOOLEAN DEFAULT FALSE ;
-- Created Order Purchase Table

CREATE TABLE order_purchases
(
  id               INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  order_no         VARCHAR(191)     NOT NULL,
  status           VARCHAR(191)     NOT NULL,
  supplier_id      INT(10) UNSIGNED NOT NULL,
  shipping_cost    VARCHAR(191)     NULL,
  order_date       VARCHAR(191)     NOT NULL,
  overall_discount DOUBLE(8, 2)     NULL,
  total            DECIMAL(20, 4)   NOT NULL,
  note             TEXT             NULL,
  is_canceled      TINYINT(1)       NOT NULL,
  company_id       INT(10) UNSIGNED NOT NULL,
  created_at       TIMESTAMP        NULL,
  updated_at       TIMESTAMP        NULL,
  CONSTRAINT order_purchases_order_no_unique
  UNIQUE (order_no),
  CONSTRAINT order_purchases_supplier_id_foreign
  FOREIGN KEY (supplier_id) REFERENCES suppliers (id)
    ON DELETE CASCADE,
  CONSTRAINT order_purchases_company_id_foreign
  FOREIGN KEY (company_id) REFERENCES companies (id)
    ON DELETE CASCADE
);
CREATE INDEX order_purchases_company_id_index
  ON order_purchases (company_id);
CREATE INDEX order_purchases_supplier_id_index
  ON order_purchases (supplier_id);


-- auto-generated definition
CREATE TABLE order_purchase_product
(
  product_id        INT(10) UNSIGNED         NOT NULL,
  order_purchase_id INT(10) UNSIGNED         NOT NULL,
  warehouse_id      INT(10) UNSIGNED         NOT NULL,
  quantity          DECIMAL(36, 18) UNSIGNED NOT NULL,
  price             DECIMAL(20, 4)           NOT NULL,
  discount          DOUBLE(8, 2)             NOT NULL,
  subtotal          DECIMAL(20, 4)           NOT NULL,
  unit_id           INT                      NOT NULL,
  created_at        TIMESTAMP                NULL,
  updated_at        TIMESTAMP                NULL,
  CONSTRAINT order_purchase_product_product_id_foreign
  FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE,
  CONSTRAINT order_purchase_product_order_purchase_id_foreign
  FOREIGN KEY (order_purchase_id) REFERENCES order_purchases (id)
    ON DELETE CASCADE,
  CONSTRAINT order_purchase_product_warehouse_id_foreign
  FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
    ON DELETE CASCADE
);
CREATE INDEX order_purchase_product_order_purchase_id_index
  ON order_purchase_product (order_purchase_id);
CREATE INDEX order_purchase_product_product_id_index
  ON order_purchase_product (product_id);
CREATE INDEX order_purchase_product_warehouse_id_index
  ON order_purchase_product (warehouse_id);


ALTER TABLE order_purchases MODIFY COLUMN is_canceled BOOLEAN DEFAULT FALSE ;

CREATE TABLE warranties
(
  id               INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  product_id        INT(10) UNSIGNED         NOT NULL,
  customer_id       INT(10) UNSIGNED         NOT NULL,
  company_id        INT(10) UNSIGNED         NOT NULL,
  quantity          DECIMAL(10, 2) UNSIGNED DEFAULT 0,
  status           enum('Receive from Customer',
    'Send to Supplier',
    'Receive from Supplier',
    'Delivered to Customer',
    'Damaged'),
    note             TEXT             NULL,
  warranty_date     TIMESTAMP                NOT NULL,
  created_at        TIMESTAMP                NULL,
  updated_at        TIMESTAMP                NULL,
  CONSTRAINT warranties_product_id_foreign
  FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE,
  CONSTRAINT warranties_customer_id_foreign
  FOREIGN KEY (customer_id) REFERENCES customers (id)
    ON DELETE CASCADE,
  CONSTRAINT warranties_company_id_foreign
  FOREIGN KEY (company_id) REFERENCES companies (id)
    ON DELETE CASCADE
);
CREATE INDEX warranties_product_id_index
  ON warranties (product_id);
CREATE INDEX warranties_customer_id_index
  ON warranties (customer_id);
CREATE INDEX warranties_company_id_index
  ON warranties (company_id);




