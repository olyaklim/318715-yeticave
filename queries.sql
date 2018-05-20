USE yeticave;

INSERT INTO categories (name)
VALUES ('Доски и лыжи'),
       ('Крепления') ,
       ('Ботинки'),
       ('Одежда'),
       ('Инструменты'),
       ('Разное');


INSERT INTO lots (dt_add, name, url_pictures, price, dt_end, price_step, author_id, category_id, description)
VALUES ('2018-04-01 13:10:05', '2018 Rossignol Snowboard',    'img/lot-1.jpg', '21000', '2018-05-15 12:00:00', '100', '1', '2', 'The Rossignol District Amptek Snowboard is an user-friendly freestyle board for the aspiring park and pipe riders.'),
       ('2018-04-01 01:22:17', '2018 Rossignol-B Snowboard',  'img/lot-2.jpg', '22000', '2018-05-20 18:00:00', '200', '2', '2', 'User-friendly freestyle board for the aspiring park'),
       ('2018-04-01 02:27:10', '2018 Rossignol-C Snowboard',  'img/lot-3.jpg', '2000', '2018-05-30 15:00:00', '300', '3', '2', 'Freestyle board'),
       ('2018-04-01 03:32:19', '2018 Rossignol-C Snowboard',  'img/lot-4.jpg', '950', '2018-06-05 13:00:00', '100', '2', '2', 'Freestyle board'),
       ('2018-04-01 04:45:11', '2018 Rossignol-C Snowboard',  'img/lot-5.jpg', '4900', '2018-06-10 12:00:00', '100', '2', '2', 'Freestyle board'),
       ('2018-03-01 13:10:05', '2011 Snowboard wwwwwwwwwww',  'img/lot-1.jpg', '21000', '2018-04-15 12:00:00', '100', '1', '2', 'board old 1'),
       ('2018-03-01 01:22:17', '2011 Snowboard eeeeeeeeeee',  'img/lot-2.jpg', '22000', '2018-04-20 18:00:00', '200', '2', '2', 'board old 2'),
       ('2018-04-01 05:02:19', '2018 Footwear Snowboard',     'img/lot-6.jpg', '30500', '2018-06-11 10:00:00', '500', '3', '4', 'Footwear for board'),

       ('2018-05-09 15:22:17', '2014 Rossignol District Snowboard',  'img/lot-1.jpg', '10999', '2018-10-20 18:00:00', '200', '2', '2', 'board old 2'),
       ('2018-05-09 14:22:17', 'DC Ply Mens 2016/2017 Snowboard',  'img/lot-2.jpg', '159999', '2018-06-11 18:00:00', '200', '2', '2', 'board old 2'),
       ('2018-05-09 13:22:17', 'Крепления Union Contact Pro 2015 года размер L/XL',  'img/lot-3.jpg', '8000', '2018-06-20 18:00:00', '200', '2', '2', 'board old 2'),
       ('2018-05-09 12:22:17', 'Ботинки для сноуборда DC Mutiny Charocal',  'img/lot-4.jpg', '10999', '2018-06-10 18:00:00', '200', '2', '2', 'board old 2'),
       ('2018-05-08 11:22:17', 'Куртка для сноуборда DC Mutiny Charocal',  'img/lot-5.jpg', '7500', '2018-07-25 18:00:00', '200', '2', '2', 'board old 2'),
       ('2018-05-07 10:22:17', 'Маска Oakley Canopy',  'img/lot-6.jpg', '5400', '2018-06-11 18:00:00', '200', '2', '2', 'board old 2');


INSERT INTO rates (dt_registration, price_user, user_id, lot_id)
VALUES ('2018-05-01 14:00:15', '21100', '5', '1'),
       ('2018-05-01 14:00:15', '21200', '4', '1'),
       ('2018-05-01 15:30:10', '21200', '4', '1'),
       ('2018-09-01 01:32:55', '22200', '6', '2');


INSERT INTO users (email, name, password_user, avatar_path, user_contact)
VALUES ('igor@mail.ru', 'igor', 'igor1990', 'img/igor1990.jpg', '097-55-11-235'),
       ('viktor@mail.ru', 'viktor', 'viktor1990', 'img/viktor1990.jpg', '097-55-22-235'),
       ('anna@mail.ru', 'anna', 'anna1990', 'img/anna1987.jpg', '097-55-33-235'),
       ('ira@mail.ru', 'ira', 'ira1990', 'img/ira1989.jpg', '097-55-33-235'),
       ('ivan@mail.ru', 'ivan', 'ivan1990', 'img/ivan1991.jpg', '097-55-33-777'),
       ('katya@mail.ru', 'katya', 'katya1990', 'img/katya1988.jpg', '097-77-00-277');




-- 1) получить все категории;
SELECT * FROM categories;

-- 2) получить самые новые, открытые лоты.
-- Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, количество ставок, название категории;

SELECT l.name, l.price, l.url_pictures, r.price_user, c.name as category FROM lots l
JOIN categories c
ON l.category_id = c.id
LEFT JOIN rates r
ON (l.id = r.lot_id and ((l.id, r.price_user) IN
  (SELECT r.lot_id, MAX(r.price_user)
  FROM rates r
  GROUP BY r.lot_id)))
WHERE NOW() < l.dt_end
ORDER BY l.dt_add DESC;

-- количество ставок

SELECT lot_id, COUNT(price_user) FROM rates r
  GROUP BY lot_id;


-- 3) показать лот по его id.
-- Получите также название категории, к которой принадлежит лот

SELECT l.name, l.price, c.name as category FROM lots l
JOIN categories c
ON l.category_id = c.id
WHERE l.id = '1';


-- 4) обновить название лота по его идентификатору;

UPDATE lots SET name = '2018-A Rossignol District Snowboard'
WHERE id = '1';

-- 5) получить список самых свежих ставок для лота по его идентификатору;

SELECT * FROM rates
WHERE lot_id = '1'
ORDER BY dt_registration DESC;
