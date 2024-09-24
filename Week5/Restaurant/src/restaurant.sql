CREATE TABLE IF NOT EXISTS `company` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`name`	TEXT NOT NULL,
	`description`	TEXT NOT NULL,
	`logo_path`	VARCHAR(255) NOT NULL,
	`deleted_at`	TEXT,
	PRIMARY KEY(`id`)
);

INSERT INTO `company` VALUES (1,'Global Foods Inc.','A multinational food service company delivering quality food products worldwide.','images\logos\Globals_Food_Inc.png',NULL);
INSERT INTO `company` VALUES (2,'Urban Eats Group','A network of restaurants and cafes focused on urban dining experiences.','images\logos\Urba_Eats.png',NULL);
INSERT INTO `company` VALUES (3,'Green Plate Ventures','A sustainable food company providing organic and eco-friendly meals.','images\logos\Green_Plate.png',NULL);
INSERT INTO `company` VALUES (4,'Epicurean Enterprises','Luxury dining services and restaurants with exclusive menus.','images\logos\Epiciurean.png',NULL);
INSERT INTO `company` VALUES (5,'Street Bites Co.','Fast casual dining focused on street food-inspired dishes.','images\logos\Street_Bites.png',NULL);

CREATE TABLE IF NOT EXISTS `users` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`company_id`	INTEGER,
	`role`	TEXT NOT NULL,
	`name`	TEXT NOT NULL,
	`surname`	TEXT NOT NULL,
	`username`	TEXT NOT NULL,
	`passwd`	TEXT NOT NULL,
	`balance`	INTEGER NOT NULL DEFAULT 5000,
	`created_at`	TEXT NOT NULL,
	`deleted_at`	TEXT,
	`pfp_path`	VARCHAR(255) DEFAULT 'images\profilePic\pfp.jpg',
	PRIMARY KEY(`id`),
	FOREIGN KEY(`company_id`) REFERENCES `company`(`id`)
);

INSERT INTO `users` VALUES (1,NULL,'admin','admin','admin','admin','$argon2id$v=19$m=65536,t=4,p=1$SkMyeVVTMHVyR1VCOGY2eg$47ALPW+Bx6KyFxLKRV6cvOLr2uTkuOEIOpGnqL3SDUI',0,'9/10/2024',NULL,'images\profilePic\pfp.jpg');
INSERT INTO `users` VALUES (2,1,'company','company','company','company','$argon2id$v=19$m=65536,t=4,p=1$Ny5QYTlJOC84dEx1M25UeA$IWIJgFFK0I5AMcB7J9J8151y4HFX3Meq7yNgeWHGRac',4899,'9/10/2024',NULL,'images\profilePic\pfp.jpg');
INSERT INTO `users` VALUES (3,NULL,'user','user','userasdsad','user','$argon2id$v=19$m=65536,t=4,p=1$OUJwUkJqcnZoeGJwc05QTw$EwdBUiq/6lnKARC/0ELKm1/M+bh3Knj/GsgTiW8OyLw',5152,'9/10/2024',NULL,'images/profilePic/3025994616390042300_52571758781.jpg');
INSERT INTO `users` VALUES (4,NULL,'silinmiş','silinmiş','silinmiş','silinmiş','silinmiş',5000,'9/10/2024','9/10/2024','images\profilePic\pfp.jpg');
INSERT INTO `users` VALUES (5,NULL,'silinmiş','silinmiş','silinmiş','silinmiş','silinmiş',5000,'9/10/2024','11/09/2024','images\profilePic\pfp.jpg');


CREATE TABLE IF NOT EXISTS `restaurant` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`company_id`	INTEGER NOT NULL,
	`name`	TEXT NOT NULL,
	`description`	TEXT NOT NULL,
	`imape_path`	VARCHAR(255) NOT NULL,
	`created_at`	TEXT NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`company_id`) REFERENCES `company`(`id`)
);

INSERT INTO `restaurant` VALUES (1,1,'Sushi Place','Authentic Japanese sushi with fresh ingredients.','/images/sushi_place.jpg','2023-09-01');
INSERT INTO `restaurant` VALUES (2,1,'Pasta Palace','Cozy spot with homemade pasta dishes and Italian wine.','/images/pasta_palace.jpg','2023-09-05');
INSERT INTO `restaurant` VALUES (3,2,'Burger Haven','Gourmet burgers with a variety of toppings and hand-cut fries.','/images/burger_haven.jpg','2023-09-10');
INSERT INTO `restaurant` VALUES (4,2,'Taco Town','Delicious Mexican street food with homemade salsas.','/images/taco_town.jpg','2023-09-12');
INSERT INTO `restaurant` VALUES (5,3,'Pizza Corner','New York-style pizza with creative toppings and great service.','/images/pizza_corner.jpg','2023-09-15');
INSERT INTO `restaurant` VALUES (6,3,'Vegan Delights','A wide range of delicious plant-based meals.','/images/vegan_delights.jpg','2023-09-18');

CREATE TABLE IF NOT EXISTS `food` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`restaurant_id`	INTEGER NOT NULL,
	`name`	TEXT NOT NULL,
	`description`	TEXT NOT NULL,
	`image_path`	VARCHAR(255),
	`price`	INTEGER NOT NULL,
	`created_at`	TEXT NOT NULL,
	`deleted_at`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`restaurant_id`) REFERENCES `restaurant`(`id`)
);

