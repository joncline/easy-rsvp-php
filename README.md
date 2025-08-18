# Easy RSVP - Laravel Application

A simple and elegant RSVP management system built with Laravel 12.0, designed for easy event creation and guest management. This application was originally created as a Ruby on Rails application and has been migrated to Laravel for better performance and modern PHP development practices.

## 🎯 Purpose

Easy RSVP solves the common problem of managing event invitations and responses. Instead of using complex event management platforms or relying on social media events, this application provides:

- **Simple Event Creation**: Create events with rich descriptions, dates, and custom settings
- **Unique RSVP URLs**: Each event gets a unique, shareable URL for guest responses
- **Guest Management**: Track who's coming, who's not, and collect additional information
- **Admin Control**: Private admin URLs for event organizers to manage responses
- **No Account Required**: Guests can RSVP without creating accounts or logging in

Perfect for:
- Family gatherings and reunions
- Birthday parties and celebrations
- Corporate events and meetings
- Wedding receptions and ceremonies
- Community events and fundraisers
- Any event requiring guest count management

## 📜 Origins

This application was originally developed as a Ruby on Rails application and has been completely migrated to Laravel 12.0 to take advantage of:
- Modern PHP 8.2+ features
- Laravel's elegant syntax and powerful ORM
- Better shared hosting compatibility
- Improved security and performance
- Easier deployment on budget hosting platforms

## 🌟 Features

- **Event Creation**: Create events with custom descriptions, dates, and settings
- **RSVP Management**: Guests can easily respond to invitations via unique URLs
- **Admin Dashboard**: Manage events and view RSVP responses
- **Custom Fields**: Add custom questions for event-specific information
- **Rich Text Editor**: Use Trix editor for event descriptions
- **Responsive Design**: Bootstrap 5.1.3 for mobile-friendly interface
- **Secure**: Environment-based configuration with protected credentials
- **No Dependencies**: Uses CDN assets - no Node.js build process required

## 📜 Original Repository

This is a Laravel port of the original Easy RSVP Ruby on Rails application:
**https://github.com/KevinBongart/easy-rsvp**

## 🛠️ Technology Stack

- **Framework**: Laravel 12.0
- **PHP**: 8.2+
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: Bootstrap 5.1.3 (CDN)
- **Rich Text**: Trix Editor (CDN)
- **Utilities**: ClipboardJS for URL copying (CDN)
- **Hosting**: Optimized for shared hosting (DreamHost, HostGator, Bluehost, etc.)

### Why These Technologies?

**Laravel 12.0**: Modern PHP framework with excellent documentation and community support
**CDN Assets**: No build process required - perfect for shared hosting environments
**MySQL**: Widely supported database on virtually all hosting providers
**Bootstrap**: Responsive, mobile-first design that works everywhere

## 📋 Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache with mod_rewrite enabled
- **Composer**: For dependency management

### Shared Hosting Compatibility
This application is specifically optimized for shared hosting providers:
- ✅ **DreamHost** - Fully tested and deployed
- ✅ **HostGator** - Compatible with PHP 8.2+ plans
- ✅ **Bluehost** - Works with their WordPress hosting plans
- ✅ **SiteGround** - Compatible with their PHP hosting
- ✅ **GoDaddy** - Works with cPanel hosting plans

## 🔧 Installation

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/joncline/easy-rsvp-php.git
   cd easy-rsvp-php
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file with your database credentials:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start development server**
   ```bash
   php artisan serve
   ```

### Shared Hosting Deployment

#### DreamHost Deployment (Tested)

1. **Upload files via FTP/SFTP**
   - Upload all files to your domain's directory
   - Ensure `public/` folder contents are in the web root

2. **Database setup**
   - Create MySQL database in DreamHost panel
   - Update `.env` with database credentials

3. **Run migrations**
   ```bash
   php artisan migrate
   ```

4. **Set permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

#### Generic Shared Hosting

1. **Check PHP version**
   - Ensure PHP 8.2+ is available
   - Enable required extensions: PDO, MySQL, OpenSSL, Mbstring

2. **Upload application**
   - Upload all files via cPanel File Manager or FTP
   - Move `public/` folder contents to `public_html/` or web root

3. **Configure environment**
   - Copy `.env.example` to `.env`
   - Update database settings from hosting control panel

