# Lamia Order API

### Usage
- One endpoint (POST /), takes JSON input.
    - param: `products` - JSON array of products (**required**)
    - param: `country`- string (**required**)
    - param: `format` - string, either "json" or "html" (**required**)
    - param: `to_email` - boolean, if set to true then email_address is required (**required**)
    - param: `email_address` - string (**optional**)

**NB!** *products* require "id" and "quantity" values for each entry. 

### Example parameters
```json
{
	"products": [
		{
			"id": "1",
			"quantity": "1"
		},
		{
			"id": "3",
			"quantity": "2"
		},
		{
			"id": "2",
			"quantity": "4"
		}
	],
	"country": "Poland",
	"format": "json",
	"to_email": "false"
}
```

```json
{
	"products": [
		{
			"id": "3",
			"quantity": "25"
		},
		{
			"id": "1",
			"quantity": "11"
		},
		{
			"id": "4",
			"quantity": "1"
		}
	],
	"country": "Sweden",
	"format": "html",
	"to_email": "true",
	"email_address": "agile@null.net"
}
```

### Dummy data

```SQL
mysql> select * from stock;
+------------+----------------+---------------+------------------------------+------------------+
| product_id | product_title  | product_price | product_description          | product_quantity |
+------------+----------------+---------------+------------------------------+------------------+
|          1 | Banana         |           2.5 | A tasty snack.               |             9000 |
|         11 | Cactus extract |            30 | Healthy, but lacks in taste. |              500 |
|         21 | Milk           |           1.7 | Cow juice!                   |             1000 |
|         31 | Almonds        |           2.8 | Good for you.                |              583 |
|         41 | Pepsi Max      |           1.2 | Zero in a can.               |               30 |
|         51 | Spice          |           8.5 | No one know what it is.      |              502 |
|         61 | L-carnitine    |            20 | Amino acid?                  |              120 |
+------------+----------------+---------------+------------------------------+------------------+
7 rows in set (0,06 sec)

```

```SQL
mysql> select * from tax_base;
+------------+--------------+------+
| country_id | country_name | tax  |
+------------+--------------+------+
|          1 | Finland      |  0.2 |
|         11 | Poland       | 0.07 |
|         21 | Sweden       | 0.75 |
|         31 | Netherlands  | 0.17 |
|         41 | Norway       | 0.25 |
|         51 | Croatia      | 0.02 |
+------------+--------------+------+
6 rows in set (0,06 sec)

```

**NB!** every index is incremented by 10 due to an unconfigured ClearDB variable `auto_increment_increment = 10`.

### Tech

##### FlightPHP
##### Heroku
##### ClearDB MySQL
##### PHP 7.x