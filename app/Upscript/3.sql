DROP TABLE IF EXISTS reserved_roles;

CREATE TABLE reserved_roles
(
  id         INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  name       VARCHAR(191)     NOT NULL,
  authority  VARCHAR(191)     NOT NULL,
  module_id  INT(10) UNSIGNED NOT NULL,
  created_at TIMESTAMP        NULL,
  updated_at TIMESTAMP        NULL
);
CREATE INDEX reserved_roles_authority_index
  ON reserved_roles (authority);
CREATE INDEX reserved_roles_module_id_index
  ON reserved_roles (module_id);

DROP TABLE IF EXISTS request_map;
CREATE TABLE request_map
(
  id               INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  code             VARCHAR(191)     NOT NULL,
  config_attribute VARCHAR(191)     NOT NULL,
  url              VARCHAR(191)     NOT NULL,
  module_id        INT(10) UNSIGNED NOT NULL,
  http_method      VARCHAR(191)     NOT NULL,
  is_common        TINYINT(1)       NOT NULL,
  created_at       TIMESTAMP        NULL,
  updated_at       TIMESTAMP        NULL
);
CREATE INDEX request_map_module_id_index
  ON request_map (module_id);


DROP TABLE IF EXISTS roles;
CREATE TABLE roles
(
  id               INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  authority        VARCHAR(191)     NOT NULL,
  name             VARCHAR(191)     NOT NULL,
  company_id       INT(10) UNSIGNED NOT NULL,
  reserved_role_id INT(10) UNSIGNED NOT NULL,
  module_id        INT(10) UNSIGNED NOT NULL,
  is_active        TINYINT(1)       NOT NULL,
  created_at       TIMESTAMP        NULL,
  updated_at       TIMESTAMP        NULL,
  CONSTRAINT roles_company_id_foreign
  FOREIGN KEY (company_id) REFERENCES companies (id)
    ON DELETE CASCADE,
  CONSTRAINT roles_reserved_role_id_foreign
  FOREIGN KEY (reserved_role_id) REFERENCES reserved_roles (id)
    ON DELETE CASCADE
);
CREATE INDEX roles_company_id_index
  ON roles (company_id);
CREATE INDEX roles_module_id_index
  ON roles (module_id);
CREATE INDEX roles_reserved_role_id_index
  ON roles (reserved_role_id);

DROP TABLE IF EXISTS user_role;
CREATE TABLE user_role
(
  id         INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  user_id    INT(10) UNSIGNED NOT NULL,
  role_id    INT(10) UNSIGNED NOT NULL,
  created_at TIMESTAMP        NULL,
  updated_at TIMESTAMP        NULL,
  CONSTRAINT user_role_user_id_foreign
  FOREIGN KEY (user_id) REFERENCES users (id)
    ON DELETE CASCADE,
  CONSTRAINT user_role_role_id_foreign
  FOREIGN KEY (role_id) REFERENCES roles (id)
    ON DELETE CASCADE
);
CREATE INDEX user_role_role_id_index
  ON user_role (role_id);
CREATE INDEX user_role_user_id_index
  ON user_role (user_id);


DROP TABLE IF EXISTS features;
CREATE TABLE features
(
  id               INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  code             VARCHAR(191)     NOT NULL,
  name             VARCHAR(191)     NOT NULL,
  request_map_code VARCHAR(191)     NOT NULL,
  module_id        INT(10) UNSIGNED NOT NULL,
  created_at       TIMESTAMP        NULL,
  updated_at       TIMESTAMP        NULL
);
CREATE INDEX features_module_id_index
  ON features (module_id);


DROP TABLE IF EXISTS feature_role;
CREATE TABLE feature_role
(
  id             INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  role_authority VARCHAR(191) NOT NULL,
  feature_code   VARCHAR(191) NOT NULL,
  created_at     TIMESTAMP    NULL,
  updated_at     TIMESTAMP    NULL
);


DROP TABLE IF EXISTS feature_reserved_role;
CREATE TABLE feature_reserved_role
(
  id               INT(10) UNSIGNED AUTO_INCREMENT
    PRIMARY KEY,
  reserved_role_id INT(10) UNSIGNED NOT NULL,
  feature_code     VARCHAR(191)     NOT NULL,
  module_id        INT(10) UNSIGNED NOT NULL,
  created_at       TIMESTAMP        NULL,
  updated_at       TIMESTAMP        NULL,
  CONSTRAINT feature_reserved_role_reserved_role_id_foreign
  FOREIGN KEY (reserved_role_id) REFERENCES reserved_roles (id)
    ON DELETE CASCADE
);
CREATE INDEX feature_reserved_role_module_id_index
  ON feature_reserved_role (module_id);