4. **Install dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

5. **Run setup**
   ```bash
   php artisan key:generate
   php artisan migrate
   php artisan config:cache
   php artisan route:cache
   ```

## 🌐 Deployment Guides

This repository includes comprehensive deployment documentation:

- **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Complete step-by-step deployment instructions
- **[DREAMHOST_COMPATIBILITY.md](DREAMHOST_COMPATIBILITY.md)** - DreamHost-specific deployment guide
- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Common issues and solutions
- **[SECURITY_NOTES.md](SECURITY_NOTES.md)** - Security configuration and best practices

### Quick Deployment Scripts

For advanced users, automated deployment scripts are provided:

```bash
# Full deployment with error handling
./deploy.sh

# Simple deployment (recommended for shared hosting)
./deploy-simple.sh
```

## 🔒 Security

- **Environment Protection**: All sensitive data in `.env` files (never committed)
- **Database Security**: Prepared statements prevent SQL injection
- **CSRF Protection**: Laravel's built-in CSRF protection enabled
- **Input Validation**: All user input validated and sanitized
- **Apache Security**: Security headers configured in `.htaccess`

## 📁 Project Structure

```
easy-rsvp-main/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   │   ├── EventController.php
│   │   ├── EventAdminController.php
│   │   └── RsvpController.php
│   └── Models/              # Eloquent models
│       ├── Event.php
│       ├── Rsvp.php
│       └── CustomField.php
├── database/
│   └── migrations/          # Database schema migrations
├── resources/
│   └── views/              # Blade templates
│       ├── events/         # Event-related views
│       └── layouts/        # Layout templates
├── routes/
│   └── web.php             # Application routes
├── public/                 # Public web assets
├── storage/                # Application storage
├── deploy.sh               # Full deployment script
├── deploy-simple.sh        # Simple deployment script
├── DEPLOYMENT_GUIDE.md     # Deployment instructions
├── SECURITY_NOTES.md       # Security documentation
└── .env.example           # Environment template
```

## 🎯 Usage

### Creating an Event

1. Visit your application's homepage
2. Fill in event details:
   - Event name and description (supports rich text)
   - Date, time, and location
   - RSVP deadline
   - Custom questions for guests
3. Click "Create Event"
4. Share the generated RSVP URL with guests
5. Use the admin URL to manage responses

### Managing RSVPs

1. Access the admin panel via the event's unique admin URL
2. View all guest responses in real-time
3. See response statistics (Yes/No/Maybe counts)
4. Export guest lists for planning
5. Edit event details as needed

### Guest Experience

1. Guests click the shared RSVP URL
2. Simple, mobile-friendly form to respond
3. Options: Yes, No, Maybe
4. Additional custom fields if configured
5. Instant confirmation message

## 🤝 Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Test thoroughly on both local and shared hosting
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

### Documentation
- [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) - Complete deployment instructions
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Common issues and solutions
- [SECURITY_NOTES.md](SECURITY_NOTES.md) - Security best practices

### Getting Help
- **Issues**: Open a GitHub issue for bugs or feature requests
- **Discussions**: Use GitHub Discussions for questions
- **Email**: Contact for deployment assistance

### Hosting-Specific Help
- **DreamHost**: Fully tested - see DREAMHOST_COMPATIBILITY.md
- **Other Shared Hosts**: Generic instructions in DEPLOYMENT_GUIDE.md
- **VPS/Dedicated**: Standard Laravel deployment practices apply

## 🏆 Acknowledgments

- **Original Concept**: Inspired by the need for simple, no-account RSVP management
- **Framework**: Built with [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- **UI Framework**: Powered by [Bootstrap](https://getbootstrap.com) for responsive design
- **Rich Text**: [Trix Editor](https://trix-editor.org) for beautiful event descriptions
- **Hosting**: Successfully deployed on [DreamHost](https://www.dreamhost.com) shared hosting
- **Community**: Thanks to the Laravel and PHP communities for excellent documentation and support

---

**Repository**: https://github.com/joncline/easy-rsvp-php  
**Original Ruby on Rails Version**: https://github.com/KevinBongart/easy-rsvp  
**Framework**: Laravel 12.0 | **PHP**: 8.2+ | **Database**: MySQL
