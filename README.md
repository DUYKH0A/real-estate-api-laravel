# Project Name

**Mô tả**:
- Laravel API cho hệ thống bất động sản
- Xác thực sử dụng Laravel Sanctum.

## Yêu cầu

- PHP >= 8.1

- Composer

## Cài đặt

Clone repository

git clone https://github.com/DUYKH0A/real-estate-api-laravel.git
cd project-name


### Cài đặt dependencies
```bash
composer install
```

### Sao chép file môi trường
```bash
cp .env.example .env
```


### Tạo file SQLite (Nếu chạy lần đầu)
```bash
touch database/database.sqlite
```

### Tạo key ứng dụng
```bash
php artisan key:generate
```

### Chạy migration và seed database
```bash
php artisan migrate --seed
```

### Tạo symbolic link cho storage (nếu project có upload file)
```bash
php artisan storage:link
```

### Chạy server Laravel
```bash
php artisan serve
```
