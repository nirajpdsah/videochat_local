/**
 * Accept incoming call
 */
async function acceptCall() {
    if (!incomingCallData) return;

    // Delete the call-request signal
    try {
        await fetch('api/delete_signal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                from_user_id: incomingCallData.from_user_id
            })
        });
    } catch (error) {
        console.error('Error deleting signal:', error);
    }

    // Update status
    await updateUserStatus('on_call');

    // Close modal
    callModalShown = false;
    document.getElementById('callModal').classList.remove('active');

    // Redirect to call page as receiver (not initiator)
    window.location.href = `call.php?user_id=${incomingCallData.from_user_id}&type=${incomingCallData.call_type}&initiator=false`;
}

/**
 * Reject incoming call
 */
async function rejectCall() {
    if (!incomingCallData) return;

    // Delete the call-request signal
    try {
        await fetch('api/delete_signal.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                from_user_id: incomingCallData.from_user_id
            })
        });
    } catch (error) {
        console.error('Error deleting signal:', error);
    }

    // Close modal
    callModalShown = false;
    document.getElementById('callModal').classList.remove('active');

    // Clear the call data
    incomingCallData = null;
}
