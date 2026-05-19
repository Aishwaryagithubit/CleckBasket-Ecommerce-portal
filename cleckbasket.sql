DROP TABLE product_report CASCADE CONSTRAINTS;
DROP TABLE report CASCADE CONSTRAINTS;
DROP TABLE payment CASCADE CONSTRAINTS;
DROP TABLE favourite_product CASCADE CONSTRAINTS;
DROP TABLE favourite CASCADE CONSTRAINTS;
DROP TABLE review CASCADE CONSTRAINTS;
DROP TABLE discount CASCADE CONSTRAINTS;
DROP TABLE order_product CASCADE CONSTRAINTS;
DROP TABLE orders CASCADE CONSTRAINTS;
DROP TABLE cart_product CASCADE CONSTRAINTS;
DROP TABLE cart CASCADE CONSTRAINTS;
DROP TABLE product CASCADE CONSTRAINTS;
DROP TABLE product_category CASCADE CONSTRAINTS;
DROP TABLE shop CASCADE CONSTRAINTS;
DROP TABLE coupon CASCADE CONSTRAINTS;
DROP TABLE collection_slot CASCADE CONSTRAINTS;
DROP TABLE users CASCADE CONSTRAINTS;

-- =========================================================
-- DROP SEQUENCES
-- =========================================================
DROP SEQUENCE users_seq;
DROP SEQUENCE shop_seq;
DROP SEQUENCE product_category_seq;
DROP SEQUENCE collection_slot_seq;
DROP SEQUENCE coupon_seq;
DROP SEQUENCE product_seq;
DROP SEQUENCE cart_seq;
DROP SEQUENCE favourite_seq;
DROP SEQUENCE orders_seq;
DROP SEQUENCE discount_seq;
DROP SEQUENCE review_seq;
DROP SEQUENCE payment_seq;
DROP SEQUENCE report_seq;

-- =========================================================
-- CREATE TABLES
-- =========================================================

CREATE TABLE users (
  user_id          NUMBER PRIMARY KEY,
  firstname        VARCHAR2(50),
  lastname         VARCHAR2(50),
  email            VARCHAR2(100) UNIQUE,
  contact_no       VARCHAR2(20),
  password_hash    VARCHAR2(200),
  verification_code VARCHAR2(50),
  role             VARCHAR2(20),
  created_date     DATE DEFAULT SYSDATE,
  status           VARCHAR2(20)
);

CREATE SEQUENCE users_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER users_trg
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
  IF :NEW.user_id IS NULL THEN
    SELECT users_seq.NEXTVAL INTO :NEW.user_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- SHOP  (user_id references users — trader who owns the shop)
