# 🎥 VideoChat - WebRTC Video Calling Application

A modern, real-time video chat application built with PHP, MySQL, and WebRTC. Connect with friends and family through high-quality video calls, audio calls, and instant messaging.

## ✨ Features

- 📹 **Video Calls** - High-quality peer-to-peer video calls
- 📞 **Audio Calls** - Voice-only calls when you're on the go
- 💬 **Instant Messaging** - Real-time chat with other users
- 👥 **User Management** - Registration, login, and user profiles
- 🟢 **Online Status** - See who's online, offline, or on a call
- 🖼️ **Profile Pictures** - Custom profile pictures for each user
- 🔒 **Secure** - HTTPS enabled, password hashing, SQL injection protection
- 📱 **Responsive** - Works on desktop and mobile devices

## 🚀 Quick Start

### Local Development

1. **Requirements:**
   - PHP 7.4+ (or XAMPP/WAMP)
   - MySQL 5.7+
   - Web server (Apache/Nginx) or PHP built-in server

2. **Installation:**
   ```bash
   # Clone the repository
   git clone https://github.com/YOUR_USERNAME/videochat.git
   cd videochat
   
   # Create database
   mysql -u root -p
   CREATE DATABASE videochat_db;
   exit
   
   # Import database schema
   mysql -u root -p videochat_db < database.sql
   
   # Configure database
   # Edit config.php with your database credentials
   
   # Start PHP server
   php -S localhost:8000
   ```

3. **Access:**
   - Open browser: `http://localhost:8000`
   - Register a new account
   - Start chatting!

### Production Deployment

See [RAILWAY_SETUP.md](RAILWAY_SETUP.md) for detailed Railway deployment instructions.

**Quick Deploy to Railway:**
1. Push code to GitHub
2. Connect Railway to your repo
3. Add MySQL service
4. Run `database.sql`
5. Deploy! 🎉

## 📁 Project Structure

```
videochat/
├── index.php          # Landing page
├── signup.php         # User registration
├── login.php          # User login
├── logout.php         # Logout handler
├── dashboard.php      # Main app interface
├── call.php           # Video/audio call page
├── config.php         # Database & config
├── database.sql       # Database schema
├── api/               # API endpoints
│   ├── register.php
│   ├── authenticate.php
│   ├── get_users.php
│   ├── update_status.php
│   ├── send_signal.php
│   ├── get_signals.php
│   └── messages.php
├── css/
│   └── style.css      # Stylesheet
├── js/
│   ├── dashboard.js   # Dashboard functionality
│   └── webrtc.js      # WebRTC implementation
└── uploads/            # Profile pictures
    └── default-avatar.png
```

## 🗄️ Database Schema

The application uses three main tables:

- **users** - User accounts and profiles
- **signals** - WebRTC signaling data
- **messages** - Chat messages

Run `database.sql` to create all tables.

## 🔧 Configuration

### Database Configuration

Edit `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'videochat_db');
```

### Railway Deployment

Railway automatically sets environment variables:
- `MYSQLHOST`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`
- `MYSQLPORT`

Your `config.php` is already configured for Railway! ✅

## 🌐 WebRTC Requirements

- **HTTPS is mandatory** for WebRTC (Railway provides this automatically)
- Modern browser (Chrome, Firefox, Edge, Safari)
- Camera and microphone permissions
- STUN servers (Google's public STUN servers are used)

## 📝 API Endpoints

### Authentication
- `POST /api/register.php` - Register new user
- `POST /api/authenticate.php` - Login user

### Users
- `GET /api/get_users.php` - Get all users
- `POST /api/update_status.php` - Update user status

### WebRTC Signaling
- `POST /api/send_signal.php` - Send WebRTC signal
- `GET /api/get_signals.php` - Get pending signals

### Messages
- `GET /api/messages.php?user_id=X` - Get messages with user
- `POST /api/messages.php` - Send message

## 🛠️ Technologies Used

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **WebRTC:** Peer-to-peer video/audio communication
- **Security:** Password hashing, prepared statements, HTTPS

## 🔒 Security Features

- Password hashing (bcrypt)
- SQL injection protection (prepared statements)
- XSS protection (input sanitization)
- HTTPS enforcement
- Session management
- CSRF protection ready

## 📱 Browser Support

- ✅ Chrome/Edge (recommended)
- ✅ Firefox
- ✅ Safari (iOS 11+)
- ⚠️ Older browsers may have limited WebRTC support

## 🐛 Troubleshooting

### Video calls not working?
- Ensure HTTPS is enabled
- Check browser console for errors
- Verify camera/microphone permissions
- Test in Chrome or Firefox

### Database connection errors?
- Verify database credentials in `config.php`
- Ensure MySQL service is running
- Check database exists and tables are created

### File upload issues?
- Check `uploads/` directory permissions (755 or 777)
- Verify PHP upload settings
- Ensure sufficient disk space

## 📄 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

For issues and questions:
- Check [DEPLOYMENT.md](DEPLOYMENT.md) for deployment help
- Review [RAILWAY_SETUP.md](RAILWAY_SETUP.md) for Railway-specific setup
- Open an issue on GitHub

## 🎯 Roadmap

- [ ] Group video calls
- [ ] Screen sharing
- [ ] File sharing in chat
- [ ] Push notifications
- [ ] Mobile app (React Native)
- [ ] End-to-end encryption
- [ ] Call recording

---

**Made with ❤️ for connecting people**

