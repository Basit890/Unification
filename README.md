# Unification - Community Fundraising Platform

A modern, community-driven fundraising platform built with PHP that connects donors with people in need. Built with a focus on transparency, ease of use, and community engagement.

## Features

### Homepage & Campaigns
- **Modern UI/UX**: Clean, responsive design with gradient cards and smooth animations
- **Live Search**: Real-time campaign search with instant results
- **Smart Filtering**: Category-based filtering and multiple sorting options
- **Top Donors Section**: Celebrating community generosity with ranking badges
- **Recent Posts**: Showcase latest fundraising campaigns with images

### User Management
- **Multi-User Types**: Donors, Fundraisers, and Admins
- **Profile Management**: Complete user profiles with activity tracking
- **Registration & Authentication**: Secure login system with session management

### Fundraising Features
- **Campaign Creation**: Easy-to-use campaign setup with image uploads
- **Progress Tracking**: Visual progress bars and funding statistics
- **Donation System**: Secure donation processing with transaction history
- **Category System**: Organized campaign categories for easy discovery

### Admin Dashboard
- **Modern Interface**: Card-based design with interactive statistics
- **User Management**: Comprehensive user oversight and management
- **Campaign Oversight**: Monitor and manage all fundraising campaigns
- **Donation Analytics**: Detailed donation reports and insights
- **Comment Management**: Moderate community interactions

### Technical Features
- **Modern CSS**: CSS Grid, Flexbox, and advanced animations
- **Font Awesome Icons**: Professional iconography throughout
- **Dark/Light Themes**: User preference-based theming
- **Performance Optimized**: Lazy loading and efficient code structure

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Icons**: Font Awesome 6.4.0
- **Server**: Apache (XAMPP compatible)
- **Design**: Modern CSS with gradients, shadows, and animations

## Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- Apache web server
- Modern web browser with CSS Grid support

## Installation

### 1. **Clone the Repository**
```bash
git clone https://github.com/Basit890/Unification.git
cd Unification
```

### 2. **Database Setup**
- Create a new MySQL database
- Import the database schema (if available)
- Update database credentials in `app/config/Database.php`

### 3. **Server Configuration**
- Place the project in your web server's document root
- Ensure Apache has mod_rewrite enabled
- Set proper file permissions for uploads directory

### 4. **Environment Setup**
- Configure your web server to point to the project directory
- Ensure PHP has file upload permissions enabled
- Set appropriate memory limits for image processing

## Project Structure

```
unification/
├── app/
│   ├── bootstrap.php          # Application initialization
│   ├── config/                # Configuration files
│   ├── controllers/           # Application controllers
│   ├── helpers/               # Helper functions
│   ├── models/                # Data models
│   ├── uploads/               # User uploaded files
│   └── views/                 # View templates
│       ├── admin/             # Admin dashboard views
│       ├── auth/              # Authentication views
│       ├── user/              # User profile views
│       └── home.php           # Main homepage
├── public/                    # Public assets
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript files
│   └── images/                # Static images
├── index.php                  # Main entry point
└── README.md                  # This file
```

## Configuration

### **Database Configuration**
Update `app/config/Database.php` with your database credentials:
```php
private $host = 'localhost';
private $db_name = 'your_database_name';
private $username = 'your_username';
private $password = 'your_password';
```

### **File Upload Settings**
Ensure your PHP configuration allows file uploads:
```ini
file_uploads = On
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

## Usage

### **For Donors**
1. Browse campaigns by category or search terms
2. View campaign details and progress
3. Make secure donations
4. Track donation history

### **For Fundraisers**
1. Create compelling campaign pages
2. Upload images and documents
3. Share campaigns with the community
4. Monitor funding progress

### **For Admins**
1. Access comprehensive dashboard
2. Manage users and campaigns
3. Monitor platform activity
4. Generate reports and analytics

## Customization

### **Themes**
The platform supports both light and dark themes. Users can toggle between themes using the theme switcher.

### **Styling**
- Main styles are in `public/css/style.css`
- Admin-specific styles are embedded in respective view files
- Uses CSS custom properties for easy color customization

### **Icons**
Font Awesome icons are used throughout. To change icons, update the `<i class="fas fa-icon-name"></i>` tags in the view files.

## Security Features

- **Session Management**: Secure user session handling
- **Input Validation**: All user inputs are sanitized
- **File Upload Security**: Restricted file types and size limits
- **SQL Injection Prevention**: Prepared statements for database queries
- **XSS Protection**: Output escaping for user-generated content

## Performance Features

- **Lazy Loading**: Images load as needed
- **Optimized CSS**: Efficient selectors and minimal redundancy
- **Compressed Assets**: Optimized file sizes
- **Caching Ready**: Structure supports future caching implementation

## Project Status

This is a **private fundraising platform** developed for community use. The codebase is not open for public contribution or copying.

## License

This is a **private project** and is not licensed for public use, distribution, or copying. All rights reserved.

## Support

For support and questions regarding this private platform:
- Contact the developer directly
- Check the documentation in the code comments
- Review the configuration files for setup guidance

## Recent Updates

### Latest Version Features
- Fixed filter bar button alignment
- Enhanced photo/document upload capabilities
- Modernized admin dashboard UI
- Improved campaign search and sorting
- Added Recent Fundraiser Posts section
- Enhanced Top Generous Donors display

---

**Built for community fundraising**
