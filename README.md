# Realty Smartz Pathshala - Website & Candidate Onboarding Portal

[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-777BB4?logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-utf8mb4-4479A1?logo=mysql)](https://www.mysql.com/)
[![License: Proprietary](https://img.shields.io/badge/License-Proprietary-gold.svg)](https://realtysmartzpathshala.in/)

Production-ready web application and proctored Candidate Onboarding Portal built for **Realty Smartz Pathshala** (Gurgaon, India). Matches the official branding, navy & gold visual language, typography, dark/light theme, and dynamic preloader.

Runs natively on **standard cPanel / shared PHP hosting** with **zero Composer or Node dependencies required on the host server**.

---

## 📑 Table of Contents
1. [Tech Stack & Architecture](#1-tech-stack--architecture)
2. [Folder Structure](#2-folder-structure)
3. [Default Administrative Credentials](#3-default-administrative-credentials)
4. [Step-by-Step cPanel Deployment Guide](#4-step-by-step-cpanel-deployment-guide)
5. [Local Development Setup](#5-local-development-setup)
6. [Automated Daily Cron Backup](#6-automated-daily-cron-backup)
7. [Database Migrations System](#7-database-migrations-system)
8. [RESTful API Endpoint Documentation](#8-restful-api-endpoint-documentation)
9. [Security & Data Protection Blueprint](#9-security--data-protection-blueprint)
10. [Email Deliverability & Anti-Spam Configuration](#10-email-deliverability--anti-spam-configuration)
11. [Administrative Account CLI Management (reset-admin.php)](#11-administrative-account-cli-management-reset-adminphp)
12. [Aptitude Test & Guest Links Module](#12-aptitude-test--guest-links-module)
13. [QR Codes Management & Walk-In Registration](#13-qr-codes-management--walk-in-registration)
14. [Google Sheets Live Sync Integration](#14-google-sheets-live-sync-integration)
15. [Automated Playwright Test Suite](#15-automated-playwright-test-suite)

---

## 1. Tech Stack & Architecture

- **Backend**: Plain PHP 8.1+ (Clean MVC architecture, strict types, zero heavy frameworks, zero runtime composer execution).
- **Database**: MySQL 8.x / MariaDB 10.4+ (`utf8mb4_unicode_ci`, InnoDB engine, indexed keys, relational foreign constraints).
- **Hard Delete Prevention**: MySQL SQLSTATE `45000` triggers block direct `DELETE` queries on core `users` and `documents` tables. Application soft-deletes via `is_archived`.
- **Security & Cryptography**: Sensitive candidate identity data (Aadhaar, bank account numbers) encrypted at rest using **AES-256-CBC with HMAC-SHA256 authenticated encryption** via OpenSSL. UI displays masked formats.
- **Frontend**: Semantic HTML5, modern vanilla CSS (custom CSS variables, responsive bento grid, light/dark themes), vanilla JavaScript (Fetch API, Canvas dynamic compression, digital signature pad).
- **Libraries Committed to `/vendor`**:
  - `PHPMailer 6.9.1`: Transactional email delivery with HTML template support and offline log fallback.
  - `FPDF 1.86`: High-performance PDF generator for branded candidate profiles and scorecards.
  - Custom `DocxHelper`: Pure PHP OpenXML `.docx` generator without COM or external binary dependencies.

---

## 2. Folder Structure

```text
/ (repository root)
├── public/                 -> ONLY this directory is exposed as the web root
│   ├── assets/             -> CSS, JS, images, logos, fonts
│   ├── index.php           -> Front controller with dynamic APP_PATH resolver
│   ├── .htaccess           -> Apache clean URLs, HTTPS force, headers, brotli/gzip
│   └── robots.txt          -> SEO crawler directives
├── app/                    -> Protected core application (located above public_html on server)
│   ├── config/             -> config.php, database.php, routes.php (ALL routes in ONE file)
│   ├── api/                -> api.php (ALL 12 REST API endpoints in ONE file, documented)
│   ├── controllers/        -> HomeController, AuthController, CandidateController, AptitudeController, AdminController
│   ├── models/             -> User, Profile, Test, Project, Blog, Setting, AuditLog
│   ├── middleware/         -> AuthMiddleware, AdminMiddleware, CsrfMiddleware, RateLimitMiddleware
│   ├── helpers/            -> CryptoHelper, Validator, Compressor, Uploader, Mailer, PdfHelper, DocxHelper, ExportHelper
│   └── views/              -> layouts/, public/, candidate/, admin/, partials/, email_templates/
├── database/               -> schema.sql, seed.sql, /migrations/*.sql
├── storage/                -> uploads/, logs/, backups/ (git-ignored, never wiped by git deploys)
├── bin/                    -> backup.php (CLI / Cron database dump runner)
├── vendor/                 -> PSR-4 autoloader, PHPMailer, FPDF (committed to git)
├── .cpanel.yml             -> Auto-deploy script for cPanel Git Version Control
├── .gitignore              -> Excludes .env, storage contents, and local artifacts
├── .env.example            -> Environment configuration template
└── README.md               -> Complete operational and deployment documentation
```

---

## 3. Default Administrative Credentials

The database seed (`database/seed.sql`) provisions three default staff accounts:

| Role | Email | Password | Permissions |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@realtysmartzpathshala.in` | `Admin@12345` | Full system control, migrations runner, backups |
| **HR Manager** | `hr@realtysmartzpathshala.in` | `Admin@12345` | Candidate reviews, approvals, question bank |
| **Reviewer** | `reviewer@realtysmartzpathshala.in` | `Admin@12345` | Candidate dossier reviews, verification notes |

> **⚠️ Security Warning:** Change the default passwords immediately upon initial login in production.

---

## 4. Step-by-Step cPanel Deployment Guide

### Step 1: Create a Private GitHub Repository
1. Log in to [GitHub](https://github.com/) and click **New Repository**.
2. Name the repository (e.g. `realty-smartz-portal`).
3. Set visibility to **Private**.
4. In your local project directory, initialize Git and push the codebase:
   ```bash
   git init
   git add .
   git commit -m "Initial commit - Realty Smartz Pathshala complete portal"
   git branch -M main
   git remote add origin git@github.com:YOUR_GITHUB_USERNAME/realty-smartz-portal.git
   git push -u origin main
   ```

---

### Step 2: Configure SSH Deploy Key in cPanel
Because the GitHub repository is **Private**, cPanel needs an authorized SSH deploy key to pull updates:

1. Log in to your **cPanel Dashboard**.
2. Under the **Security** section, click **SSH Access**.
3. Click **Manage SSH Keys** &gt; **Generate a New Key**.
   - Key Name: `id_rsa` (or `github_deploy`)
   - Key Password: Leave blank (no passphrase)
   - Key Type: `RSA`
   - Key Size: `4096`
4. Click **Generate Key**.
5. Under **Public Keys**, find the newly generated key, click **Manage**, and click **Authorize**.
6. Click **View/Download** on the public key and **Copy the entire key text** (starts with `ssh-rsa AAAA...`).
7. Go to your **GitHub Repository** in your browser:
   - Navigate to **Settings** &gt; **Deploy keys** (in the left menu).
   - Click **Add deploy key**.
   - Title: `cPanel Production Server`
   - Key: Paste the public key copied from cPanel.
   - Leave "Allow write access" **unchecked**.
   - Click **Add key**.

---

### Step 3: Clone Repository via cPanel Git Version Control
1. In cPanel, navigate to the **Files** section and click **Git™ Version Control**.
2. Click the **Create** button in the upper-right corner.
3. Configure the clone settings:
   - **Clone URL**: Paste your GitHub SSH URL (e.g. `git@github.com:YOUR_USERNAME/realty-smartz-portal.git`).
   - **Repository Path**: Enter `repositories/realty-smartz` (or any staging directory under `/home/USERNAME/`).
   - **Repository Name**: `realty-smartz`
4. Click **Create**. cPanel will clone the repository using the authorized SSH key.

---

### Step 4: Configure Target Directories in `.cpanel.yml`
Open `.cpanel.yml` in the cPanel File Manager or in your local repo before pushing:
Replace `YOUR_CPANEL_USERNAME` with your actual cPanel username:
```yaml
deployment:
  tasks:
    - export CPANEL_USER=YOUR_CPANEL_USERNAME
    - export APP_DIR=/home/$CPANEL_USER/pathshala_app
    - export PUBLIC_DIR=/home/$CPANEL_USER/public_html
```
*(If your site runs on an addon domain or subdomain, change `PUBLIC_DIR` to `/home/$CPANEL_USER/subdomain.domain.com`)*.

---

### Step 5: Execute Initial Deployment
1. In cPanel **Git™ Version Control**, click **Manage** next to `realty-smartz`.
2. Select the **Deploy HEAD Commit** tab.
3. Click **Deploy HEAD Commit**.
4. cPanel will run `.cpanel.yml`:
   - It copies the `public/` folder contents directly into `public_html/`.
   - It copies `app/`, `vendor/`, `database/`, and `bin/` safely into `/home/USERNAME/pathshala_app/` (above web root).
   - It sets up `storage/uploads`, `storage/logs`, and `storage/backups`.

---

### Step 6: Create MySQL Database & User
1. In cPanel, go to **MySQL® Databases**.
2. Create a new database: e.g. `USERNAME_pathshala_db`.
3. Create a new database user: e.g. `USERNAME_dbuser` with a strong password.
4. Under **Add User to Database**, select the user and database, click **Add**, and grant **ALL PRIVILEGES** (except DROP on production if desired).
5. Open **phpMyAdmin** from cPanel:
   - Select your new database.
   - Click **Import** tab.
   - Choose `database/schema.sql` &gt; Click **Go**.
   - Choose `database/seed.sql` &gt; Click **Go**.

---

### Step 7: Create Production `.env` File
In cPanel **File Manager**, navigate to `/home/USERNAME/pathshala_app/` (or your chosen APP_DIR).
Create a file named `.env` and fill in your production values:

```env
APP_NAME="Realty Smartz Pathshala"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://realtysmartzpathshala.in
APP_KEY=b9e4a38f0d7e2c918a5f3d4e7a6b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b

DB_HOST=localhost
DB_PORT=3306
DB_NAME=USERNAME_pathshala_db
DB_USER=USERNAME_dbuser
DB_PASS=YOUR_STRONG_DB_PASSWORD

MAIL_HOST=mail.realtysmartzpathshala.in
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=info@realtysmartzpathshala.in
MAIL_PASSWORD=YOUR_CPANEL_EMAIL_PASSWORD
MAIL_FROM_ADDRESS=info@realtysmartzpathshala.in
MAIL_FROM_NAME="Realty Smartz Pathshala"

HR_NOTIFICATION_EMAIL=hr@realtysmartzpathshala.in
MAX_UPLOAD_SIZE_MB=2
```

---

### Step 8: Verify PHP 8.1+ & Extensions
1. In cPanel, search for **Select PHP Version** or **MultiPHP Manager**.
2. Ensure your domain is set to **PHP 8.1, 8.2, or 8.3**.
3. Open **MultiPHP INI Editor** or **PHP Extensions**:
   - Verify the following extensions are enabled:
     - `pdo_mysql`
     - `mbstring`
     - `gd` (required for image compression)
     - `zip` (required for bulk ZIP exports)
     - `fileinfo` (required for MIME validation)
     - `openssl` (required for AES-256 data encryption)
     - `curl`

---

## 5. Local Development Setup

To test locally using PHP's built-in web server:

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
2. Configure your local MySQL database credentials in `.env`.
3. Import `database/schema.sql` and `database/seed.sql` into your local MySQL server:
   ```bash
   mysql -u root -p realty_smartz_db < database/schema.sql
   mysql -u root -p realty_smartz_db < database/seed.sql
   ```
4. Start the PHP server pointing **strictly to `/public`**:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
5. Open your browser:
   - Public Website: `http://127.0.0.1:8000/`
   - Candidate Portal: `http://127.0.0.1:8000/login`
   - Admin Management: `http://127.0.0.1:8000/admin/login`

---

## 6. Automated Daily Cron Backup

To schedule automated daily database backups:

1. In cPanel, navigate to **Cron Jobs**.
2. Under **Add New Cron Job**, select **Once Per Day (0 2 * * *)** (2:00 AM).
3. In the **Command** field, enter:
   ```bash
   /usr/local/bin/php /home/USERNAME/pathshala_app/bin/backup.php > /dev/null 2>&1
   ```
4. Click **Add New Cron Job**.
5. Backups are saved compressed as `.sql.gz` in `/storage/backups/` and can be inspected or downloaded at any time from the **Admin Panel &gt; DB Backups**.

---

## 7. Database Migrations System

When you pull code changes that include new database tables or schema adjustments:
1. Migration files are stored sequentially in `database/migrations/` (e.g. `001_create_initial_schema.sql`, `002_add_field.sql`).
2. Log into the Admin Panel as **Super Admin**.
3. In the left navigation, click **Run Migrations** (or visit `/admin/migrate`).
4. The migration runner checks which scripts have already executed, applies only new files within a transaction, and records them in the `migrations` table.
5. The admin footer automatically updates with the **Latest Deployed Timestamp**.

---

## 8. RESTful API Endpoint Documentation

All endpoints are served under `/api/v1` and handle JSON request and response payloads. The complete specification is documented at the top of `app/api/api.php`:

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/register` | Register new candidate & dispatch 6-digit OTP | Public |
| `POST` | `/api/v1/auth/login` | Authenticate candidate or administrator | Public |
| `POST` | `/api/v1/auth/verify-otp` | Verify email address with 6-digit OTP | Public |
| `GET` | `/api/v1/candidate/profile` | Retrieve candidate profile with decrypted fields | Candidate Auth |
| `POST` | `/api/v1/candidate/profile/save` | Save profile section (Sections A through G) | Candidate Auth |
| `POST` | `/api/v1/candidate/document/upload`| Upload and compress verification document (max 2MB) | Candidate Auth |
| `POST` | `/api/v1/candidate/profile/submit`| Submit dossier for HR review (locks modifications) | Candidate Auth |
| `GET` | `/api/v1/test/session` | Start or resume active assessment session | Candidate Auth |
| `POST` | `/api/v1/test/answer` | Autosave question answer or mark for review | Candidate Auth |
| `POST` | `/api/v1/test/violation` | Log proctor tab-switch or fullscreen blur | Candidate Auth |
| `POST` | `/api/v1/test/submit` | Finalize assessment attempt and calculate score | Candidate Auth |
| `GET` | `/api/v1/projects` | List real estate portfolio projects with filters | Public |

---

## 9. Security & Data Protection Blueprint

1. **Non-Destructive Data Architecture**:
   - No hard `DELETE` queries are executed anywhere in application logic.
   - Database triggers `trg_prevent_user_delete` and `trg_prevent_document_delete` block accidental `DELETE` operations at the database engine level.
   - Admin UI provides **Archive** and **Restore** actions.
2. **Encrypted at Rest**:
   - Sensitive government identification numbers (Aadhaar) and banking account numbers are encrypted using **AES-256-CBC** before being written to disk and decrypted dynamically for authorized views.
   - Aadhaar numbers are masked in the UI (`XXXXXXXX1234`).
3. **Hard 2 MB Upload Limit & Dual Compression**:
   - Browser compresses candidate images in an offscreen canvas to `< 1600px` before uploading.
   - The server verifies MIME types using `finfo` and performs a secondary pass with PHP GD.
   - Files are stored outside the web root (`storage/uploads/`) and served strictly through authenticated PHP scripts.
4. **Proctored Aptitude Test Security**:
   - Server-validated expiration timestamps prevent timer manipulation.
   - Visibility changes, window blurs, and fullscreen exits trigger client-side proctor warnings and log infractions to the HR dossier review screen.
   - Copy-paste and right-click context menus are disabled.

---

## 10. Email Deliverability & Anti-Spam Configuration

All transactional emails (candidate registration OTP, email verification, password reset tokens, application approvals, reviewer feedback) are dispatched using **PHPMailer over SMTPS on Port 465 (SSL)**.

To guarantee that your transactional messages land directly in the candidate's **Primary Inbox** rather than Spam or Junk folders:

### 1. Fix SPF & DKIM Records in cPanel
1. Log in to your **cPanel Dashboard**.
2. In the **Email** section, click **Email Deliverability**.
3. Locate your primary domain (`realtysmartzpathshala.in`).
4. If the status is displayed as **Problems Exist (DKIM or SPF missing/invalid)**, click the **Repair** button.
5. cPanel will automatically generate and publish the authoritative TXT records for SPF and 2048-bit DKIM directly to your authoritative DNS zone.

### 2. Configure the DMARC TXT DNS Record
1. In cPanel, navigate to **Zone Editor** (or your external DNS manager if using Cloudflare).
2. Click **Manage** next to `realtysmartzpathshala.in`.
3. Add a new DNS Record:
   - **Type**: `TXT`
   - **Name / Host**: `_dmarc` (or `_dmarc.realtysmartzpathshala.in.`)
   - **TTL**: `3600` (or 14400)
   - **Record / Value**:
     ```text
     v=DMARC1; p=none; rua=mailto:info@realtysmartzpathshala.in
     ```
4. Save the record. This establishes domain ownership and aligns your From headers with the SMTP envelope.

### 3. Application Deliverability Rules Enforced in Code
- **Strict Identity Alignment**: The `From` address (`MAIL_FROM_ADDRESS`) strictly equals the authenticated SMTP username (`MAIL_USERNAME`), preventing SPF envelope misalignment flags.
- **Multipart MIME Structure**: Every email is generated simultaneously with high-fidelity semantic HTML and a clean plain-text alternative (`AltBody`).
- **Subject Line Discipline**: Subject lines are concise and informative (e.g. *"Your Pathshala verification code"*). No spam-trigger keywords like "FREE", "100%", or excessive punctuation ("!!!").
- **Asset Self-Hosting**: All embedded logos and assets are served directly from `https://realtysmartzpathshala.in/` without third-party CDN tracking or URL shorteners.
- **Light Branded Layout**: No black backgrounds. Uses a clean light container (`#EEF1F6` outer canvas with `#FFFFFF` card and `#3447E6` primary accents).

### 4. Admin Diagnostic Tool & Logging
- **Live Test Dispatch**: Navigate to **Admin Panel &gt; Site Content &amp; CMS &gt; Email &amp; SMTP** tab (`/admin/content#emailSettings`). Enter any test recipient address and click **Send Test Email**.
- **Diagnostic Log File**: Complete connection logs, SMTP handshakes, and delivery failure explanations are recorded to:
  ```text
  /storage/logs/mail.log
  ```
- **Spam Score Test**: Send a test email from the Admin Panel to the temporary address provided at [mail-tester.com](https://www.mail-tester.com/) to confirm a 10/10 score.

---

## 11. Administrative Account CLI Management (reset-admin.php)

A secure CLI management tool is provided in `/bin/reset-admin.php` to manage administrative accounts directly from the terminal without accessing the web UI.

> **🔒 Security Architecture:** This script is located completely outside `/public` and can only be executed via the server command line (SSH or local terminal). Passwords are never hardcoded.

### Command Usage

#### Interactive Mode (Recommended):
Simply run the script with PHP. It will prompt for target email and password:
```bash
php bin/reset-admin.php
```

#### Argument Mode:
Pass the administrative email and desired password directly:
```bash
php bin/reset-admin.php admin@realtysmartzpathshala.in "YourStrongPassword123"
```

### What `reset-admin.php` Does:
1. Hashes the new password using `PASSWORD_BCRYPT` with cost 11.
2. Updates or creates the user with:
   - `role = 'super_admin'`
   - `status = 'active'`
   - `email_verified = 1`
   - `is_archived = 0`
3. Clears all rate-limiting lockout records from the `login_attempts` table for that administrative email.
4. Records the administrative intervention to `/storage/logs/auth.log`.

---

## 12. Aptitude Test & Guest Links Module

The application provides a resilient, proctored aptitude testing engine that operates under two workflows:
1. **Registered Candidates**: Take the test as part of their candidate onboarding progression.
2. **Guest Test Candidates (`/test/g/{token}`)**:
   - HR administrators generate tokenized, time-limited assessment links from **Assessments > Guest Links** or directly from any candidate profile.
   - Links can be assigned to an existing candidate or created as open links.
   - Open guest links prompt the candidate for Full Name, Mobile, and Email prior to test entry.
   - Features:
     - 10-minute server-enforced countdown timer.
     - Autosave on every question selection.
     - Mid-test refresh support (resumes seamlessly on the same link without losing answers or time).
     - Automatic submission on timer expiration.
     - Rate-limited, CSRF-protected, and strictly forbidden from accessing any administrative or dashboard routes.

---

## 13. QR Codes Management & Walk-In Registration

Located under **Admin Panel > QR Codes** (`/admin/qr-codes`):
- **QR Code 1 (Full Onboarding Portal)**: Directs applicants to `/register` for complete personal data, KYC document uploads, and aptitude testing.
- **QR Code 2 (Walk-In Aptitude Test)**: Directs applicants to `/aptitude/register` for instant, mobile-first registration without requiring immediate document uploads.
- **Features**:
  - High-resolution SVG downloads.
  - Direct PNG image downloads.
  - Printable branded A4 flyers with clear scanning instructions.
  - Dedicated **Aptitude Test Candidates** admin directory (`/admin/aptitude-candidates`) with a 1-click **Convert to Onboarding** workflow.

---

## 14. Google Sheets Live Sync Integration

Automatic, real-time candidate data synchronization into Google Sheets via `GoogleSheetsService.php`:
- **17 Structured Columns**:
  1. Timestamp
  2. Candidate ID
  3. Full Name
  4. Email
  5. Mobile
  6. Candidate Type (`Onboarding` / `Aptitude-only`)
  7. Source (`Web`, `QR Walk-in`, `Guest Link`)
  8. Registration Date
  9. Aadhaar Number (Masked: `XXXX XXXX 1234`)
  10. PAN Number
  11. Current Status
  12. Aptitude Score (%)
  13. Category Breakdown
  14. Anti-Cheat Flags (Tab switches, Fullscreen exits)
  15. Test Date & Time
  16. Profile URL
  17. Notes / Remarks
- **Supported Modes**:
  - **Webhook Mode (Zero Credential Setup)**: Set `google_sheets_webhook_url` in `app/config/config.php` using Google Apps Script.
  - **Service Account Mode**: Place service account JSON in `storage/credentials/google-sheets-sa.json` and configure `google_sheets_spreadsheet_id`.

---

## 15. Automated Playwright Test Suite

The test suite is located in `/tests` and runs using Playwright:
```bash
# Run all tests
npx playwright test

# Run specific specification
npx playwright test tests/admin_shell_viewports.spec.js
npx playwright test tests/approval_wording_purge.spec.js
npx playwright test tests/candidate_profile_and_pdf.spec.js
npx playwright test tests/guest_link_end_to_end.spec.js
npx playwright test tests/aptitude_qr_and_candidates.spec.js
```

---

&copy; Realty Smartz Pathshala. All rights reserved.
