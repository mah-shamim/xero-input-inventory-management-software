DROP TABLE IF EXISTS product_damages;
CREATE TABLE product_damages
(
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id   INT UNSIGNED   NOT NULL,
  warehouse_id INT UNSIGNED   NOT NULL,
  quantity     DOUBLE         NOT NULL,
  unit_id      INT UNSIGNED   NOT NULL,
  sale_value   DECIMAL(20, 4) NOT NULL,
  created_at   TIMESTAMP      NULL,
  updated_at   TIMESTAMP      NULL,
  company_id   INT UNSIGNED   NOT NULL,
  CONSTRAINT product_damages_product_id_foreign
  FOREIGN KEY (product_id) REFERENCES products (id)
    ON DELETE CASCADE,
  CONSTRAINT product_damages_warehouse_id_foreign
  FOREIGN KEY (warehouse_id) REFERENCES warehouses (id)
    ON DELETE CASCADE,
  CONSTRAINT product_damages_unit_id_foreign
  FOREIGN KEY (unit_id) REFERENCES units (id)
    ON DELETE CASCADE,
  CONSTRAINT product_damages_company_id_foreign
  FOREIGN KEY (company_id) REFERENCES companies (id)
    ON DELETE CASCADE
);

CREATE INDEX product_damages_product_id_index
  ON product_damages (product_id);

CREATE INDEX product_damages_warehouse_id_index
  ON product_damages (warehouse_id);

CREATE INDEX product_damages_unit_id_index
  ON product_damages (unit_id);

CREATE INDEX product_damages_company_id_index
  ON product_damages (company_id);

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-141', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-142', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-143', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-144', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages/{productdamage}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-145', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages/{productdamage}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-146', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages/{productdamage}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-147', 'ROLE_101_1,ROLE_102_1', 'api/inventory/productdamages/{productdamage}/edit', 1, 'GET', FALSE, now(),
        now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_60', 'CREATE PRODUCT DAMAGE', 'INV-141,INV-142', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_61', 'UPDATE PRODUCT DAMAGE', 'INV-143,INV-144', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_63', 'SHOW PRODUCT DAMAGE LIST', 'INV-145', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_64', 'DELETE PRODUCT DAMAGE', 'INV-146', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_60', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_61', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_63', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_64', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_60', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_61', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_63', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_64', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_60', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_61', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_63', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_64', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_60', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_61', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_63', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_64', 1, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-148', 'ROLE_101_1,ROLE_102_1', 'api/inventory/units/getUnits', 1, 'POST', FALSE, now(),
        now());