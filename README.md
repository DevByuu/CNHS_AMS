# 🎓 RFID Attendance System - Concepcion National High School

<div align="center">

![RFID Attendance](https://img.shields.io/badge/RFID-Attendance%20System-blue?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**A modern, real-time RFID-based attendance tracking system built for educational institutions**

[Features](#-features) • [Installation](#-installation) • [Usage](#-usage) • [Screenshots](#-screenshots) • [Documentation](#-documentation)

---

</div>

## 📋 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Screenshots](#-screenshots)
- [Database Schema](#-database-schema)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

---

## 🎯 About

The **RFID Attendance System** is a comprehensive, real-time attendance tracking solution designed specifically for Concepcion National High School. The system leverages RFID technology to automate student check-ins, providing administrators with powerful analytics, real-time monitoring, and comprehensive reporting capabilities.

### 🌟 Key Highlights

- ⚡ **Real-time Monitoring** - Live dashboard with instant attendance updates
- 🎴 **RFID Integration** - Fast, contactless check-in using RFID cards
- 📊 **Advanced Analytics** - Comprehensive reports and trend analysis
- 📱 **Responsive Design** - Works seamlessly on desktop, tablet, and mobile
- 🔔 **Sound Notifications** - Audio feedback for successful check-ins
- 📄 **PDF Reports** - Professional, printable attendance reports
- 🎨 **Modern UI/UX** - Clean, intuitive interface with smooth animations

---

## ✨ Features

### 👨‍🎓 Student Management
- ✅ Student registration with LRN and grade level
- ✅ RFID card assignment and management
- ✅ Bulk import/export capabilities
- ✅ Student profile management

### 📡 Real-Time Attendance Monitoring
- ✅ Live attendance dashboard with auto-refresh
- ✅ RFID scanner integration
- ✅ Instant check-in notifications with sound effects
- ✅ Visual scanning animations
- ✅ Today's check-ins scrollable list
- ✅ Live attendance rate circular progress indicator

### 📊 Reports & Analytics
- ✅ Daily, weekly, and monthly attendance reports
- ✅ Grade-level filtering and analysis
- ✅ Attendance trend charts (line and bar graphs)
- ✅ Present students PDF export with school branding
- ✅ Summary by grade level breakdown
- ✅ Perfect attendance tracking
- ✅ Absence rate calculations

### 🎨 Dashboard Features
- ✅ Real-time statistics cards
- ✅ 7-day attendance trend graphs
- ✅ Today's distribution donut chart
- ✅ Auto-refresh every 30 seconds
- ✅ Smooth data animations
- ✅ Live time display

### 📈 Advanced Features
- ✅ Custom date range selection
- ✅ Grade-level specific reports
- ✅ Attendance rate percentage tracking
- ✅ Late arrival tracking
- ✅ Professional PDF generation with logos
- ✅ Responsive design for all devices

---

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 10.x
- **Language:** PHP 8.1+
- **Database:** MySQL 8.0+
- **PDF Generation:** DomPDF

### Frontend
- **Template Engine:** Blade
- **CSS Framework:** Bootstrap 5.3
- **JavaScript:** Vanilla JS (ES6+)
- **Charts:** Chart.js 4.4.1
- **Icons:** Bootstrap Icons
- **Notifications:** SweetAlert2

### Additional Libraries
- **Carbon:** Date/time manipulation
- **Laravel Mix:** Asset compilation

---

## 💻 System Requirements

### Minimum Requirements
- **PHP:** 8.1 or higher
- **Composer:** 2.x
- **MySQL:** 8.0 or higher
- **Web Server:** Apache/Nginx
- **Node.js:** 16.x or higher (for asset compilation)
- **NPM:** 8.x or higher

### Recommended Requirements
- **PHP:** 8.2
- **RAM:** 2GB minimum
- **Storage:** 1GB free space
- **RFID Reader:** USB RFID card reader (HID-compliant)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/rfid-attendance-system.git
cd rfid-attendance-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=attendance_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations:

```bash
php artisan migrate
```

### 6. Seed Database (Optional)

```bash
php artisan db:seed
```

### 7. Compile Assets

```bash
npm run dev
# or for production
npm run build
```

### 8. Storage Link

```bash
php artisan storage:link
```

### 9. Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## ⚙️ Configuration

### School Information

Update school details in `config/app.php`:

```php
'school_name' => env('SCHOOL_NAME', 'Concepcion National High School'),
'school_address' => env('SCHOOL_ADDRESS', 'Concepcion, Mabini, Bohol'),
```

Or add to your `.env` file:

```env
SCHOOL_NAME="Your School Name"
SCHOOL_ADDRESS="Your School Address"
```

### RFID Reader Configuration

The system works with standard USB RFID readers that act as keyboard input devices. No additional configuration is required.

### Logos

Place your school logos in `public/img/`:
- `cnhs.png` - School logo
- `deped-logo.png` - DepEd logo

---

## 📖 Usage

### For Administrators

1. **Login** to the system using your credentials
2. **Manage Students** - Add, edit, or import student records
3. **Monitor Attendance** - View real-time check-ins on the live monitor
4. **Generate Reports** - Create and export attendance reports

### For Attendance Officers

1. Navigate to **Live Attendance Monitor**
2. Students tap their RFID cards on the reader
3. System automatically records check-in with timestamp
4. View all today's check-ins in the scrollable list
5. Export reports as needed

### Generating Reports

1. Go to **Attendance Reports**
2. Select date range and grade level (optional)
3. Click **Generate Report** to view analytics
4. Click **Export Report** for PDF download

---

## 📸 Screenshots

### Dashboard
![Dashboard](docs/screenshots/dashboard.png)
*Real-time attendance dashboard with live statistics and charts*

### Live Monitor
![Live Monitor](docs/screenshots/live-monitor.png)
*RFID attendance monitoring with instant check-in feedback*

### Reports
![Reports](docs/screenshots/reports.png)
*Comprehensive attendance reports with analytics*

### PDF Export
![PDF Export](docs/screenshots/pdf-export.png)
*Professional PDF reports with school branding*

---

## 🗄️ Database Schema

### Tables

#### students
- `id` - Primary key
- `lrn` - Learner Reference Number (unique)
- `name` - Student full name
- `grade` - Grade level (7-12)
- `rfid` - RFID card number (unique)
- `created_at` - Record creation timestamp
- `updated_at` - Last update timestamp

#### attendances
- `id` - Primary key
- `student_id` - Foreign key to students
- `date` - Attendance date
- `time_in` - Check-in time
- `status` - Attendance status (present/absent/late)
- `created_at` - Record creation timestamp
- `updated_at` - Last update timestamp

#### users
- `id` - Primary key
- `name` - User name
- `email` - Email (unique)
- `password` - Hashed password
- `role` - User role
- `created_at` - Record creation timestamp
- `updated_at` - Last update timestamp

---

## 🔌 API Endpoints

### Attendance
- `GET /api/attendance/today` - Get today's check-ins
- `POST /attendance/checkin` - Process RFID check-in
- `GET /api/attendance/stats` - Get attendance statistics

### Dashboard
- `GET /api/dashboard/stats` - Get dashboard statistics

### Reports
- `GET /reports/realtime-data` - Get real-time report data
- `GET /reports/export-present` - Export present students PDF

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Code Style

- Follow PSR-12 coding standards for PHP
- Use ESLint for JavaScript
- Write clear, descriptive commit messages
- Add comments for complex logic

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Contact

**Concepcion National High School**
- 📍 Concepcion, Mabini, Bohol
- 📧 Email: cnhs@example.com
- 🌐 Website: https://cnhs.example.com

**Project Maintainer**
- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- Department of Education - Region VII, Central Visayas
- Laravel Community
- Bootstrap Team
- Chart.js Contributors
- All contributors and testers

---

<div align="center">

**Made with ❤️ for Concepcion National High School**

![Footer](https://img.shields.io/badge/Built%20with-Laravel-red?style=flat-square)
![Footer](https://img.shields.io/badge/RFID-Technology-blue?style=flat-square)
![Footer](https://img.shields.io/badge/Education-First-green?style=flat-square)

⭐ Star this repo if you find it helpful!

</div>