CREATE INDEX feature_reserved_role_reserved_role_id_index
  ON feature_reserved_role (reserved_role_id);

INSERT INTO reserved_roles (id, name, authority, module_id, created_at, updated_at)
VALUES (101, 'Inventory Admin Reserved Role', 'ROLE_101', 1, now(), now());
INSERT INTO reserved_roles (id, name, authority, module_id, created_at, updated_at)
VALUES (102, 'Purchase Executive Reserved Role', 'ROLE_102', 1, now(), now());
INSERT INTO reserved_roles (id, name, authority, module_id, created_at, updated_at)
VALUES (103, 'Sales Executive Reserved Role', 'ROLE_103', 1, now(), now());

INSERT INTO roles (authority, name, company_id, reserved_role_id, module_id, is_active, created_at, updated_at)
VALUES ('ROLE_101_1', 'Inventory Admin', 1, 101, 1, TRUE, now(), now());
INSERT INTO roles (authority, name, company_id, reserved_role_id, module_id, is_active, created_at, updated_at)
VALUES ('ROLE_102_1', 'Purchase Executive', 1, 102, 1, TRUE, now(), now());
INSERT INTO roles (authority, name, company_id, reserved_role_id, module_id, is_active, created_at, updated_at)
VALUES ('ROLE_103_1', 'Sales Executive', 1, 103, 1, TRUE, now(), now());

INSERT INTO user_role (user_id, role_id, created_at, updated_at) VALUES (1, (SELECT id
                                                                             FROM roles
                                                                             WHERE authority = 'ROLE_101_1'), now(),
                                                                         now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-1', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-2', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-3', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-4', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses/{payroll}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-5', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses/{payroll}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-6', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses/{payroll}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-7', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payroll/expenses/{payroll}/edit', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-8', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-9', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-10', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-11', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands/{brand}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-12', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands/{brand}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-13', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands/{brand}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-14', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/brands/{brand}/edit ', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-15', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-16', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-17', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-18', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories/{category}', 1, 'PATCH', FALSE, now(),
   now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-19', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories/{category}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-20', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories/{category}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-21', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/categories/{category}/edit ', 1, 'GET', FALSE, now(),
   now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-22', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-23', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-24', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-25', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers/{customer}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-26', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers/{customer}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-27', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers/{customer}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-28', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/customers/{customer}/edit', 1, 'GET', FALSE, now(),
        now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-29', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-30', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-31', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-32', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products/{product}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-33', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products/{product}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-34', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products/{product}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-35', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products/{product}/edit', 1, 'GET', FALSE, now(),
        now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-36', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-37', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-38', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-39', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit/{productunit}', 1, 'PATCH', FALSE, now(),
   now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-40', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit/{productunit}', 1, 'GET', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-41', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit/{productunit}', 1, 'DELETE', FALSE, now(),
   now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-42', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/productunit/{productunit}/edit', 1, 'GET', FALSE, now(),
   now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-43', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-44', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-45', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-46', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases/{Purchase}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-47', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases/{Purchase}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-48', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases/{Purchase}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-49', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/purchases/{Purchase}/edit', 1, 'GET', FALSE, now(),
        now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-50', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-51', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-52', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-53', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales/{sale}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-54', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales/{sale}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-55', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales/{sale}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-56', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/sales/{sale}/edit', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-57', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-58', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-59', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-60', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers/{supplier}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-61', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers/{supplier}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-62', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers/{supplier}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-63', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/suppliers/{supplier}/edit', 1, 'GET', FALSE, now(),
        now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-64', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-65', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-66', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-67', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions/{unitconversion}', 1, 'PATCH', FALSE,
   now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-68', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions/{unitconversion}', 1, 'GET', FALSE,
        now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-69', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions/{unitconversion}', 1, 'DELETE', FALSE,
   now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-70', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions/{unitconversion}/edit', 1, 'GET', FALSE,
   now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-71', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/unitconversions/{fromUnitId}/{toInitId}/{quantity}', 1,
   'POST', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-72', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-73', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-74', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-75', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-76', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-77', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-78', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}/edit', 1, 'GET', FALSE,
   now(), now());

# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-79', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units', 1, 'GET', FALSE, now(), now());
# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-80', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units', 1, 'POST', FALSE, now(), now());
# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-81', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/create', 1, 'GET', FALSE, now(), now());
# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-82', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}', 1, 'PATCH', FALSE, now(), now());
# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-83', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}', 1, 'GET', FALSE, now(), now());
# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-84', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}', 1, 'DELETE', FALSE, now(), now());
# INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
# VALUES ('INV-85', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/units/{unit}/edit', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-86', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-87', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-88', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses/create', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-89', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses/{warehouse}', 1, 'PATCH', FALSE, now(),
   now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-90', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses/{warehouse}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-91', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses/{warehouse}', 1, 'DELETE', FALSE, now(),
        now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-92', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/warehouses/{warehouse}/edit', 1, 'GET', FALSE, now(),
   now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-93', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payments/model/{model}/model_id/{model_id}/payment_id', 1, 'POST',
   FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-94', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payments/model/{model}/model_id/{model_id}/payment_id', 1, 'GET',
   FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-95', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/payments/model/{model}/model_id/{model_id}/payment_id/create', 1,
   'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-96', 'ROLE_101_1,ROLE_102_1,ROLE_103_1',
        'api/payments/model/{model}/model_id/{model_id}/payment_id/{payment_id}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-97', 'ROLE_101_1,ROLE_102_1,ROLE_103_1',
        'api/payments/model/{model}/model_id/{model_id}/payment_id/{payment_id}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-98', 'ROLE_101_1,ROLE_102_1,ROLE_103_1',
        'api/payments/model/{model}/model_id/{model_id}/payment_id/{payment_id}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-99', 'ROLE_101_1,ROLE_102_1,ROLE_103_1',
        'api/payments/model/{model}/model_id/{model_id}/payment_id/{payment_id}/edit', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-100', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/actualpurchases', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-101', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/expenses ', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-102', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/overall', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-103', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/products', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-104', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/purchases', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-105', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/sales', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-106', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/suppliers', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-107', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/warehouses', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-108', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/report/warehouses/show', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-109', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/user', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-110', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/versions/update_version', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-111', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'home', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-112', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'login', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-113', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'login', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-114', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'logout', 1, 'GET', FALSE, now(), now());

# Start Work Razib

ALTER TABLE categories
  ADD COLUMN type VARCHAR(191);
UPDATE categories
SET type = 'PRODUCT';
ALTER TABLE categories
  MODIFY COLUMN type VARCHAR(191) NOT NULL;

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-115', 'PERMIT_ALL', 'oauth/authorize', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-116', 'PERMIT_ALL', 'oauth/authorize', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-117', 'PERMIT_ALL', 'oauth/authorize', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-118', 'PERMIT_ALL', 'oauth/clients', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-119', 'PERMIT_ALL', 'oauth/clients', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-116', 'PERMIT_ALL', 'oauth/clients/{client_id}', 1, 'PATCH', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-120', 'PERMIT_ALL', 'oauth/clients/{client_id}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-121', 'PERMIT_ALL', 'oauth/personal-access-tokens', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-122', 'PERMIT_ALL', 'oauth/personal-access-tokens', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-123', 'PERMIT_ALL', 'oauth/personal-access-tokens/{token_id}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-124', 'PERMIT_ALL', 'oauth/scopes', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-125', 'PERMIT_ALL', 'oauth/token', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-126', 'PERMIT_ALL', 'oauth/token/refresh ', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-127', 'PERMIT_ALL', 'oauth/tokens', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-128', 'PERMIT_ALL', 'oauth/tokens/{token_id}', 1, 'DELETE', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-129', 'PERMIT_ALL', 'password/email', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-130', 'PERMIT_ALL', 'password/reset', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-131', 'PERMIT_ALL', 'password/reset', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-132', 'PERMIT_ALL', 'password/reset/{token}', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-133', 'PERMIT_ALL', 'register', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-134', 'PERMIT_ALL', 'register', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-135', 'PERMIT_ALL', 'settings', 1, 'GET', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-136', 'PERMIT_ALL', 'settings', 1, 'POST', FALSE, now(), now());
INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-137', 'PERMIT_ALL', '{all}', 1, 'GET', FALSE, now(), now());

INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES ('INV-138', 'PERMIT_ALL', 'api/featureRole/hasFeatureAccess', 1, 'POST', FALSE, now(), now());

#brand
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_1', 'CREATE BRAND', 'INV-9,INV-10', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_2', 'UPDATE BRAND', 'INV-11,inv-14', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_3', 'SHOW BRAND LIST', 'INV-8', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_4', 'DELETE BRAND', 'INV-13', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_1', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_2', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_3', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_4', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_1', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_2', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_3', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_4', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_1', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_2', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_3', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_4', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_1', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_2', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_3', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_4', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_1', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_2', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_3', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_4', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_1', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_2', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_3', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_4', 1, now(), now());

#Supplier
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_5', 'CREATE SUPPLIER', 'INV-58,INV-59', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_6', 'UPDATE SUPPLIER', 'INV-60,INV-63', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_7', 'SHOW SUPPLIER LIST', 'INV-57', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_8', 'DELETE SUPPLIER', 'INV-62', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_5', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_6', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_7', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_8', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_5', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_6', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_7', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_8', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_5', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_6', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_7', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_8', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_5', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_6', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_7', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_8', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_5', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_6', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_7', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_8', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_5', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_6', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_7', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_8', 1, now(), now());



#Customer
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_9', 'CREATE CUSTOMER', 'INV-23,INV-24', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_10', 'UPDATE CUSTOMER', 'INV-28,INV-25', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_11', 'SHOW CUSTOMER LIST', 'INV-22', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_12', 'DELETE CUSTOMER', 'INV-27', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_9', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_10', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_11', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_12', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_9', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_10', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_11', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_12', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_9', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_10', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_11', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_12', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_9', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_10', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_11', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_12', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_9', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_10', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_11', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_12', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_9', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_10', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_11', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_12', 1, now(), now());


#Warehouse

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_13', 'SHOW WAREHOUSE LIST', 'INV-86', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_14', 'CREATE WAREHOUSE', 'INV-87,INV-88', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_15', 'UPDATE WAREHOUSE', 'INV-89,INV-92', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_16', 'DELETE WAREHOUSE', 'INV-91', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_13', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_14', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_15', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_16', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_13', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_14', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_15', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_16', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_13', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_14', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_15', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_16', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_13', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_14', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_15', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_16', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_13', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_14', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_15', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_16', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_13', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_14', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_15', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_16', 1, now(), now());


#Purchases

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_17', 'SHOW PURCHASES LIST', 'INV-43', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_18', 'CREATE PURCHASES', 'INV-44,INV-45,INV-71', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_19', 'UPDATE PURCHASES', 'INV-46,INV-49,INV-71', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_20', 'DELETE PURCHASES', 'INV-48', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_17', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_18', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_19', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_20', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_17', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_18', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_19', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_20', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_17', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_18', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_19', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_20', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_17', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_18', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_19', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_20', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_17', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_18', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_19', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_20', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_17', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_18', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_19', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_20', 1, now(), now());


#Sales

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_21', 'SHOW SALES LIST', 'INV-50', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_22', 'CREATE SALES', 'INV-51,INV-52,INV-71', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_23', 'UPDATE SALES', 'INV-53,INV-56,INV-71', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_24', 'DELETE SALES', 'INV-55', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_21', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_22', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_23', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_24', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_21', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_22', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_23', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_24', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_21', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_22', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_23', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_24', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_21', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_22', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_23', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_24', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_21', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_22', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_23', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_24', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_21', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_22', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_23', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_24', 1, now(), now());



#Product

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_25', 'SHOW PRODUCT LIST', 'INV-29', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_26', 'CREATE PRODUCT', 'INV-30,INV-31', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_27', 'UPDATE PRODUCT', 'INV-32,INV-35', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_28', 'DELETE PRODUCT', 'INV-34', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_25', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_26', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_27', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_28', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_25', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_26', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_27', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_28', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_25', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_26', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_27', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_28', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_25', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_26', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_27', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_28', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_25', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_26', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_27', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_28', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_25', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_26', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_27', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_28', 1, now(), now());



#Expenses

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_29', 'SHOW EXPENSE LIST', 'INV-1', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_30', 'CREATE EXPENSE', 'INV-2,INV-3', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_31', 'UPDATE EXPENSE', 'INV-4,INV-7', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_32', 'DELETE EXPENSE', 'INV-6', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_29', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_30', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_31', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_32', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_29', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_30', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_31', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_32', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_29', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_30', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_31', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_32', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_29', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_30', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_31', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_32', 1, now(), now());

