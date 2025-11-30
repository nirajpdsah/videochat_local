# Call Notification Bug Fix

## Problem
When User A initiates a call, their screen shows "connected" but User B (the receiver) doesn't get any notification or ring.

## Root Cause Analysis

After analyzing the code, I've identified **two main issues**:

### Issue 1: Call-Request Signals Not Being Cleaned Up
- When a call is initiated, a `call-request` signal is created in the database
- This signal is marked with `is_read = 0` to keep it visible to the receiver
- However, when the receiver accepts or rejects the call, the signal is **NEVER deleted**
- This causes old signals to accumulate in the database

### Issue 2: Signal Cleanup Missing in AcceptCall/RejectCall Functions
- In `js/dashboard.js`, the `acceptCall()` and `rejectCall()` functions don't delete the signal after handling it
- This means the signal stays in the database indefinitely

## Files Created/Modified

### 1. Created: `api/delete_signal.php`
New API endpoint to delete call-request signals. This endpoint:
- Takes a `from_user_id` parameter
- Deletes all `call-request` signals from that user to the current user
- Returns success/failure status

### 2. Modified: `js/dashboard.js`

#### Changes to `acceptCall()`:
Added signal cleanup before redirecting:
```javascript
// Delete the call-request signal
try {
    await fetch('api/delete_signal.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            from_user_id: incomingCallData.from_user_id
        })
    });
} catch (error) {
    console.error('Error deleting signal:', error);
}
```

#### Changes to `rejectCall()`:
Added the same signal cleanup:
```javascript  
// Delete the call-request signal
try {
    await fetch('api/delete_signal.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            from_user_id: incomingCallData.from_user_id
        })
    });
} catch (error) {
    console.error('Error deleting signal:', error);
}
```

### 3. Created: `debug_signals.php`
Debugging tool to view all signals in the database. Visit this page to see:
- All incoming signals (to you)
- All outgoing signals (from you)
- Signal status (read/unread)
- Live testing of the `get_signals.php` API

## How to Apply the Fix

### Step 1: Apply the changes to `js/dashboard.js`

Find the `acceptCall()` function (around line 411) and add the signal deletion code BEFORE the status update:

```javascript
async function acceptCall() {
    if (!incomingCallData) return;
    
    // DELETE THE CALL-REQUEST SIGNAL - ADD THIS CODE
    try {
        await fetch('api/delete_signal.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                from_user_id: incomingCallData.from_user_id
            })
        });
    } catch (error) {
        console.error('Error deleting signal:', error);
    }
    // END OF ADDED CODE
    
    // Update status
    await updateUserStatus('on_call');
    
    // Close modal
    callModalShown = false;
    document.getElementById('callModal').classList.remove('active');
    
    // Redirect to call page as receiver (not initiator)
    window.location.href = `call.php?user_id=${incomingCallData.from_user_id}&type=${incomingCallData.call_type}&initiator=false`;
}
```

Find the `rejectCall()` function (around line 428) and add the same signal deletion code:

```javascript
async function rejectCall() {
    if (!incomingCallData) return;
    
    // DELETE THE CALL-REQUEST SIGNAL - ADD THIS CODE
    try {
        await fetch('api/delete_signal.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                from_user_id: incomingCallData.from_user_id
            })
        });
    } catch (error) {
        console.error('Error deleting signal:', error);
    }
    // END OF ADDED CODE
    
    // Close modal
    callModalShown = false;
    document.getElementById('callModal').classList.remove('active');
    
    // Clear the call data
    incomingCallData = null;
}
```

### Step 2: Verify the fix works

1. Make sure all files are deployed (especially `api/delete_signal.php`)
2. Clear your browser cache (Ctrl+Shift+R)
3. Open the application in two different browsers or incognito windows
4. Login as two different users
5. Have User A initiate a call to User B
6. User B should see the incoming call notification
7. Accept or reject the call
8. Check `debug_signals.php` to verify the signal was deleted

## Debugging Steps

If the issue persists:

1. **Check the browser console** (F12) on both users' browsers
   - Look for errors related to `send_call_request.php`
   - Look for errors related to `get_signals.php`

2. **Visit `debug_signals.php`** to see all signals:
   - Check if call-request signals are being created
   - Check if they're being received by the other user
   - Check if they're being deleted after accept/reject

3. **Check the database**:
   - Run: `SELECT * FROM signals WHERE signal_type = 'call-request' ORDER BY created_at DESC;`
   - Verify that call-request signals exist
   - Verify that `call_type` column exists and has correct values

4. **Look for console logs**:
   - The receiver's browser should show: `[CALL REQUEST] Detected call request from: [username]`
   - If this doesn't appear, the signal isn't being retrieved properly

## Common Issues

### Issue: "Failed to send call request"
- **Solution**: Run `database_update.sql` to add `call-request` to the signal_type ENUM

### Issue: Receiver doesn't see notification but signal exists in database
- **Solution**: Check browser console for JavaScript errors
- Make sure `checkForIncomingCalls()` is running (check console logs)

### Issue: Multiple notifications for the same call
- **Solution**: This fix should prevent this by deleting signals after they're handled

## Testing Checklist

- [ ] `api/delete_signal.php` file created
- [ ] `js/dashboard.js` updated with signal cleanup code
- [ ] Browser cache cleared
- [ ] Tested with two users
- [ ] Call notification appears for receiver
- [ ] Signal is deleted after accept
- [ ] Signal is deleted after reject
- [ ] No error messages in browser console

## Expected Behavior After Fix

1. ✅ User A clicks video/audio call button
2. ✅ Call request signal created in database
3. ✅ User B sees "Incoming Call" modal within 1 second
4. ✅ User B accepts or rejects
5. ✅ Signal is immediately deleted from database
6. ✅ No duplicate notifications

This fix ensures proper cleanup of call-request signals and should resolve the notification issue!
