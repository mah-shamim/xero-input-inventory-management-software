ALTER TABLE companies
  MODIFY COLUMN contact_phone TEXT NOT NULL;

ALTER TABLE expenses
  ADD COLUMN payment_method INT;
UPDATE expenses
SET payment_method = 1;
ALTER TABLE expenses
  MODIFY COLUMN payment_method INT NOT NULL;