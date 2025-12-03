# 🚀 Local Setup Guide for VideoChat on XAMPP

This guide will help you set up and run the VideoChat application on your local machine using XAMPP.

## 📋 Prerequisites

Before you begin, make sure you have:

- ✅ **XAMPP** installed (with Apache and MySQL)
  - Download from: https://www.apachefriends.org/
  - Minimum PHP version: 7.4+
  - MySQL version: 5.7+
- ✅ **Modern web browser** (Chrome, Firefox, or Edge recommended)
- ✅ **Camera and microphone** (for video/audio calls)

## 🔧 Step-by-Step Setup

### Step 1: Verify XAMPP Installation

1. Open **XAMPP Control Panel**
2. Start **Apache** service (click "Start" button)
3. Start **MySQL** service (click "Start" button)
4. Both should show green "Running" status

> [!TIP]
> If ports 80 or 443 are already in use, you may need to change Apache's port in the XAMPP config.

### Step 2: Verify Project Location

Your project should be located at:
```
C:\xampp\htdocs\videochat\
```

If it's elsewhere, move it to this location or adjust your Apache configuration.

### Step 3: Create the Database

**Option A: Using the Automated Script (Recommended)**

1. Double-click `setup_database.bat` in the project folder
2. Wait for the script to complete
3. You should see "Database setup complete!" message

**Option B: Manual Setup via phpMyAdmin**

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "SQL" tab at the top
3. Copy and paste the contents of `database.sql` file
4. Click "Go" to execute
5. Verify that `videochat_db` database appears in the left sidebar

**Option C: Manual Setup via Command Line**

```bash
# Navigate to XAMPP MySQL bin directory
cd C:\xampp\mysql\bin

# Login to MySQL (default password is empty)
mysql -u root -p

# Create database
CREATE DATABASE videochat_db;
exit

# Import schema
mysql -u root -p videochat_db < C:\xampp\htdocs\videochat\database.sql
```

### Step 4: Verify Database Configuration

The `config.php` file is already configured for XAMPP defaults:

- **Host:** localhost
- **Username:** root
- **Password:** (empty)
- **Database:** videochat_db
- **Port:** 3306

> [!NOTE]
> If you've changed your MySQL root password, update it in `config.php` on line 7.

### Step 5: Check Directory Permissions

Ensure the `uploads/` directory has write permissions:

1. Right-click on `C:\xampp\htdocs\videochat\uploads` folder
2. Select "Properties"
3. Go to "Security" tab
4. Make sure your user account has "Write" permission

### Step 6: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/videochat`
3. You should see the VideoChat landing page

> [!IMPORTANT]
> For WebRTC video calls to work properly in production, you need HTTPS. For local testing, HTTP is fine, but some browsers may require you to allow camera/microphone permissions explicitly.

## 👤 Creating Your First User

1. Click **"Sign Up"** on the landing page
2. Fill in the registration form:
   - Username (unique)
   - Email address
   - Password (minimum 6 characters)
3. Click **"Sign Up"** button
4. You'll be automatically logged in and redirected to the dashboard

## 🧪 Testing the Application

### Test Basic Functionality

1. **Create two test accounts:**
   - Open the app in a normal browser window
   - Register as User 1 (e.g., "alice")
   - Open the app in an incognito/private window
   - Register as User 2 (e.g., "bob")

2. **Test online status:**
   - Both users should see each other in their user list
   - Status should show as "online" (green indicator)

3. **Test messaging:**
   - Click on a user to open chat
   - Send a message
   - Verify it appears in the other user's window

4. **Test video call:**
   - Click the video call button (📹)
   - Accept camera/microphone permissions when prompted
   - The other user should receive a call notification
   - Accept the call to establish connection

## 🐛 Troubleshooting

### Issue: "Connection failed" error

**Solution:**
- Verify MySQL is running in XAMPP Control Panel
- Check database credentials in `config.php`
- Ensure `videochat_db` database exists in phpMyAdmin

### Issue: Blank page or PHP errors

**Solution:**
- Check Apache error logs: `C:\xampp\apache\logs\error.log`
- Ensure PHP version is 7.4 or higher
- Verify all PHP files have correct syntax

### Issue: "Cannot upload profile picture"

**Solution:**
- Check `uploads/` directory exists
- Verify write permissions on `uploads/` folder
- Check PHP upload settings in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

### Issue: Video calls not connecting

**Solution:**
- Allow camera/microphone permissions in browser
- Use Chrome or Firefox (best WebRTC support)
- Check browser console (F12) for JavaScript errors
- For local testing, HTTP is acceptable, but some features may require HTTPS

### Issue: Users not showing as online

**Solution:**
- Open browser console (F12) and check for errors
- Verify `api/update_status.php` is accessible
- Check that JavaScript is enabled
- Clear browser cache and reload

### Issue: Database import fails

**Solution:**
- Ensure MySQL is running
- Check for syntax errors in `database.sql`
- Try importing tables one at a time
- Verify MySQL user has CREATE and INSERT privileges

## 📁 Important Files and Directories

```
videochat/
├── index.php              # Landing page
├── login.php              # Login page
├── signup.php             # Registration page
├── dashboard.php          # Main app interface
├── call.php               # Video/audio call page
├── config.php             # Database configuration
├── database.sql           # Database schema
├── LOCAL_SETUP.md         # This file
├── api/                   # Backend API endpoints
├── css/                   # Stylesheets
├── js/                    # JavaScript files
└── uploads/               # User profile pictures
```

## 🔒 Security Notes for Local Development

> [!WARNING]
> This setup is for **local development only**. Before deploying to production:
> - Set a strong MySQL root password
> - Enable HTTPS (required for WebRTC)
> - Update `config.php` with production credentials
> - Set proper file permissions (755 for directories, 644 for files)
> - Review security settings in `php.ini`

## 🎯 Next Steps

Once your local setup is working:

1. ✅ Explore the application features
2. ✅ Test video and audio calls
3. ✅ Try the messaging system
4. ✅ Customize the UI in `css/style.css`
5. ✅ Review the code and make improvements
6. ✅ Check out [DEPLOYMENT.md](DEPLOYMENT.md) for production deployment

## 💡 Tips for Development

- **Use browser DevTools** (F12) to debug JavaScript issues
- **Check Apache error logs** for PHP errors
- **Use phpMyAdmin** to inspect database tables and data
- **Test in multiple browsers** for compatibility
- **Keep XAMPP services running** while developing

## 📞 Need Help?

- Check the main [README.md](README.md) for general information
- Review [DEPLOYMENT.md](DEPLOYMENT.md) for deployment guidance
- Look at existing `.md` files for specific troubleshooting guides
- Check browser console for JavaScript errors
- Review Apache error logs for PHP issues

---

**Happy coding! 🎉**

If you encounter any issues not covered here, check the browser console and Apache error logs for more details.