INSERT INTO `food` VALUES (1,1,'California Roll','Crab, avocado, cucumber, and sesame seeds rolled in rice and seaweed.','images\foods\Orange.jpg',12,'2023-09-05',NULL);
INSERT INTO `food` VALUES (2,1,'Spicy Tuna Roll','Tuna, cucumber, and spicy mayo, rolled in rice and seaweed.','images\foods\Orange.jpg',14,'2023-09-06',NULL);
INSERT INTO `food` VALUES (3,2,'Classic Margherita','Fresh mozzarella, tomato, and basil on a thin crust.','images\foods\Orange.jpg',16,'2023-09-08',NULL);
INSERT INTO `food` VALUES (4,2,'Penne Alfredo','Penne pasta tossed in a creamy Alfredo sauce.','images\foods\Orange.jpg',18,'2023-09-10',NULL);
INSERT INTO `food` VALUES (5,3,'Beef Burger','Juicy beef patty with lettuce, tomato, and house sauce.','images\foods\Orange.jpg',12,'2023-09-12',NULL);
INSERT INTO `food` VALUES (6,3,'Chicken Tacos','Grilled chicken, pico de gallo, and avocado sauce in soft tortillas.','images\foods\Orange.jpg',10,'2023-09-15',NULL);
INSERT INTO `food` VALUES (7,4,'Grilled Salmon','Perfectly grilled salmon served with lemon butter sauce.','images\foods\Orange.jpg',25,'2023-09-18',NULL);
INSERT INTO `food` VALUES (8,4,'Ribeye Steak','Premium ribeye steak cooked to perfection.','images\foods\Orange.jpg',30,'2023-09-20',NULL);
INSERT INTO `food` VALUES (9,5,'Vegetarian Ramen','Ramen noodles in a flavorful vegetable broth with tofu and greens.','images\foods\Orange.jpg',14,'2023-09-22',NULL);
INSERT INTO `food` VALUES (10,5,'Chocolate Cake','Rich and moist chocolate cake with a creamy chocolate frosting.','images\foods\Orange.jpg',8,'2023-09-25',NULL);

CREATE TABLE IF NOT EXISTS `basket` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id`	INTEGER NOT NULL,
	`food_id`	INTEGER NOT NULL,
	`note`	TEXT NOT NULL,
	`quantity`	INTEGER DEFAULT 1,
	`created_at`	TEXT NOT NULL,
	`deleted_at`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`food_id`) REFERENCES `food`(`id`),
	FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
);

CREATE TABLE IF NOT EXISTS `comments` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id`	INTEGER NOT NULL,
	`restaurant_id`	INTEGER NOT NULL,
	`title`	TEXT NOT NULL,
	`description`	TEXT NOT NULL,
	`score`	INTEGER NOT NULL,
	`created_at`	TEXT NOT NULL,
	`deleted_at`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`restaurant_id`) REFERENCES `restaurant`(`id`),
	FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
);

INSERT INTO `comments` VALUES (1,3,1,'Test','Test',5,'18/09/2024',NULL);
INSERT INTO `comments` VALUES (2,1,1,'qweadas','asdweasd',5,'18/09/2024',NULL);
INSERT INTO `comments` VALUES (3,1,1,'asdasd','asdawqewqeqw',10,'18/09/2024',NULL);
INSERT INTO `comments` VALUES (4,1,1,'asdawd','21324234',9,'18/09/2024',NULL);
INSERT INTO `comments` VALUES (5,1,2,'qweda','waedsadwa',7,'18/09/2024',NULL);

CREATE TABLE IF NOT EXISTS `coupon` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`restaurant_id`	INTEGER NOT NULL,
	`name`	TEXT NOT NULL,
	`discount`	INTEGER NOT NULL,
	`created_at`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`restaurant_id`) REFERENCES `restaurant`(`id`)
);

INSERT INTO `coupon` VALUES (1,1,'INITIAL',10,'12/09/2024');
INSERT INTO `coupon` VALUES (3,6,'sadsad',5,'12/09/2024');

CREATE TABLE IF NOT EXISTS `order` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`user_id`	INTEGER NOT NULL,
	`order_status`	VARCHAR(255) DEFAULT 'Hazırlanıyor',
	`total_price`	INTEGER NOT NULL,
	`created_at`	TEXT NOT NULL,
	`note`	TEXT,
	`deleted_at`	TEXT,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`user_id`) REFERENCES `users`(`id`)
);

INSERT INTO `order` VALUES (45,2,'Teslim Edildi',12,'19','sadasda','20/09/2024');
INSERT INTO `order` VALUES (46,2,'Hazırlanıyor',25,'19','asdasd',NULL);
INSERT INTO `order` VALUES (47,2,'Teslim Edildi',14,'19','sdfdsdasd','20/09/2024');
INSERT INTO `order` VALUES (48,2,'Teslim Edildi',12,'19','asdasdw2134','20/09/2024');
INSERT INTO `order` VALUES (49,3,'Teslim Edildi',12,'20','','20/09/2024');

CREATE TABLE IF NOT EXISTS `order_items` (
	`id`	INT NOT NULL AUTO_INCREMENT UNIQUE,
	`food_id`	INTEGER NOT NULL,
	`order_id`	INTEGER NOT NULL,
	`quantity`	INTEGER NOT NULL,
	`price`	INTEGER NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`food_id`) REFERENCES `food`(`id`),
	FOREIGN KEY(`order_id`) REFERENCES `order`(`id`)
);

INSERT INTO `order_items` VALUES (47,1,45,1,12);
INSERT INTO `order_items` VALUES (48,7,46,1,25);
INSERT INTO `order_items` VALUES (49,2,47,1,14);
INSERT INTO `order_items` VALUES (50,1,48,1,12);
INSERT INTO `order_items` VALUES (51,1,49,1,12);

COMMIT;