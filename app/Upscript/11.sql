alter table product_purchase
  add column others JSON after unit_id;

alter table product_sale
    add column others JSON after unit_id;


CREATE table if not exists sessions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  payload TEXT,
  last_activity INT
);


ALTER TABLE customers
  ADD COLUMN `name` VARCHAR(45) NULL AFTER `user_id`,
  ADD COLUMN `loggable` TINYINT(1) NULL DEFAULT 0 AFTER `is_default`,
  ADD COLUMN `email` VARCHAR(45) NULL DEFAULT NULL AFTER `loggable`,
  CHANGE COLUMN `phone` `phone` VARCHAR(191) NULL DEFAULT NULL ;
ALTER TABLE customers MODIFY user_id int(10) unsigned DEFAULT null ;

ALTER TABLE suppliers
  ADD COLUMN `name` VARCHAR(45) NULL AFTER `user_id`,
  ADD COLUMN `loggable` TINYINT(1) NULL DEFAULT 0,
  ADD COLUMN `email` VARCHAR(45) NULL DEFAULT NULL AFTER `loggable`,
  CHANGE COLUMN `phone` `phone` VARCHAR(191) NULL DEFAULT NULL ;
ALTER TABLE suppliers MODIFY user_id int(10) unsigned DEFAULT null ;

ALTER table product_unit
    CHANGE COLUMN `parent_id` `parent_id` INT(11) null default 0;


CREATE OR REPLACE VIEW view_purchases_total_due AS
SELECT
    p.id,
    p.total,
    SUM(pay.paid) AS paid,
    (p.total - SUM(pay.paid)) as due
FROM purchases p
         LEFT JOIN payments pay ON pay.paymentable_id = p.id AND pay.paymentable_type LIKE '%Purchase'
         LEFT JOIN suppliers s ON p.supplier_id = s.id
GROUP BY p.id;
