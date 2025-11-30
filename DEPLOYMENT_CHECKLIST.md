# ✅ Railway Deployment Checklist

Use this checklist to ensure everything is set up correctly.

## Pre-Deployment

- [ ] Code pushed to GitHub
- [ ] All files committed
- [ ] `database.sql` file exists
- [ ] `config.php` uses Railway environment variables
- [ ] `railway.json` exists
- [ ] `uploads/` directory exists
- [ ] `default-avatar.png` added to `uploads/` (or will add after deployment)

## Railway Setup

- [ ] Railway account created
- [ ] GitHub connected to Railway
- [ ] New project created
- [ ] Repository connected
- [ ] Web service deployed

## Database Setup

- [ ] MySQL service added
- [ ] MySQL service is running
- [ ] `database.sql` executed successfully
- [ ] Tables created:
  - [ ] `users` table
  - [ ] `signals` table
  - [ ] `messages` table

## Configuration

- [ ] Environment variables auto-set by Railway:
  - [ ] `MYSQLHOST`
  - [ ] `MYSQLUSER`
  - [ ] `MYSQLPASSWORD`
  - [ ] `MYSQLDATABASE`
  - [ ] `MYSQLPORT`
- [ ] Web service start command: `php -S 0.0.0.0:$PORT -t .`
- [ ] Port configured automatically

## Testing

- [ ] App URL accessible (HTTPS enabled)
- [ ] Landing page loads
- [ ] User registration works
- [ ] User login works
- [ ] Dashboard loads
- [ ] User list displays
- [ ] Profile picture upload works
- [ ] Chat functionality works
- [ ] Video call initiates
- [ ] Audio call initiates
- [ ] WebRTC connection establishes

## Post-Deployment

- [ ] Default avatar uploaded
- [ ] Tested with 2+ users
- [ ] All features working
- [ ] No errors in logs
- [ ] Performance acceptable
- [ ] Custom domain configured (optional)

## Troubleshooting (if needed)

- [ ] Checked Railway logs
- [ ] Verified database connection
- [ ] Tested HTTPS is working
- [ ] Checked browser console
- [ ] Verified file permissions

---

**Status:** ⬜ Not Started | 🟡 In Progress | ✅ Complete

**Notes:**
_________________________________________________
_________________________________________________
_________________________________________________

