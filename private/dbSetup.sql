mysql -h wwwstu.csci.viu.ca -p
create database csci375b_project;

/*using database*/
mysql -h wwwstu.csci.viu.ca -p
37d35j2h
use csci375b_project;
/*drop table*/
drop table Cook;
drop table Cashier;
drop table orders;
drop table menuItems;
drop table orderItems;
/*create 5 tables*/
Create table Account(
	Account_ID int not null auto_increment primary key,
	userid varchar(40) not null,
	password varchar(256) not null,
	UserName varchar(40) not null, 
	email varchar(40) not null
);

Create table Cook(
	Cook_ID int not null auto_increment primary key,
	userid varchar(40) not null,
	password varchar(256) not null,
	UserName varchar(40) not null, 
	email varchar(40) not null
);

Create table Cashier(
	Cashier_ID int not null auto_increment primary key,
	userid varchar(40) not null,
	password varchar(256) not null,
	UserName varchar(40) not null, 
	email varchar(40) not null
);

Create table orders (
    order_ID int not null auto_increment primary key,
    price decimal(8,2) NOT NULL DEFAULT 0,
    payment int DEFAULT 0,
    status varchar(50) NOT NULL DEFAULT 'Waiting for payment',
    time datetime,
    pickup datetime,
	Cook_ID int REFERENCES Cook(Cook_ID),
    Account_ID int REFERENCES Account(Account_ID)
);

Create table menuItems (
    menu_ID int not null auto_increment primary key,
    name varchar(255),
    price decimal(8,2) NOT NULL DEFAULT 0,
    photoPath varchar(255),
    description varchar(255),
    quantity int NOT NULL
);

Create table orderItems (
    order_ID int REFERENCES orders(order_ID),
    menu_ID int REFERENCES menuItems(menu_ID),
    quantity int NOT NULL,
    primary key (order_ID, menu_ID)
);

/*insert data using csv file*/
LOAD DATA LOCAL INFILE './public_html/project/private/orders.csv'
INTO TABLE orders
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE './public_html/project/private/menuItems.csv'
INTO TABLE menuItems
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

LOAD DATA LOCAL INFILE './public_html/project/private/orderItems.csv'
INTO TABLE orderItems
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n';

/*show data*/
select * from Account;
select * from Cook;
select * from Cashier;
select * from orders;
select * from menuItems;
select * from orderItems;

/*Account stuff*/
select order_ID, price from orders where Account_ID = 2 order by time asc;

select orderItems.menu_ID, menuItems.name, orderItems.quantity
from orderItems left join menuItems on orderItems.menu_ID = menuItems.menu_ID
where order_ID = 1;

/*cook stuff*/
select order_ID from orders where payment=1 and Cook_ID = 0 order by time asc;

select orderItems.menu_ID, menuItems.name, orderItems.quantity
from orderItems left join menuItems on orderItems.menu_ID = menuItems.menu_ID
where order_ID = 1;

update orders set status='Ready', Cook_ID = 1 where order_ID = 1;

update orders set payment = 1 where payment = 0;
update orders set Cook_ID = 0 where Cook_ID = 1;
update orders set status='in progress' where status='Ready';
/*cashier stuff*/
select DATE(time) as Day, SUM(price) as Total from orders 
where payment=1 and DATE(time) <= '2019-04-02' or DATE(time) <= '2019-04-03'
group by DATE(time);

select SUM(price) from orders where payment=1 and DATE(time) <= '2019-04-02' or DATE(time) <= '2019-04-03';

select price, UserName, time from orders natural join Account where payment=1 and DATE(time) = '2019-04-03';

select SUM(price) from orders natural join Account where payment=1 and DATE(time) = '2019-04-03';




