INSERT INTO request_map (code, config_attribute, url, module_id, http_method, is_common, created_at, updated_at)
VALUES
  ('INV-140', 'ROLE_101_1,ROLE_102_1,ROLE_103_1', 'api/inventory/products/getProducts', 1, 'POST', FALSE, now(), now());

UPDATE features
SET request_map_code = CONCAT(request_map_code, ',INV-140')
WHERE code IN ('F_INV_38', 'F_INV_18', 'F_INV_22');