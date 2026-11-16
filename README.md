# Hearts2Hearts (H2H) Web Portal & S2U Fanbase Management System

Official web portal and fanbase platform for girl group **Hearts2Hearts (H2H)**, integrated with an **AdminLTE 4** administrative dashboard. Built with modern dynamic PHP Native, MySQL, and responsive front-end styling.

---

## 🌟 Key Features

### 1. Public Fan Experience
- **Home & Hero Section**: Brand introduction, quick navigation, latest updates, and social links.
- **About Members**: Interactive member profiles (Jiwoo, Ian, Carmen, Stella, Yuha, Yeon) with MBTI, zodiac, fun facts, and roles.
- **Music & Discography**: Album catalog and tracklist preview with integrated audio playback.
- **Merchandise Store**: Categorized merchandise items (Lightstick, Photocard, Apparel, Accessories) with real-time stock and checkout flow.
- **Events & Ticketing**: Upcoming concerts, fan meetings, and festivals with live quota monitoring.
- **News**: Official band announcements and press releases.
- **S2U Fan Feedback**: Dedicated feedback form for verified S2U fanbase members with automatic authentication gating and profile prefilling.

### 2. AdminLTE 4 Administration Dashboard (`/admin`)
- **Dashboard Overview**: Summary counters, database module status, and recent S2U fan feedback widget.
- **Comprehensive CRUD**: Manage Albums, Songs, Members, Merchandise, Event Categories, Events, News, Gallery, Orders, and Feedbacks.
- **Table Data Grid Utilities**:
  - 🔍 **Live Search**: Multi-column keyword search with query persistence.
  - 🎯 **Contextual Filter**: Dynamic category, status, and relation filtering.
  - 📄 **Per-Page Selection**: Switch display limits between 5, 10, 15, 25, and 50 rows.
  - ↕️ **Interactive Column Sorting**: Clickable headers with directional sorting indicators (`ASC` / `DESC`).
  - 📑 **Smart Pagination**: Full state-preserving URL pagination.
- **Safe Delete Operation**:
  - Modal confirmation with dynamic record identification (`#ID (table)`).
  - Safe cascade cleanup preventing foreign key lock errors (`order_items`, `songs`, `events`, `gallery`, `feedbacks`).
  - Physical media cleanup for uploaded files upon deletion.
  - Offline-ready Bootstrap 5 bundle with CDN fallback.
- **Localization**: 100% English interface for admin labels, tables, navigation, forms, validation messages, and alerts.

---

## 🛠️ Technology Stack

- **Backend**: PHP 8.1+ (Native OOP & Procedural)
- **Database**: MySQL 8.0+ (`mysqli` extension with prepared statements)
- **Admin Dashboard**: AdminLTE 4 (Bootstrap 5.3, Bootstrap Icons)
- **Frontend**: HTML5, Vanilla JavaScript, CSS3 Responsive Grid & Flexbox
- **Testing**: PHP CLI automated testing suites

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.1 or higher with `mysqli` extension enabled
- MySQL 8.0 or MariaDB
- Local web server (Laragon, XAMPP, or Apache/Nginx)

### Steps

1. **Clone Repository**
   ```bash
   git clone https://github.com/<your-username>/H2H.git
   cd H2H
   ```

2. **Configure Database**
   - Create a new MySQL database named `db_hearts2hearts`:
     ```sql
     CREATE DATABASE `db_hearts2hearts` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
   - Import the database dump:
     ```bash
     mysql -u root -p db_hearts2hearts < database/db_hearts2hearts.sql
     ```
   - Copy database configuration if credentials differ:
     ```bash
     cp config/database.example.php config/database.php
     ```

3. **Run Application**
   - If using Laragon or XAMPP, place the folder in your web root (`www/H2H` or `htdocs/H2H`).
   - Or start PHP built-in server:
     ```bash
     php -S localhost:8000
     ```
   - Public Web: `http://localhost:8000` or `http://localhost/H2H`
   - Admin Panel: `http://localhost:8000/admin` or `http://localhost/H2H/admin`

---

## 🔑 Default Accounts

| Role | Email | Password | Access Level |
|---|---|---|---|
| **Super Administrator** | `admin@h2h.com` | `admin123` | Full AdminLTE 4 dashboard & CRUD |
| **S2U Fan (Customer)** | `fan@h2h.com` | `admin123` | Store checkout & S2U fan feedback |

---

## 🧪 Automated Testing

Run the included verification suites to test all components:

```bash
# Test delete functionality & safe cascade handling
php tests/test_delete_functionality.php

# Test AdminLTE 4 table features (search, filter, sort, paginate, dashboard)
php tests/test_admin_features.php

# Test S2U rebranding, auth gating, and feedback persistence
php tests/test_s2u_feedback_verification.php
```

---

## 📂 Project Structure

```text
H2H/
├── admin/                    # AdminLTE 4 management dashboard
│   ├── assets/               # AdminLTE CSS/JS and Bootstrap 5 bundle
│   ├── config/               # Table schemas and column configurations
│   ├── includes/             # Admin helper functions
│   ├── dashboard.php         # Admin dashboard view & widgets
│   ├── index.php             # Main listing controller & creation form
│   ├── edit.php              # Record update view
│   ├── delete.php            # Safe delete controller with cascade cleanup
│   └── login.php             # Admin authentication
├── assets/                   # Public band assets (logos, member photos, audio, album covers)
├── config/                   # Global database connection & settings
├── database/                 # Complete SQL schema & seed dump (`db_hearts2hearts.sql`)
├── docs/                     # Architecture & implementation documentation
├── includes/                 # Public navbar, footer, auth guards, and utilities
├── tests/                    # Automated PHP CLI testing suites
├── uploads/                  # User & admin uploaded media (albums, gallery, members, etc.)
├── about.php                 # Member profiles & band backstory
├── checkout.php              # Auth-gated merchandise checkout
├── events.php                # Concerts & fan meetings schedule
├── feedback.php              # Auth-gated S2U fan feedback form
├── index.php                 # Public home page
├── music.php                 # Discography & audio player
├── news.php                  # Band announcements & press
├── orders.php                # Customer order history
├── store.php                 # Merchandise catalog
└── style.css                 # Public responsive styling
```

---

## 📄 License
This project is developed for educational and portfolio demonstration purposes. All musical references and media are property of their respective creators.
