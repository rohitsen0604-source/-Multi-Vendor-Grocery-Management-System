# Multi-Vendor Online Grocery Management System (MVOGMS)

A web-based multi-vendor grocery management platform built with PHP and MySQL.

---

## 🚀 Deployment Guide for Render

### Step 1: Set Up a Free MySQL Database
Render does not host free MySQL instances directly, but you can use any free MySQL cloud provider:
- **[TiDB Cloud (Serverless)](https://tidbcloud.com/)** (Recommended: 100% free, 25GB, MySQL 8.0 compatible)
- **[Aiven for MySQL](https://aiven.io/)** (Free tier)
- **[Clever Cloud](https://www.clever-cloud.com/)** (Free MySQL addon)
- **[Railway](https://railway.app/)**

Once created, obtain your database connection details:
- **Host**: e.g., `gateway01.us-east-1.prod.aws.tidbcloud.com`
- **Port**: e.g., `4000` (or `3306`)
- **User**: e.g., `xxxx.root`
- **Password**: `your_password`
- **Database**: e.g., `test` or `mvogms_db`

> **Note:** The application includes an **auto-seeding feature**. When connecting to an empty database for the first time, it will automatically import tables and initial data from `database/mvogms_db.sql`.

---

### Step 2: Deploy on Render

1. Log in to [Render Dashboard](https://dashboard.render.com/).
2. Click **New +** > **Web Service**.
3. Connect your GitHub repository (`-Multi-Vendor-Grocery-Management-System`).
4. Configure the service:
   - **Name**: `grocery-management-system` (or your choice)
   - **Language / Runtime**: **Docker**
   - **Region**: Choose closest to you (e.g., Singapore, Frankfurt, Oregon)
   - **Branch**: `main`
   - **Instance Type**: **Free**
5. Add Environment Variables under **Environment**:
   | Key | Value | Description |
   |-----|-------|-------------|
   | `DB_HOST` | `your-db-host` | Database hostname |
   | `DB_PORT` | `3306` (or `4000` for TiDB) | Port |
   | `DB_USER` | `your-db-user` | Database user |
   | `DB_PASSWORD` | `your-db-password` | Database password |
   | `DB_NAME` | `your-db-name` | Database name |
   | `DB_SSL` | `true` (if cloud DB requires SSL) | Enable SSL (optional) |

   *(Alternatively, set a single `DATABASE_URL` like `mysql://user:pass@host:port/dbname`)*

6. Click **Create Web Service**. Render will automatically build the Docker image and deploy your application!

---

## 🔑 Default Login Credentials

- **Admin Portal**: `/admin`
  - **Username**: `admin`
  - **Password**: `admin123`
- **Vendor Portal**: `/vendor`
  - **Username**: `cblake`
  - **Password**: `admin123`
