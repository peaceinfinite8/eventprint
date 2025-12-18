# EventPrint - Quick Start Guide

## 🚀 Getting Started (5 Steps)

### Step 1: Start XAMPP
```
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL
```

### Step 2: Import Database
```
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create database: eventprint
3. Import: eventprint.sql
4. Import: migrations/migration_p0.sql
```

### Step 3: Configure Base URL
Edit `app/config/app.php`:
```php
'base_url' => 'http://localhost/eventprint/public',
```

Edit `public/.htaccess`:
```apache
RewriteBase /eventprint/public/
```

### Step 4: Test
Visit: `http://localhost/eventprint/public/`

Expected: Homepage loads with hero carousel

### Step 5: Browse All Pages
- Homepage: `/`
- Products: `/products`
- Product Detail: `/products/slug-produk`
- Blog: `/blog`
- Blog Detail: `/blog/slug-artikel`
- Our Home: `/our-home`
- Contact: `/contact`
- Admin: `/admin/login`

---

## 🔧 Troubleshooting

### Pages show 404
**Fix**: Check RewriteBase in `.htaccess` matches your folder structure

### Assets (CSS/JS) not loading
**Fix**: Verify `base_url` in `app/config/app.php`

### Database connection error
**Fix**: Check credentials in `app/config/db.php`

### PHP errors visible
**Fix**: Set `'debug' => false` in `app/config/app.php` for production

---

## 📋 Admin Access

**Default Login**:
- URL: `/admin/login`
- Check `users` table in database for credentials

**Admin Modules**:
- Products & Categories
- Blog Posts
- Hero Slides
- Our Store Locations
- Contact Messages
- Settings
- Users

---

## ✅ Feature Checklist

- [x] Homepage with hero carousel
- [x] Products list with filter & pagination
- [x] Product detail with gallery & options
- [x] Blog list with featured posts
- [x] Blog detail with social share
- [x] Our Home with store locations
- [x] Contact form with validation
- [x] Responsive layout
- [x] SEO-friendly URLs
- [x] Dynamic content from CMS

---

## 🎨 Customization

### Change Logo
Admin → Settings → Upload logo

### Edit Hero Slides
Admin → Home Content → Hero Slides

### Add Products
Admin → Products → Add New

### Add Blog Posts
Admin → Blog → Add New

### Update Contact Info
Admin → Settings → Contact Information

---

## 📞 Support

For issues or questions, refer to:
- `INSTALLATION.md` - Detailed setup guide
- `ADMIN_GUIDE.md` - Admin panel guide
- `IMPLEMENTATION_PROGRESS.md` - Technical details

---

**Status**: Ready for Production ✅  
**Last Updated**: <?= date('Y-m-d H:i') ?>
