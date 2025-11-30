# 🚂 Railway Deployment Guide - Step by Step

## Prerequisites
- GitHub account
- Railway account (sign up at [railway.app](https://railway.app))

---

## Step 1: Prepare Your Code

### 1.1 Push to GitHub
```bash
# If you haven't already, initialize git and push to GitHub
git init
git add .
git commit -m "Initial commit - VideoChat app"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/videochat.git
git push -u origin main
```

### 1.2 Verify Files
Make sure these files exist in your repository:
- ✅ `config.php` (with Railway env vars)
- ✅ `database.sql` (database schema)
- ✅ `railway.json` (Railway config)
- ✅ All PHP files
- ✅ `uploads/` directory (with `.gitkeep` or `default-avatar.png`)

---

## Step 2: Create Railway Account

1. Go to [railway.app](https://railway.app)
2. Click **"Start a New Project"**
3. Sign up with GitHub (recommended) or email
4. Authorize Railway to access your GitHub repositories

---

## Step 3: Create New Project

1. Click **"New Project"** in Railway dashboard
2. Select **"Deploy from GitHub repo"**
3. Choose your `videochat` repository
4. Railway will start deploying automatically

---

## Step 4: Add MySQL Database

1. In your Railway project dashboard, click **"+ New"**
2. Select **"Database"** → **"Add MySQL"**
3. Railway will create a MySQL database service
4. **Important:** Note the service name (e.g., "MySQL")

---

## Step 5: Configure Environment Variables

Railway automatically sets these for MySQL:
- `MYSQLHOST`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`
- `MYSQLPORT`

**Your `config.php` already uses these!** ✅

### To verify:
1. Click on your **Web Service** (not MySQL)
2. Go to **"Variables"** tab
3. You should see all MySQL variables automatically set

---

## Step 6: Set Up Database Tables

### Option A: Using Railway MySQL Console (Recommended)

1. Click on your **MySQL service** in Railway
2. Go to **"Data"** tab
3. Click **"Open MySQL Console"** or **"Query"**
4. Copy and paste the entire contents of `database.sql`
5. Click **"Run"** or execute the query

### Option B: Using MySQL Client

1. In MySQL service, go to **"Connect"** tab
2. Copy the connection details
3. Use a MySQL client (like MySQL Workbench, phpMyAdmin, or command line):
   ```bash
   mysql -h MYSQLHOST -u MYSQLUSER -p MYSQLDATABASE < database.sql
   ```

### Option C: Using Railway CLI

```bash
# Install Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link to your project
railway link

# Run SQL file
railway run mysql < database.sql
```

---

## Step 7: Configure Web Service

1. Click on your **Web Service** (the PHP app)
2. Go to **"Settings"** tab
3. Configure:

   **Build Command:** (leave empty or use)
   ```bash
   # Railway auto-detects PHP, but you can specify:
   echo "PHP detected"
   ```

   **Start Command:**
   ```bash
   php -S 0.0.0.0:$PORT
   ```

4. **Port:** Railway sets `$PORT` automatically
5. **Root Directory:** Leave empty (or set to `/` if needed)

---

## Step 8: Add Default Avatar

1. Make sure `uploads/` directory exists
2. Add a `default-avatar.png` file to `uploads/`
3. Commit and push:
   ```bash
   git add uploads/default-avatar.png
   git commit -m "Add default avatar"
   git push
   ```

**OR** use Railway's file system:
- Railway provides persistent storage
- Files in `uploads/` will persist between deployments

---

## Step 9: Deploy

Railway auto-deploys on every git push!

1. Make sure all changes are committed:
   ```bash
   git add .
   git commit -m "Ready for deployment"
   git push
   ```

2. Railway will automatically:
   - Build your app
   - Deploy it
   - Assign a URL (e.g., `videochat-production.up.railway.app`)

3. Check deployment status in Railway dashboard

---

## Step 10: Get Your App URL

1. In your Web Service, go to **"Settings"**
2. Scroll to **"Domains"**
3. Your app is available at: `your-app-name.up.railway.app`
4. **HTTPS is automatically enabled!** ✅

---

## Step 11: Test Your Application

1. Visit your Railway URL
2. Test registration:
   - Create a new account
   - Verify it works

3. Test login:
   - Log in with your account

4. Test video call:
   - Open in two different browsers/incognito windows
   - Create two accounts
   - Start a video call

---

## Troubleshooting

### Database Connection Error
**Problem:** "Connection failed"
**Solution:**
- Verify MySQL service is running
- Check environment variables are set
- Ensure database tables are created

### 404 Errors
**Problem:** Pages not found
**Solution:**
- Check `railway.json` configuration
- Verify `index.php` exists in root
- Check start command is correct

### WebRTC Not Working
**Problem:** Video calls don't connect
**Solution:**
- Verify HTTPS is enabled (Railway does this automatically)
- Check browser console for errors
- Ensure STUN servers are accessible
- Test in Chrome/Firefox (best WebRTC support)

### File Upload Issues
**Problem:** Profile pictures not uploading
**Solution:**
- Check `uploads/` directory permissions
- Verify PHP upload settings
- Check Railway storage limits

### App Not Starting
**Problem:** Service crashes
**Solution:**
- Check logs in Railway dashboard
- Verify PHP version compatibility
- Check start command syntax

---

## Monitoring & Logs

### View Logs
1. Click on your Web Service
2. Go to **"Deployments"** tab
3. Click on a deployment
4. View **"Logs"** for real-time output

### Check Status
- Green dot = Running
- Yellow dot = Building
- Red dot = Error

---

## Custom Domain (Optional)

1. In Web Service → Settings → Domains
2. Click **"Generate Domain"** (free Railway domain)
3. Or add your own custom domain:
   - Click **"Custom Domain"**
   - Add your domain
   - Update DNS records as instructed

---

## Cost Management

Railway Free Tier:
- **$5 credit/month**
- Usually enough for small projects
- Monitor usage in dashboard

To avoid charges:
- Use only free tier resources
- Monitor usage regularly
- Set up usage alerts

---

## Next Steps

✅ Your app is live!
- Share the URL with users
- Test all features
- Monitor performance
- Consider upgrading if you need more resources

---

## Quick Reference

**Railway Dashboard:** [railway.app](https://railway.app)
**Documentation:** [docs.railway.app](https://docs.railway.app)
**Support:** Check Railway Discord or docs

---

## Common Commands

```bash
# View logs
railway logs

# Open shell
railway shell

# Run commands
railway run php -v

# Check status
railway status
```

---

**Need Help?** Check Railway docs or their Discord community!

