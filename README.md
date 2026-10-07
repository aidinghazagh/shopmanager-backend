# Shop Manager API

A Laravel 11 REST API for running a small shop: products and stock, customers, orders, and payments made in installments. It is the backend for the [Shop Manager Flutter app](https://github.com/aidinghazagh/shopmanager-frontend).

Each shop is an account: the `shops` table is the model you log in as. Every product, customer and order belongs to exactly one shop.

## How it works

### Orders keep the prices they were sold at

`OrderController::store` does more than link an order to product IDs. For each line it copies the product's current values into the `order_products` row:

| Column | Copied from |
| --- | --- |
| `name_on_created` | `products.name` |
| `price_on_created` | `products.price` |
| `purchase_price_on_created` | `products.purchase_price` |
| `quantity` | the request |

Renaming a product or changing its prices later leaves existing orders as they were. An old order's revenue (`price_on_created × quantity`) and profit (`(price_on_created − purchase_price_on_created) × quantity`) are still the figures from the day of the sale.

Deleting a product does change history. `order_products.product_id` cascades on delete, so the product's lines are removed from past orders too.

### One transaction per order

The order row, all of its lines and the optional first payment (`paid`) are written inside a single database transaction. If a product ID in the request doesn't belong to the shop, or any insert fails, everything is rolled back. You never end up with an order missing some of its lines, or a payment with no order.

### Partial payments

An order can have any number of rows in `payments`. Send `paid` when you create the order to record the first one, then add more with `POST /api/order/{order}/payment`. The API stores payments but doesn't check them against the order total, so the client works out the remaining balance.

### Editing orders

Orders can't be edited at the moment. `Route::resource` registers `PUT/PATCH /api/order/{order}`, but `OrderController::update` is commented out, so those requests return an error. The commented-out version refused to edit any order that already had a payment: it checked `Order::isLocked()` and returned the `locked_order` message ("This order has payments and cannot be edited."). Both are still in the code. To change an order today, delete it and create it again.

### Product history

Two tables record what happens to a product:

- **`product_logs`**: when `ProductController::update` changes `name`, `price` or `purchase_price`, it writes one row per changed field with the old and new value.
- **`product_inventory_logs`**: the product doesn't store a stock count. Each change is a signed `quantity_change` row: the starting stock when the product is created, then the difference between the old and new count on each update. The product's `inventory` accessor adds those rows up.

Product responses (other than the dropdown) include both histories as `logs` and `inventory_logs`, newest first. A product update and its log rows are written in one transaction.

Creating an order doesn't write to `product_inventory_logs`, so selling a product doesn't lower its stock. Stock changes when the product is edited.

### Subscriptions

Each shop has a `valid_until` date. The `CheckShopValidity` middleware wraps every product, customer, order and payment route. Once that date has passed, those routes return `shop_expired` ("Please renew your shop subscription"). Login, logout and the `/api/shop` routes are outside the middleware, so an expired shop can still sign in and see when it expired.

There's no sign-up or renewal endpoint. You create shops and set `valid_until` directly in the database (see [Create a shop](#create-a-shop)).

### English and Farsi errors

`App\Models\ErrorMessages` holds every error string in English (`en`) and Farsi (`fa`). It also holds Farsi names for request fields, so a validation message reads `نام الزامی است` rather than `name is required`. The language comes from the shop's `language` column, which is set from `default_lang` on every login. Before the shop is known (a failed login or a missing token), the API uses the `default_lang` sent with the request.

To add a language, add a key set to `$messages` and `$translations` in that class. `ErrorMessages::isLanguageValid()` accepts any language that has a set in `$messages`.

## Authentication

Authentication uses [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum) personal access tokens.

1. Call `POST /api/login` with `phone`, `password` and `default_lang` (`en` or `fa`).
2. Read the plain-text token from `output.token`.
3. Send `Authorization: Bearer <token>` with every other request.

Protected routes go through `App\Http\Middleware\CheckIfAuthenticated`. It looks up the bearer token with `PersonalAccessToken::findToken()` and sets the matching shop as the current user. Tokens don't expire (`sanctum.expiration` is `null`). `POST /api/logout` deletes every token the shop has, which signs it out on all devices.

Always include `default_lang` when logging in. Login saves it to `shops.language`, a `NOT NULL` column, so a request without it fails.

## Responses

Every endpoint returns HTTP 200 with the same envelope. Clients check `status`, not the HTTP code:

```json
{
  "status": true,
  "output": {},
  "errors": [],
  "validations": {}
}
```

- `status`: `true` if the request succeeded.
- `output`: the result, either an object or a list.
- `errors`: general error messages, already translated.
- `validations`: a map from field name to a list of messages, filled when the input is invalid.

Send `Accept: application/json` with every request. Laravel puts validation errors and exceptions in this envelope only when the client asks for JSON.

All money fields (`price`, `purchase_price`, `discount`, `paid`, `amount`) are unsigned integers with no decimal part.

## Endpoints

Every path starts with `/api`. `GET /product`, `/customer` and `/order` take `?offset=N` and return 10 items per page, most recently updated first. The other list endpoints return everything.

**Public**

| Method | Path | Body | Description |
| --- | --- | --- | --- |
| POST | `/login` | `phone`, `password`, `default_lang` | Returns a Sanctum token |

**Token required**

| Method | Path | Body | Description |
| --- | --- | --- | --- |
| POST | `/logout` | | Deletes all of the shop's tokens |
| GET | `/shop` | | The signed-in shop, including `valid_until` and `language` |
| PATCH | `/shop/{shop}` | `name`, `language` | Renames the shop |

**Token and active subscription required**

| Method | Path | Body | Description |
| --- | --- | --- | --- |
| GET | `/product` | | Products, with `logs` and `inventory_logs` |
| GET | `/product/dropdown` | | Every product as `id`, `name`, `price`, for pickers |
| POST | `/product` | `name`, `price`, `purchase_price`, `inventory` | Creates a product. `inventory` becomes the first inventory log row |
| GET | `/product/{product}` | | One product with its history |
| PUT/PATCH | `/product/{product}` | same as create | Updates a product and logs what changed |
| DELETE | `/product/{product}` | | Deletes a product and its lines in past orders |
| GET | `/customer` | | Customers |
| GET | `/customer/dropdown` | | Every customer as `id`, `name`, for pickers |
| POST | `/customer` | `name`, `phone` | Creates a customer |
| GET | `/customer/{customer}` | | One customer |
| PUT/PATCH | `/customer/{customer}` | `name`, `phone` | Updates a customer |
| DELETE | `/customer/{customer}` | | Deletes a customer and their orders |
| GET | `/customer/{customer}/orders` | | All of a customer's orders, newest first |
| GET | `/order` | | Orders, each with its customer and lines |
| POST | `/order` | `products`, `customer_id`, `discount`, `paid` | Creates an order (example below) |
| GET | `/order/{order}` | | One order |
| DELETE | `/order/{order}` | | Deletes an order with its lines and payments |
| GET | `/order/{order}/payment` | | An order's payments, newest first |
| POST | `/order/{order}/payment` | `amount` | Adds a payment |
| DELETE | `/payment/{payment}` | | Deletes a payment |

`Route::resource` also registers `create` and `edit` routes for products, customers and orders, plus `update` for orders. No controller method exists for any of them, so they return an error.

### Creating an order

```http
POST /api/order
Accept: application/json
Content-Type: application/json
Authorization: Bearer 1|...

{
  "customer_id": 1,
  "discount": 5000,
  "products": { "1": 2, "4": 1 },
  "paid": 100000
}
```

`products` maps each product ID to a quantity. `customer_id`, `discount` and `paid` are optional; leave out `customer_id` for a walk-in sale. The response's `output` is the saved order:

```json
{
  "id": 1,
  "customer": { "id": 1, "shop_id": 1, "name": "Sara", "phone": "09121234567", "created_at": "...", "updated_at": "..." },
  "discount": 5000,
  "created_at": "...",
  "updated_at": "...",
  "order_products": [
    { "id": 1, "quantity": 2, "name_on_created": "Tea 500g", "price_on_created": 130000, "purchase_price_on_created": 90000 }
  ]
}
```

The `id` in `order_products` is the ID of the order line, not the product.

## Data model

- `shops`: the accounts. Each has a name, a unique `phone` used to log in, a password, a `language` and a `valid_until` date.
- `customers`, `products`, `orders`: each row has a `shop_id`. An order's `customer_id` is optional.
- `order_products`: order lines, including the snapshot columns described above.
- `payments`: each belongs to one order.
- `product_logs`, `product_inventory_logs`: product history.
- `personal_access_tokens`: Sanctum tokens.

## Running it locally

You'll need PHP 8.2 or newer, Composer, and MySQL. SQLite also works for local development.

```bash
git clone https://github.com/aidinghazagh/shopmanager-backend.git
cd shopmanager-backend
composer install
cp .env.example .env
php artisan key:generate
```

`.env.example` expects a MySQL database named `shopmanager` on `127.0.0.1`, with user `root` and no password. Create that database or edit the `DB_*` values. To use SQLite instead, set `DB_CONNECTION=sqlite` and set `DB_DATABASE` to the absolute path of an empty file such as `database/database.sqlite`.

```bash
php artisan migrate
php artisan serve
```

The API now runs at `http://127.0.0.1:8000/api`.

### Create a shop

There's no sign-up endpoint, so create a shop from Tinker:

```bash
php artisan tinker --execute="App\Models\Shop::forceCreate(['name' => 'Demo Shop', 'phone' => '09120000000', 'password' => 'secret123', 'valid_until' => now()->addYear()]);"
```

The model's `hashed` cast hashes the password. You need `forceCreate` because `valid_until` isn't mass-assignable. `php artisan db:seed` won't work: `DatabaseSeeder` calls `Shop::factory()`, and the project has no `ShopFactory`.

Then log in:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H 'Accept: application/json' \
  -H 'Content-Type: application/json' \
  -d '{"phone": "09120000000", "password": "secret123", "default_lang": "en"}'
```

## Project layout

```
app/Http/Controllers/   Auth, Shop, Product, Customer, Order, Payment
app/Http/Middleware/    CheckIfAuthenticated (bearer token), CheckShopValidity (subscription)
app/Http/Requests/      Validation rules and translated messages for each endpoint
app/Http/Resources/     JSON shapes for orders, order lines, products and their logs
app/Models/             Eloquent models, plus ErrorMessages and ResponseResult (the envelope)
app/Services/           AuthService::checkUserAccess, the shop ownership check
routes/api.php          Every endpoint
database/migrations/    Schema
```

## License

MIT. See [LICENSE](LICENSE).
