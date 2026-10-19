# Realty Smartz Pathshala - Production Deployment Guide

This guide walks you through deploying the project to **GitHub** and taking it live on **cPanel**.

---

## Part 1: Push Project to GitHub

### 1. Initialize Git & Create First Commit (Local Machine)
In your terminal / PowerShell inside the project directory (`d:\Realty Smartz\Realty Pathsala`):

```bash
# 1. Initialize git
git init

# 2. Stage all project files (sensitive .env, uploads, and logs are automatically excluded by .gitignore)
git add .

# 3. Commit your code
git commit -m "feat: complete Realty Smartz Pathshala portal with candidate management, assessments, and cPanel configuration"
```

### 2. Connect to your GitHub Repository
Create a new repository on [GitHub](https://github.com/new) (e.g., `realty-smartz-pathsala`). Ensure **"Add a README file"** is **unchecked**.

Then run:
```bash
# Set primary branch to main
git branch -M main

# Add your GitHub remote repository (replace repository name if different)
git remote add origin https://github.com/realtysmartz-byte/realty-smartz-pathsala.git

# Push your code to GitHub
git push -u origin main
```

---

## Part 2: Deploy to cPanel (Live Website)

You can choose either **Option A (Automated via Git Version Control - Recommended)** or **Option B (File Manager / ZIP Upload)**.

---

### Option A: Automated Git Version Control (Recommended)

This project includes a ready-to-use `.cpanel.yml` file that automates separating core app files (`pathshala_app/`) from publicly accessible web assets (`public_html/`).

1. **Log in to cPanel** (e.g. `https://43.242.226.197:2083` or `https://realtysmartzpathshala.in:2083`).
2. Navigate to **Files** &rarr; **Git™ Version Control**.
3. Click **Create**:
   - **Clone a Repository**: Toggle to ON.
   - **Clone URL**: Paste your GitHub URL (e.g., `https://github.com/YOUR_GITHUB_USERNAME/realty-smartz-pathsala.git`).
   - **Repository Path**: Enter `repositories/realty-pathsala`.
   - **Repository Name**: `realty-pathsala`.
4. Click **Create**.
5. Once cloned, click **Manage** on the repository, navigate to the **Deploy HEAD Commit** tab, and click **Deploy HEAD Commit**.
   - `.cpanel.yml` will automatically copy your public assets and front controller to `public_html/` and application code to `/home/realtys1/pathshala_app/`.

---

### Option B: Direct File Manager / ZIP Upload

If you prefer uploading manually:
1. Create a zip of your project files (excluding `node_modules` and local `.env`).
2. In cPanel **File Manager**, open `public_html/`.
3. Upload and extract the zip file directly into `public_html/`.
4. The included root `.htaccess` will automatically and securely route all visitor traffic into `/public/` while strictly protecting your database and application code from direct web access.

---

## Part 3: Import Database in cPanel phpMyAdmin

A complete, clean, production-ready database dump is pre-built at:
`database/cpanel_full_db.sql`

1. In cPanel, go to **Databases** &rarr; **MySQL® Databases**.
2. Create your database: `realtys1_pathsalanew` (or verify it exists).
3. Create your user: `realtys1_abhishek` and assign it to `realtys1_pathsalanew` with **ALL PRIVILEGES**.
4. In cPanel, open **phpMyAdmin** from the Databases section.
5. Select the database `realtys1_pathsalanew` on the left sidebar.
6. Click the **Import** tab at the top.
7. Click **Choose File** and select `database/cpanel_full_db.sql` from your local machine.
8. Click **Import** at the bottom.

---

## Part 4: Configure Live `.env` File

In cPanel **File Manager**:
1. Navigate to your app directory (`/home/realtys1/pathshala_app/` if using Option A, or `public_html/` if using Option B).
2. Enable **"Show Hidden Files (dotfiles)"** in File Manager Settings (top right gear icon).
3. Create a new file named `.env` and paste your production credentials:

```dotenv
APP_NAME="Realty Smartz Pathshala"
APP_ENV=production
APP_DEBUG=false
# Use your server IP or domain:
APP_URL=http://43.242.226.197
APP_KEY=c2VjdXJlX2FlczI1Nl9rZXlfcmVhbHR5c21hcnR6cGF0aHNoYWxhMjAyNg==

# Database Credentials (Localhost inside cPanel)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=realtys1_pathsalanew
DB_USER=realtys1_abhishek
DB_PASS=@Realtypathsala2233
DB_CHARSET=utf8mb4

# Production SMTP Email Server
MAIL_DRIVER=smtp
MAIL_HOST=mail.realtysmartzpathshala.in
MAIL_PORT=465
MAIL_USERNAME=info@realtysmartzpathshala.in
MAIL_PASSWORD=@Realtypathsala2233
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=info@realtysmartzpathshala.in
MAIL_FROM_NAME="Realty Smartz Pathshala"

# Security & Sessions (set to false to allow login on both HTTP IP and HTTPS domain)
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false
```

> **Important Notes:**
> - When running *on* the live cPanel server, always set `DB_HOST=localhost`.
> - If accessing via IP `http://43.242.226.197`, keep `SESSION_SECURE_COOKIE=false` so the browser does not reject HTTP session cookies. When you install an SSL certificate on your domain, you can set it to `true`.
> - The application's `url()` helper dynamically detects whether you are accessing via `http://43.242.226.197` or `https://realtysmartzpathshala.in` and serves all assets and links matching your active address automatically.

---

## Part 5: Verify Live Website

1. **Public Site**: Open `http://43.242.226.197` (or `https://realtysmartzpathshala.in`)
   - Test navigation (Home, About, Projects, Blog, Contact).
   - Verify borderless images and authentic social media brand icons in the footer.
2. **Candidate Onboarding**: Open `http://43.242.226.197/register`
   - Test candidate registration, email OTP, and document submission.
3. **Admin Portal**: Open `http://43.242.226.197/admin/login`
   - Log in with your Super Admin credentials (`admin@realtysmartzpathshala.in` / `Admin@2026`).
   - Verify candidates directory, joining date editing, deletion, and assessment reports.
