# Easy RSVP - Laravel Application

A simple and elegant RSVP management system built with Laravel 12.0, designed for easy event creation and guest management.

## 🌟 Features

- **Event Creation**: Create events with custom descriptions, dates, and settings
- **RSVP Management**: Guests can easily respond to invitations via unique URLs
- **Admin Dashboard**: Manage events and view RSVP responses
- **Custom Fields**: Add custom questions for event-specific information
- **Rich Text Editor**: Use Trix editor for event descriptions
- **Responsive Design**: Bootstrap 5.1.3 for mobile-friendly interface
- **Secure**: Environment-based configuration with protected credentials

## 🚀 Live Demo

The application is deployed and running at: **https://rsvp.joncline.com**

## 🛠️ Technology Stack

- **Framework**: Laravel 12.0
- **PHP**: 8.2+
- **Database**: MySQL
- **Frontend**: Bootstrap 5.1.3 (CDN)
- **Rich Text**: Trix Editor
- **Utilities**: ClipboardJS for URL copying
- **Hosting**: DreamHost Shared Hosting

## 📋 Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Apache with mod_rewrite enabled

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/joncline/easy-rsvp-main.git
   cd easy-rsvp-main
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

## 🌐 Deployment

### DreamHost Shared Hosting

This application is optimized for DreamHost shared hosting deployment. See the comprehensive deployment guides:

- **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Step-by-step deployment instructions
- **[DREAMHOST_COMPATIBILITY.md](DREAMHOST_COMPATIBILITY.md)** - Compatibility analysis
- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Common issues and solutions
- **[SECURITY_NOTES.md](SECURITY_NOTES.md)** - Security configuration details

### Quick Deployment

Use the provided deployment scripts:

```bash
# Full deployment with Node.js handling
./deploy.sh

# Simple deployment (recommended for shared hosting)
./deploy-simple.sh
```

## 🔒 Security

- Environment files (`.env`, `.env.production`) are protected in `.gitignore`
- Database credentials are never committed to the repository
- Apache security headers configured in `.htaccess`
- Only `.env.example` contains placeholder values for sharing

## 📁 Project Structure

```
easy-rsvp-main/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   └── Models/              # Eloquent models
├── database/
│   └── migrations/          # Database migrations
├── resources/
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Web routes
├── public/                 # Public assets
├── deploy.sh               # Full deployment script
├── deploy-simple.sh        # Simple deployment script
└── .env.example           # Environment template
```

## 🎯 Usage

### Creating an Event

1. Visit the homepage
2. Fill in event details (name, description, date, location)
3. Configure RSVP settings
4. Add custom fields if needed
5. Create the event

### Managing RSVPs

1. Access the admin panel via the event's admin URL
2. View all responses and guest information
3. Export or manage guest lists
4. Edit event details as needed

### Guest Experience

1. Guests receive a unique RSVP URL
2. Simple form to respond Yes/No/Maybe
3. Additional custom fields if configured
4. Confirmation and thank you message

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

For deployment issues or questions:
- Check the [TROUBLESHOOTING.md](TROUBLESHOOTING.md) guide
- Review the [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)
- Open an issue on GitHub

## 🏆 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI powered by [Bootstrap](https://getbootstrap.com)
- Rich text editing with [Trix](https://trix-editor.org)
- Successfully deployed on [DreamHost](https://www.dreamhost.com)