-- =========================================================
CREATE TABLE shop (
  shop_id           NUMBER PRIMARY KEY,
  shop_name         VARCHAR2(100),
  registration_date DATE DEFAULT SYSDATE,
  description       VARCHAR2(500),
  user_id           NUMBER,
  CONSTRAINT fk_shop_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE SEQUENCE shop_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER shop_trg
BEFORE INSERT ON shop
FOR EACH ROW
BEGIN
  IF :NEW.shop_id IS NULL THEN
    SELECT shop_seq.NEXTVAL INTO :NEW.shop_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- PRODUCT CATEGORY
-- =========================================================
CREATE TABLE product_category (
  product_category_id NUMBER PRIMARY KEY,
  category_type       VARCHAR2(100)
);

CREATE SEQUENCE product_category_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER product_category_trg
BEFORE INSERT ON product_category
FOR EACH ROW
BEGIN
  IF :NEW.product_category_id IS NULL THEN
    SELECT product_category_seq.NEXTVAL INTO :NEW.product_category_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- COLLECTION SLOT
-- =========================================================
CREATE TABLE collection_slot (
  collection_slot_id NUMBER PRIMARY KEY,
  slot_day           VARCHAR2(20),
  slot_date          DATE,
  slot_time          VARCHAR2(20)
);

CREATE SEQUENCE collection_slot_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER collection_slot_trg
BEFORE INSERT ON collection_slot
FOR EACH ROW
BEGIN
  IF :NEW.collection_slot_id IS NULL THEN
    SELECT collection_slot_seq.NEXTVAL INTO :NEW.collection_slot_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- COUPON
-- =========================================================
CREATE TABLE coupon (
  coupon_id               NUMBER PRIMARY KEY,
  coupon_title            VARCHAR2(100),
  coupon_offer            VARCHAR2(200),
  start_date              DATE,
  end_date                DATE,
  coupon_discount_percent NUMBER
);

CREATE SEQUENCE coupon_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER coupon_trg
BEFORE INSERT ON coupon
FOR EACH ROW
BEGIN
  IF :NEW.coupon_id IS NULL THEN
    SELECT coupon_seq.NEXTVAL INTO :NEW.coupon_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- PRODUCT
-- =========================================================
CREATE TABLE product (
  product_id          NUMBER PRIMARY KEY,
  product_name        VARCHAR2(100),
  description         VARCHAR2(500),
  price               NUMBER(10,2),
  stock_quantity      NUMBER,
  min_order           NUMBER,
  max_order           NUMBER,
  allergy_information VARCHAR2(200),
  product_image       VARCHAR2(200),
  shop_id             NUMBER,
  product_category_id NUMBER,
  CONSTRAINT fk_product_shop     FOREIGN KEY (shop_id)             REFERENCES shop(shop_id),
  CONSTRAINT fk_product_category FOREIGN KEY (product_category_id) REFERENCES product_category(product_category_id)
);

CREATE SEQUENCE product_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER product_trg
BEFORE INSERT ON product
FOR EACH ROW
BEGIN
  IF :NEW.product_id IS NULL THEN
    SELECT product_seq.NEXTVAL INTO :NEW.product_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- CART
-- =========================================================
CREATE TABLE cart (
  cart_id      NUMBER PRIMARY KEY,
  user_id      NUMBER,
  created_date DATE DEFAULT SYSDATE,
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE SEQUENCE cart_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER cart_trg
BEFORE INSERT ON cart
FOR EACH ROW
BEGIN
  IF :NEW.cart_id IS NULL THEN
    SELECT cart_seq.NEXTVAL INTO :NEW.cart_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- CART PRODUCT
-- =========================================================
CREATE TABLE cart_product (
  cart_id    NUMBER,
  product_id NUMBER,
  quantity   NUMBER,
  CONSTRAINT pk_cart_product PRIMARY KEY (cart_id, product_id),
  CONSTRAINT fk_cp_cart    FOREIGN KEY (cart_id)    REFERENCES cart(cart_id),
  CONSTRAINT fk_cp_product FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- =========================================================
-- FAVOURITE
-- =========================================================
CREATE TABLE favourite (
  favourite_id NUMBER PRIMARY KEY,
  user_id      NUMBER,
  CONSTRAINT fk_fav_user FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE SEQUENCE favourite_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER favourite_trg
BEFORE INSERT ON favourite
FOR EACH ROW
BEGIN
  IF :NEW.favourite_id IS NULL THEN
    SELECT favourite_seq.NEXTVAL INTO :NEW.favourite_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- FAVOURITE PRODUCT
-- =========================================================
CREATE TABLE favourite_product (
  favourite_id NUMBER,
  product_id   NUMBER,
  added_date   DATE DEFAULT SYSDATE,
  CONSTRAINT pk_fav_product PRIMARY KEY (favourite_id, product_id),
  CONSTRAINT fk_fp_fav     FOREIGN KEY (favourite_id) REFERENCES favourite(favourite_id),
  CONSTRAINT fk_fp_product FOREIGN KEY (product_id)   REFERENCES product(product_id)
);

-- =========================================================
-- ORDERS
-- =========================================================
CREATE TABLE orders (
  order_id           NUMBER PRIMARY KEY,
  user_id            NUMBER,
  order_status       VARCHAR2(50),
  order_amount       NUMBER(10,2),
  order_date         DATE DEFAULT SYSDATE,
  collection_slot_id NUMBER,
  coupon_id          NUMBER,
  CONSTRAINT fk_order_user   FOREIGN KEY (user_id)            REFERENCES users(user_id),
  CONSTRAINT fk_order_slot   FOREIGN KEY (collection_slot_id) REFERENCES collection_slot(collection_slot_id),
  CONSTRAINT fk_order_coupon FOREIGN KEY (coupon_id)          REFERENCES coupon(coupon_id)
);

CREATE SEQUENCE orders_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER orders_trg
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
  IF :NEW.order_id IS NULL THEN
    SELECT orders_seq.NEXTVAL INTO :NEW.order_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- ORDER PRODUCT
-- =========================================================
CREATE TABLE order_product (
  order_id          NUMBER,
  product_id        NUMBER,
  quantity          NUMBER,
  price_at_purchase NUMBER(10,2),
  CONSTRAINT pk_order_product PRIMARY KEY (order_id, product_id),
  CONSTRAINT fk_op_order   FOREIGN KEY (order_id)   REFERENCES orders(order_id),
  CONSTRAINT fk_op_product FOREIGN KEY (product_id) REFERENCES product(product_id)
);

-- =========================================================
-- DISCOUNT
-- =========================================================
CREATE TABLE discount (
  discount_id         NUMBER PRIMARY KEY,
  discount_percentage NUMBER,
  start_date          DATE,
  end_date            DATE,
  product_id          NUMBER,
  CONSTRAINT fk_discount_product FOREIGN KEY (product_id) REFERENCES product(product_id)
);

CREATE SEQUENCE discount_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER discount_trg
BEFORE INSERT ON discount
FOR EACH ROW
BEGIN
  IF :NEW.discount_id IS NULL THEN
    SELECT discount_seq.NEXTVAL INTO :NEW.discount_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- REVIEW
-- =========================================================
CREATE TABLE review (
  review_id    NUMBER PRIMARY KEY,
  review       VARCHAR2(500),
  review_rating NUMBER,
  user_id      NUMBER,
  product_id   NUMBER,
  created_date DATE DEFAULT SYSDATE,
  CONSTRAINT fk_review_user    FOREIGN KEY (user_id)    REFERENCES users(user_id),
  CONSTRAINT fk_review_product FOREIGN KEY (product_id) REFERENCES product(product_id)
);

CREATE SEQUENCE review_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER review_trg
BEFORE INSERT ON review
FOR EACH ROW
BEGIN
  IF :NEW.review_id IS NULL THEN
    SELECT review_seq.NEXTVAL INTO :NEW.review_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- PAYMENT
-- =========================================================
CREATE TABLE payment (
  payment_id     NUMBER PRIMARY KEY,
  payment_date   DATE DEFAULT SYSDATE,
  payment_amount NUMBER(10,2),
  payment_method VARCHAR2(50),
  user_id        NUMBER,
  order_id       NUMBER,
  CONSTRAINT fk_payment_user  FOREIGN KEY (user_id)  REFERENCES users(user_id),
  CONSTRAINT fk_payment_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE SEQUENCE payment_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER payment_trg
BEFORE INSERT ON payment
FOR EACH ROW
BEGIN
  IF :NEW.payment_id IS NULL THEN
    SELECT payment_seq.NEXTVAL INTO :NEW.payment_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- REPORT
-- =========================================================
CREATE TABLE report (
  report_id    NUMBER PRIMARY KEY,
  report_type  VARCHAR2(100),
  report_title VARCHAR2(200),
  report_date  DATE DEFAULT SYSDATE,
  user_id      NUMBER,
  order_id     NUMBER,
  CONSTRAINT fk_report_user  FOREIGN KEY (user_id)  REFERENCES users(user_id),
  CONSTRAINT fk_report_order FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

CREATE SEQUENCE report_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER report_trg
BEFORE INSERT ON report
FOR EACH ROW
BEGIN
  IF :NEW.report_id IS NULL THEN
    SELECT report_seq.NEXTVAL INTO :NEW.report_id FROM dual;
  END IF;
END;
/

-- =========================================================
-- PRODUCT REPORT
-- =========================================================
CREATE TABLE product_report (
  product_id NUMBER,
  report_id  NUMBER,
  CONSTRAINT pk_product_report PRIMARY KEY (product_id, report_id),
  CONSTRAINT fk_pr_product FOREIGN KEY (product_id) REFERENCES product(product_id),
  CONSTRAINT fk_pr_report  FOREIGN KEY (report_id)  REFERENCES report(report_id)
);


-- =========================================================
-- DATA: USERS
-- user_id 1=Admin, 2=John(Butcher), 3=Sarah(Green),
--         4=Michael(Fish), 5=Emily(Bakery),
--         6=David(Customer), 7=Lily(Customer), 8=Aishwarya(Admin)
-- =========================================================
INSERT INTO users VALUES (NULL,'Admin','System','admin@cleckbasket.com','9800000001','admin123','V001','ADMIN',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'John','Butcher','john@butcher.com','9800000002','pass123','V002','TRADER',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'Sarah','Green','sarah@green.com','9800000003','pass123','V003','TRADER',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'Michael','Fish','michael@fish.com','9800000004','pass123','V004','TRADER',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'Emily','Bakery','emily@bakery.com','9800000005','pass123','V005','TRADER',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'David','Customer','david@gmail.com','9800000006','pass123','V006','CUSTOMER',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'Lily','Customer','lily@gmail.com','9800000007','pass123','V007','CUSTOMER',SYSDATE,'ACTIVE');
INSERT INTO users VALUES (NULL,'Aishwarya','Admin','aish@cleckbasket.com','9800000008','admin123','V008','ADMIN',SYSDATE,'ACTIVE');


-- =========================================================
-- DATA: SHOP
-- FIX: column order is (shop_id, shop_name, registration_date, description, user_id)
-- The original script passed (NULL, name, description, user_id_number, date)
-- which put a NUMBER into the DATE column => ORA-00932
-- Corrected: pass SYSDATE as registration_date, then description, then user_id NUMBER
--
-- shop_id 1 = Butcher Shop  (owned by user 2 = John)
-- shop_id 2 = Green Grocer  (owned by user 3 = Sarah)
-- shop_id 3 = Bakery House  (owned by user 5 = Emily)
-- shop_id 4 = Fish Market   (owned by user 4 = Michael)
-- shop_id 5 = Delicatessen   (owned by user 2 = John, second shop)
-- =========================================================
INSERT INTO shop VALUES (NULL,'Butcher Shop',  SYSDATE,'Fresh premium meats and cuts',        2);
INSERT INTO shop VALUES (NULL,'Green Grocer',  SYSDATE,'Organic vegetables & exotic fruits',  3);
INSERT INTO shop VALUES (NULL,'Bakery House',  SYSDATE,'Fresh baked artisan goods',           5);
INSERT INTO shop VALUES (NULL,'Fish Market',   SYSDATE,'Fresh seafood and frozen fish',       4);
INSERT INTO shop VALUES (NULL,'Delicatessen',  SYSDATE,'Premium gourmet imported foods',      2);


-- =========================================================
-- DATA: PRODUCT CATEGORY
-- 1=Meat 2=Vegetables 3=Fruits 4=Bakery
-- 5=Seafood 6=Dairy 7=Gourmet 8=Snacks
-- =========================================================
INSERT INTO product_category VALUES (NULL,'Meat');
INSERT INTO product_category VALUES (NULL,'Vegetables');
INSERT INTO product_category VALUES (NULL,'Fruits');
INSERT INTO product_category VALUES (NULL,'Bakery');
INSERT INTO product_category VALUES (NULL,'Seafood');
INSERT INTO product_category VALUES (NULL,'Dairy');
INSERT INTO product_category VALUES (NULL,'Gourmet');
INSERT INTO product_category VALUES (NULL,'Snacks');


-- =========================================================
-- DATA: PRODUCT
-- shop 1 (Butcher): products 1-5   category 1 (Meat)
-- shop 2 (Green):   products 6-10  categories 2,3,6
-- shop 3 (Bakery):  products 11-15 category 4
-- shop 4 (Fish):    products 16-20 category 5
-- shop 5 (Deli):    products 21-25 categories 7,6,1
-- =========================================================

-- Butcher Shop (shop_id=1, category Meat=1)
INSERT INTO product VALUES (NULL,'Whole Chicken',       'Fresh farm chicken',           8.50, 100,1,5,'None',        'chicken.png',   1,1);
INSERT INTO product VALUES (NULL,'Pork Ribs',           'Tender pork ribs',            12.00,  80,1,5,'None',        'ribs.png',      1,1);
INSERT INTO product VALUES (NULL,'Buffalo Meat',        'Lean buffalo meat',           10.00,  90,1,5,'None',        'buffalo.png',   1,1);
INSERT INTO product VALUES (NULL,'Lamb Chops',          'Premium lamb chops',          15.00,  60,1,5,'None',        'lamb.png',      1,1);
INSERT INTO product VALUES (NULL,'Beef Steak',          'Aged beef steak',             18.00,  70,1,5,'None',        'beef.png',      1,1);

-- Green Grocer (shop_id=2)
INSERT INTO product VALUES (NULL,'Bitter Gourd',        'Fresh organic bitter gourd',   2.00, 120,1,10,'None',       'bittergourd.png',  2,2);
INSERT INTO product VALUES (NULL,'Passion Fruit',       'Sweet tropical fruit',         3.50, 100,1,10,'None',       'passionfruit.png', 2,3);
INSERT INTO product VALUES (NULL,'Blueberries',         'Imported fresh blueberries',   5.00,  80,1,10,'None',       'blueberries.png',  2,3);
INSERT INTO product VALUES (NULL,'Aged Gouda',          'Imported cheese block',        7.00,  60,1, 5,'Dairy',      'gouda.png',        2,6);
INSERT INTO product VALUES (NULL,'Green Apple',         'Fresh crisp apples',           2.50, 150,1,10,'None',       'apple.png',        2,3);

-- Bakery House (shop_id=3, category Bakery=4)
INSERT INTO product VALUES (NULL,'Sourdough Loaf',      'Artisan sourdough bread',      4.00,  90,1, 5,'Gluten',     'sourdough.png',  3,4);
INSERT INTO product VALUES (NULL,'Butter Croissant',    'Flaky butter croissant',       2.50, 120,1,10,'Dairy,Gluten','croissant.png', 3,4);
INSERT INTO product VALUES (NULL,'Fruit Cupcake',       'Mixed fruit cupcakes',         3.00,  80,1,10,'Gluten',     'cupcake.png',    3,4);
INSERT INTO product VALUES (NULL,'Cinnamon Roll',       'Sweet cinnamon pastry',        3.50,  70,1, 5,'Gluten',     'cinnamon.png',   3,4);
INSERT INTO product VALUES (NULL,'Chocolate Muffin',    'Rich chocolate muffin',        3.20, 100,1,10,'Gluten',     'muffin.png',     3,4);

-- Fish Market (shop_id=4, category Seafood=5)
INSERT INTO product VALUES (NULL,'Salmon Fish',         'Fresh Atlantic salmon',       15.00,  60,1, 5,'Fish',       'salmon.png',  4,5);
INSERT INTO product VALUES (NULL,'Cod Loin',            'Premium cod fillet',          12.00,  70,1, 5,'Fish',       'cod.png',     4,5);
INSERT INTO product VALUES (NULL,'King Prawns',         'Large prawns',                18.00,  50,1, 5,'Shellfish',  'prawns.png',  4,5);
INSERT INTO product VALUES (NULL,'Smoked Mackerel',     'Smoked seafood fish',         10.00,  80,1, 5,'Fish',       'mackerel.png',4,5);
INSERT INTO product VALUES (NULL,'Tuna Steak',          'Fresh tuna cut',              14.00,  65,1, 5,'Fish',       'tuna.png',    4,5);

-- Delicatessen (shop_id=5)
INSERT INTO product VALUES (NULL,'Truffle Oil',         'Premium Italian truffle oil', 20.00,  40,1, 3,'None',       'truffle.png',    5,7);
INSERT INTO product VALUES (NULL,'Aged Gouda',          'Dutch aged cheese',           12.00,  50,1, 3,'Dairy',      'gouda2.png',     5,6);
INSERT INTO product VALUES (NULL,'Prosciutto di Parma', 'Italian cured ham',           25.00,  30,1, 3,'Meat',       'prosciutto.png', 5,1);
INSERT INTO product VALUES (NULL,'Artisan Olives',      'Imported olives mix',          8.00,  90,1,10,'None',       'olives.png',     5,7);
INSERT INTO product VALUES (NULL,'Brie Cheese',         'Soft French cheese',          10.00,  60,1, 5,'Dairy',      'brie.png',       5,6);


-- =========================================================
-- DATA: COLLECTION SLOT
-- =========================================================
INSERT INTO collection_slot VALUES (NULL,'Wednesday',DATE '2026-08-12','10:00 - 13:00');
INSERT INTO collection_slot VALUES (NULL,'Wednesday',DATE '2026-08-12','14:00 - 17:00');
INSERT INTO collection_slot VALUES (NULL,'Thursday', DATE '2026-08-13','10:00 - 13:00');
INSERT INTO collection_slot VALUES (NULL,'Thursday', DATE '2026-08-13','14:00 - 17:00');
INSERT INTO collection_slot VALUES (NULL,'Friday',   DATE '2026-08-14','09:00 - 12:00');
INSERT INTO collection_slot VALUES (NULL,'Friday',   DATE '2026-08-14','13:00 - 16:00');
INSERT INTO collection_slot VALUES (NULL,'Wednesday',DATE '2026-08-19','10:00 - 13:00');
INSERT INTO collection_slot VALUES (NULL,'Thursday', DATE '2026-08-20','14:00 - 17:00');


-- =========================================================
-- DATA: COUPON
-- =========================================================
INSERT INTO coupon VALUES (NULL,'WELCOME10', '10% off for new customers',       DATE '2026-08-01',DATE '2026-12-31',10);
INSERT INTO coupon VALUES (NULL,'MEAT15',    '15% discount on meat products',   DATE '2026-08-01',DATE '2026-09-01',15);
INSERT INTO coupon VALUES (NULL,'FRESH5',    '5% off on fresh groceries',       DATE '2026-08-05',DATE '2026-09-05', 5);
INSERT INTO coupon VALUES (NULL,'SEAFOOD12', '12% seafood special offer',       DATE '2026-08-07',DATE '2026-08-30',12);
INSERT INTO coupon VALUES (NULL,'BAKERY8',   '8% bakery promotion',             DATE '2026-08-10',DATE '2026-09-10', 8);
INSERT INTO coupon VALUES (NULL,'DAIRY6',    '6% dairy product offer',          DATE '2026-08-01',DATE '2026-08-31', 6);
INSERT INTO coupon VALUES (NULL,'GOURMET20', '20% gourmet collection offer',    DATE '2026-08-15',DATE '2026-09-15',20);
INSERT INTO coupon VALUES (NULL,'WEEKEND5',  'Weekend shopping discount',       DATE '2026-08-01',DATE '2026-12-31', 5);


-- =========================================================
-- DATA: CART  (user 6=David, user 7=Lily)
-- cart_id 1-8 assigned by sequence
-- =========================================================
INSERT INTO cart VALUES (NULL,6,SYSDATE);
INSERT INTO cart VALUES (NULL,7,SYSDATE);
INSERT INTO cart VALUES (NULL,6,SYSDATE);
INSERT INTO cart VALUES (NULL,7,SYSDATE);
INSERT INTO cart VALUES (NULL,6,SYSDATE);
INSERT INTO cart VALUES (NULL,7,SYSDATE);
INSERT INTO cart VALUES (NULL,6,SYSDATE);
INSERT INTO cart VALUES (NULL,7,SYSDATE);


-- =========================================================
-- DATA: CART PRODUCT
-- =========================================================
INSERT INTO cart_product VALUES (1, 1, 2);
INSERT INTO cart_product VALUES (1, 6, 1);
INSERT INTO cart_product VALUES (2,11, 3);
INSERT INTO cart_product VALUES (2,14, 1);
INSERT INTO cart_product VALUES (3, 3, 2);
INSERT INTO cart_product VALUES (3, 4, 1);
INSERT INTO cart_product VALUES (4,16, 2);
INSERT INTO cart_product VALUES (4,18, 1);
INSERT INTO cart_product VALUES (5,21, 1);
INSERT INTO cart_product VALUES (5,23, 2);
INSERT INTO cart_product VALUES (6, 8, 2);
INSERT INTO cart_product VALUES (6,10, 1);
INSERT INTO cart_product VALUES (7,12, 2);
INSERT INTO cart_product VALUES (7,20, 1);
INSERT INTO cart_product VALUES (8,24, 1);
INSERT INTO cart_product VALUES (8,25, 2);


-- =========================================================
-- DATA: FAVOURITE
-- =========================================================
INSERT INTO favourite VALUES (NULL,6);
INSERT INTO favourite VALUES (NULL,7);
INSERT INTO favourite VALUES (NULL,6);
INSERT INTO favourite VALUES (NULL,7);
INSERT INTO favourite VALUES (NULL,6);
INSERT INTO favourite VALUES (NULL,7);
INSERT INTO favourite VALUES (NULL,6);
INSERT INTO favourite VALUES (NULL,7);


-- =========================================================
-- DATA: FAVOURITE PRODUCT
-- =========================================================
INSERT INTO favourite_product VALUES (1, 5,SYSDATE);
INSERT INTO favourite_product VALUES (1, 6,SYSDATE);
INSERT INTO favourite_product VALUES (2,11,SYSDATE);
INSERT INTO favourite_product VALUES (2,14,SYSDATE);
INSERT INTO favourite_product VALUES (3,18,SYSDATE);
INSERT INTO favourite_product VALUES (3,16,SYSDATE);
INSERT INTO favourite_product VALUES (4,21,SYSDATE);
INSERT INTO favourite_product VALUES (4,25,SYSDATE);
INSERT INTO favourite_product VALUES (5, 1,SYSDATE);
INSERT INTO favourite_product VALUES (5, 3,SYSDATE);
INSERT INTO favourite_product VALUES (6,12,SYSDATE);
INSERT INTO favourite_product VALUES (6,24,SYSDATE);
INSERT INTO favourite_product VALUES (7, 7,SYSDATE);
INSERT INTO favourite_product VALUES (7,20,SYSDATE);
INSERT INTO favourite_product VALUES (8,22,SYSDATE);
INSERT INTO favourite_product VALUES (8, 9,SYSDATE);


-- =========================================================
-- DATA: ORDERS
-- user 6=David, user 7=Lily
-- collection_slot_id 1-8, coupon_id 1-8
-- =========================================================
INSERT INTO orders VALUES (NULL,6,'COMPLETED', 32.50,DATE '2026-08-12',1,1);
INSERT INTO orders VALUES (NULL,7,'PENDING',   24.00,DATE '2026-08-12',2,3);
INSERT INTO orders VALUES (NULL,6,'PROCESSING',18.00,DATE '2026-08-13',3,2);
INSERT INTO orders VALUES (NULL,7,'COMPLETED', 41.00,DATE '2026-08-13',4,4);
INSERT INTO orders VALUES (NULL,6,'PENDING',   27.50,DATE '2026-08-14',5,5);
INSERT INTO orders VALUES (NULL,7,'COMPLETED', 55.00,DATE '2026-08-14',6,7);
INSERT INTO orders VALUES (NULL,6,'PROCESSING',20.00,DATE '2026-08-19',7,6);
INSERT INTO orders VALUES (NULL,7,'COMPLETED', 48.00,DATE '2026-08-20',8,8);


-- =========================================================
-- DATA: ORDER PRODUCT
-- =========================================================
INSERT INTO order_product VALUES (1, 1,2, 8.50);
INSERT INTO order_product VALUES (1, 5,1,18.00);
INSERT INTO order_product VALUES (2,11,3, 5.00);
INSERT INTO order_product VALUES (3, 6,1, 2.00);
INSERT INTO order_product VALUES (3, 9,2, 7.00);
INSERT INTO order_product VALUES (4,16,2,15.00);
INSERT INTO order_product VALUES (5,18,1,10.00);
INSERT INTO order_product VALUES (5,20,1,14.00);
INSERT INTO order_product VALUES (6,21,1,20.00);
INSERT INTO order_product VALUES (6,23,1,25.00);
INSERT INTO order_product VALUES (7, 7,2, 3.50);
INSERT INTO order_product VALUES (8,24,2, 8.00);
INSERT INTO order_product VALUES (8,25,1,10.00);


-- =========================================================
-- DATA: DISCOUNT
-- =========================================================
INSERT INTO discount VALUES (NULL,10,DATE '2026-08-01',DATE '2026-08-31', 1);
INSERT INTO discount VALUES (NULL,15,DATE '2026-08-01',DATE '2026-09-01', 2);
INSERT INTO discount VALUES (NULL, 5,DATE '2026-08-05',DATE '2026-08-25', 6);
INSERT INTO discount VALUES (NULL, 8,DATE '2026-08-01',DATE '2026-08-30',11);
INSERT INTO discount VALUES (NULL,12,DATE '2026-08-10',DATE '2026-09-10',16);
INSERT INTO discount VALUES (NULL,20,DATE '2026-08-15',DATE '2026-09-15',21);
INSERT INTO discount VALUES (NULL, 7,DATE '2026-08-01',DATE '2026-08-20',24);
INSERT INTO discount VALUES (NULL, 6,DATE '2026-08-05',DATE '2026-08-25',25);


-- =========================================================
-- DATA: REVIEW
-- =========================================================
INSERT INTO review VALUES (NULL,'Fresh and premium quality meat',      5,6, 1,SYSDATE);
INSERT INTO review VALUES (NULL,'The lamb chops were excellent',       5,7, 4,SYSDATE);
INSERT INTO review VALUES (NULL,'Very fresh vegetables delivered',     4,6, 6,SYSDATE);
INSERT INTO review VALUES (NULL,'Blueberries tasted amazing',          5,7, 8,SYSDATE);
INSERT INTO review VALUES (NULL,'Croissants were buttery and soft',    5,6,12,SYSDATE);
INSERT INTO review VALUES (NULL,'Fresh seafood and fast delivery',     4,7,16,SYSDATE);
INSERT INTO review VALUES (NULL,'Premium gourmet products',            5,6,21,SYSDATE);
INSERT INTO review VALUES (NULL,'Brie cheese quality was excellent',   5,7,25,SYSDATE);


-- =========================================================
-- DATA: PAYMENT
-- =========================================================
INSERT INTO payment VALUES (NULL,SYSDATE,32.50,'CARD', 6,1);
INSERT INTO payment VALUES (NULL,SYSDATE,24.00,'ESEWA',7,2);
INSERT INTO payment VALUES (NULL,SYSDATE,18.00,'CARD', 6,3);
INSERT INTO payment VALUES (NULL,SYSDATE,41.00,'CASH', 7,4);
INSERT INTO payment VALUES (NULL,SYSDATE,27.50,'CARD', 6,5);
INSERT INTO payment VALUES (NULL,SYSDATE,55.00,'ESEWA',7,6);
INSERT INTO payment VALUES (NULL,SYSDATE,20.00,'CARD', 6,7);
INSERT INTO payment VALUES (NULL,SYSDATE,48.00,'CASH', 7,8);


-- =========================================================
-- DATA: REPORT  (created by admin user_id=1)
-- =========================================================
INSERT INTO report VALUES (NULL,'Sales',      'Weekly Sales Report',          SYSDATE,1,1);
INSERT INTO report VALUES (NULL,'Inventory',  'Inventory Status Report',      SYSDATE,1,2);
INSERT INTO report VALUES (NULL,'Customer',   'Customer Purchase Report',     SYSDATE,1,3);
INSERT INTO report VALUES (NULL,'Finance',    'Payment Summary Report',       SYSDATE,1,4);
INSERT INTO report VALUES (NULL,'Orders',     'Pending Orders Report',        SYSDATE,1,5);
INSERT INTO report VALUES (NULL,'Delivery',   'Collection Slot Report',       SYSDATE,1,6);
INSERT INTO report VALUES (NULL,'System',     'Marketplace Activity Report',  SYSDATE,1,7);
INSERT INTO report VALUES (NULL,'Performance','Trader Performance Report',    SYSDATE,1,8);


-- =========================================================
-- DATA: PRODUCT REPORT
-- =========================================================
INSERT INTO product_report VALUES ( 1,1);
INSERT INTO product_report VALUES ( 6,2);
INSERT INTO product_report VALUES (12,3);
INSERT INTO product_report VALUES (16,4);
INSERT INTO product_report VALUES (21,5);
INSERT INTO product_report VALUES (24,6);
INSERT INTO product_report VALUES ( 4,7);
INSERT INTO product_report VALUES (25,8);

COMMIT;

SELECT * FROM users;
SELECT * FROM shop;
SELECT * FROM product;
SELECT * FROM orders;

ALTER TABLE product ADD quantity NUMBER;
ALTER TABLE review ADD review_title VARCHAR2(100);

-- Contact messages
DROP TABLE contact_message CASCADE CONSTRAINTS;
DROP SEQUENCE contact_message_seq;

CREATE TABLE contact_message (
  message_id     NUMBER PRIMARY KEY,
  first_name     VARCHAR2(100) NOT NULL,
  last_name      VARCHAR2(100),
  email          VARCHAR2(200) NOT NULL,
  contact_number VARCHAR2(30),
  subject        VARCHAR2(50),
  message        VARCHAR2(2000) NOT NULL,
  is_read        NUMBER(1) DEFAULT 0,
  sent_date      DATE DEFAULT SYSDATE
);

CREATE SEQUENCE contact_message_seq START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER contact_message_trg
BEFORE INSERT ON contact_message
FOR EACH ROW
BEGIN
  IF :NEW.message_id IS NULL THEN
    SELECT contact_message_seq.NEXTVAL INTO :NEW.message_id FROM dual;
  END IF;
END;
/