# API Guard for Laravel

Token-based API authentication for Laravel applications. Incoming requests are authenticated against a remote auth API (with caching), exposed as the `apiguard` request guard.

## Installation

```bash
composer require nodesol/apiguard
```

Publish the config file if you want to customize it:

```bash
php artisan vendor:publish --tag=apiguard-config
```

## Environment variables

| Variable | Purpose |
| --- | --- |
| `APIGUARD_NAME` | Name of the guard driver (default: `apiguard`) |
| `APIGUARD_URL` | Auth API endpoint |
| `APIGUARD_HEADERS` | Comma-separated request headers forwarded to the auth API |
| `APIGUARD_USER_COLUMN` | Dot-notation path to the user payload in the API response |
| `APIGUARD_POSTDATA` | JSON body sent with the auth API request |
| `CACHE_AUTH_STORE` | Cache store used to cache resolved users |

## Supported versions

- PHP: 8.2+
- Laravel: 11, 12 and 13

## Testing

```bash
composer test
```
