# Deployment Guide for VideoChat

## 🚀 Railway Deployment (Recommended)

### Step 1: Prepare Your Repository
1. Push your code to GitHub
2. Make sure `database.sql` is in the root directory

### Step 2: Set Up Railway
1. Go to [railway.app](https://railway.app) and sign up
2. Click "New Project" → "Deploy from GitHub repo"
3. Select your repository

### Step 3: Add MySQL Database
1. In your Railway project, click "+ New"
2. Select "Database" → "MySQL"
3. Railway will automatically create the database

### Step 4: Run Database Setup
1. Go to your MySQL service in Railway
2. Click on "Connect" tab
3. Copy the connection details
4. Use Railway's MySQL console or a MySQL client to run `database.sql`

### Step 5: Configure Environment Variables
Railway automatically sets these for MySQL:
- `MYSQLHOST`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`
- `MYSQLPORT`

### Step 6: Deploy
1. Railway will auto-deploy on git push
2. Your app will be available at `your-app.railway.app`
3. HTTPS is automatically enabled

---

## 🌐 Render Deployment

### Step 1: Create Account
1. Sign up at [render.com](https://render.com)

### Step 2: Create Web Service
1. Click "New" → "Web Service"
2. Connect your GitHub repository
3. Settings:
   - **Name**: videochat
   - **Environment**: PHP
   - **Build Command**: (leave empty)
   - **Start Command**: `php -S 0.0.0.0:$PORT`

### Step 3: Add PostgreSQL Database
1. Click "New" → "PostgreSQL"
2. Note: You'll need to adapt your code for PostgreSQL or use MySQL addon

### Step 4: Set Environment Variables
Add these in the Environment tab:
- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`
- `DB_PORT`

---

## 📦 InfinityFree Deployment

### Step 1: Sign Up
1. Go to [infinityfree.net](https://infinityfree.net)
2. Create a free account

### Step 2: Create Website
1. Go to Control Panel
2. Click "Create Account"
3. Choose a subdomain (e.g., `yourname.infinityfreeapp.com`)

### Step 3: Upload Files
1. Use File Manager or FTP
2. Upload all your files to `htdocs` directory

### Step 4: Create Database
1. Go to MySQL Databases in Control Panel
2. Create a new database
3. Create a user and assign to database
4. Run `database.sql` using phpMyAdmin

### Step 5: Update Config
Edit `config.php` with your database credentials

---

## ⚠️ Important Notes

### WebRTC Requirements
- **HTTPS is mandatory** for WebRTC to work
- All free hosting platforms provide free SSL certificates
- Make sure your site is accessed via HTTPS

### File Uploads
- Ensure `uploads/` directory has write permissions (755 or 777)
- Add `default-avatar.png` to the uploads folder
- Some hosts may have upload size limits

### Performance
- Free tiers have resource limitations
- For production use, consider paid hosting
- Monitor your usage to avoid hitting limits

### Database
- Run `database.sql` to create all tables
- Make sure foreign key constraints are enabled
- Regular backups recommended

---

## 🔧 Troubleshooting

### WebRTC Not Working
- Check browser console for errors
- Ensure HTTPS is enabled
- Verify STUN servers are accessible
- Check firewall/network restrictions

### Database Connection Errors
- Verify environment variables are set correctly
- Check database credentials
- Ensure database is running
- Test connection with a simple PHP script

### File Upload Issues
- Check directory permissions
- Verify upload_max_filesize in PHP settings
- Check disk space quota

---

## 📝 Post-Deployment Checklist

- [ ] Database tables created
- [ ] Default avatar image uploaded
- [ ] HTTPS enabled and working
- [ ] Test user registration
- [ ] Test login functionality
- [ ] Test video call between two users
- [ ] Test chat functionality
- [ ] Verify file uploads work
- [ ] Check error logs for issues

---

## 🆘 Support

For issues:
1. Check Railway/Render/InfinityFree documentation
2. Review application logs
3. Test locally first
4. Check browser console for errors

