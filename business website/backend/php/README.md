Plain PHP backend for Dumsel Merchants

Quick start

1. Edit database credentials: `backend/php/src/config.php`
2. Import database (run in a terminal where `mysql` is available):

```powershell
mysql -u root -p < "c:\Users\Milton\Documents\GitHub\business website\backend\php\migrations\init.sql"
```

3. Start PHP built-in server (from repo root or anywhere):

```powershell
# serve the public folder
cd "c:\Users\Milton\Documents\GitHub\business website\backend\php\public"
php -S localhost:8080
```

4. API endpoints mirror the Node backend, e.g.:
- `GET http://localhost:8080/api/products`
- `POST http://localhost:8080/api/users/register`

Notes
- Passwords are stored in plain text (per your request). Do NOT use this in production.
- This is a minimal router; for more features consider using a framework like Laravel.