# Categories

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_33', 'SHOW CATEGORY LIST', 'INV-15', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_34', 'CREATE CATEGORY', 'INV-16,INV-17', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_35', 'UPDATE CATEGORY', 'INV-18,INV-21', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_36', 'DELETE CATEGORY', 'INV-20', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_33', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_34', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_35', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_36', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_33', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_34', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_35', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_36', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_33', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_34', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_35', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_36', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_33', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_34', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_35', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_36', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_33', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_34', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_35', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_36', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_33', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_34', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_35', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_36', 1, now(), now());


#product Unit

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_37', 'SHOW PRODUCTUNIT LIST', 'INV-36', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_38', 'CREATE PRODUCTUNIT', 'INV-37,INV-38', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_39', 'UPDATE PRODUCTUNIT', 'INV-39,INV-41', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_40', 'DELETE PRODUCTUNIT', 'INV-42', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_37', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_38', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_39', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_40', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_37', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_38', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_39', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_40', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_37', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_38', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_39', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_40', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_37', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_38', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_39', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_40', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_37', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_38', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_39', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_40', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_37', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_38', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_39', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_40', 1, now(), now());


#Unit Conversion

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_41', 'SHOW UNITCONVERSION LIST', 'INV-64', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_42', 'CREATE UNITCONVERSION', 'INV-65,INV-66', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_43', 'UPDATE UNITCONVERSION', 'INV-67,INV-70', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_44', 'DELETE UNITCONVERSION', 'INV-69', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_41', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_42', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_43', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_44', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_41', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_42', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_43', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_44', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_41', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_42', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_43', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_44', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_41', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_42', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_43', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_44', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_41', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_42', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_43', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_44', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_41', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_42', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_43', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_44', 1, now(), now());


#Unit

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_45', 'SHOW UNIT LIST', 'INV-72', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_46', 'CREATE UNIT', 'INV-73,INV-74', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_47', 'UPDATE UNIT', 'INV-75,INV-78', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_48', 'DELETE UNIT', 'INV-77', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_45', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_46', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_47', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_48', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_45', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_46', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_47', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_48', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_45', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_46', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_47', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_48', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_45', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_46', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_47', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_48', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_45', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_46', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_47', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_48', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_45', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_46', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_47', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_48', 1, now(), now());


#Payment


INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_49', 'SHOW PAYMENT LIST', 'INV-93', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_50', 'CREATE PAYMENT', 'INV-94,INV-95', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_51', 'UPDATE PAYMENT', 'INV-97,INV-99', 1, now(), now());

INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_52', 'DELETE PAYMENT', 'INV-98', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_49', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_50', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_51', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_52', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_49', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_50', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_51', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_52', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_49', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_50', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_51', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_52', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_49', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_50', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_51', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_52', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_49', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_50', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_51', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_52', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_49', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_50', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_51', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_52', 1, now(), now());

# report
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_53', 'SHOW ACTUAL PURCHASE REPORT', 'INV-100', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_54', 'SHOW EXPENSE REPORT', 'INV-101', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_55', 'SHOW OVERALL REPORT', 'INV-102', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_56', 'SHOW PRODUCT REPORT', 'INV-103', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_57', 'SHOW PURCHASE REPORT', 'INV-104', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_58', 'SHOW SALE REPORT', 'INV-105', 1, now(), now());
INSERT INTO features (code, name, request_map_code, module_id, created_at, updated_at)
VALUES ('F_INV_59', 'SHOW WAREHOUSE REPORT', 'INV-107,INV-108', 1, now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_53', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_54', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_55', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_56', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_57', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_58', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_101_1', 'F_INV_59', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_53', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_54', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_56', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_57', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_102_1', 'F_INV_58', now(), now());

INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_54', now(), now());
INSERT INTO feature_role (role_authority, feature_code, created_at, updated_at)
VALUES ('ROLE_103_1', 'F_INV_58', now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_53', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_54', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_55', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_56', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_57', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_58', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (101, 'F_INV_59', 1, now(), now());

INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_53', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_54', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_56', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_57', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (102, 'F_INV_58', 1, now(), now());


INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_54', 1, now(), now());
INSERT INTO feature_reserved_role (reserved_role_id, feature_code, module_id, created_at, updated_at)
VALUES (103, 'F_INV_58', 1, now(), now());
