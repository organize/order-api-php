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

### Tech

##### FlightPHP
##### Heroku
##### ClearDB MySQL
##### PHP 7.x