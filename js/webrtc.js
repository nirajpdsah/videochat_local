/**
 * WebRTC Video/Audio Call Implementation
 * Handles peer-to-peer connection between two users
 */

// WebRTC Configuration
const config = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

let localStream = null;
let remoteStream = null;
let peerConnection = null;
let signalingInterval = null;
let isAudioEnabled = true;
let isVideoEnabled = true;

/**
 * Initialize call on page load
 */
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Initializing call...');
    console.log('Call Type:', callType);
    console.log('Is Initiator:', isInitiator);
    
    await startCall();
    
    // Start listening for signals
    signalingInterval = setInterval(checkForSignals, 1000);
});

/**
 * Start the call
 */
async function startCall() {
    try {
        // Get user media (camera/microphone)
        const constraints = {
            audio: true,
            video: callType === 'video' ? {width: 1280, height: 720} : false
        };
        
        localStream = await navigator.mediaDevices.getUserMedia(constraints);
        
        // Display local video
        const localVideo = document.getElementById('localVideo');
        localVideo.srcObject = localStream;
        
        // Hide video info overlay once stream starts
        if (callType === 'video') {
            localStream.getVideoTracks()[0].onended = function() {
                console.log('Video track ended');
            };
        }
        
        // Create peer connection
        createPeerConnection();
        
        // If initiator, create offer
        if (isInitiator) {
            await createOffer();
        }
        
        updateCallStatus('Connected');
        
    } catch (error) {
        console.error('Error starting call:', error);
        alert('Could not access camera/microphone. Please check permissions.');
        endCall();
    }
}

/**
 * Create WebRTC peer connection
 */
function createPeerConnection() {
    peerConnection = new RTCPeerConnection(config);
    
    // Add local tracks to peer connection
    localStream.getTracks().forEach(track => {
        peerConnection.addTrack(track, localStream);
    });
    
    // Handle incoming tracks
    peerConnection.ontrack = (event) => {
        console.log('Received remote track');
        if (!remoteStream) {
            remoteStream = new MediaStream();
            const remoteVideo = document.getElementById('remoteVideo');
            remoteVideo.srcObject = remoteStream;
        }
        remoteStream.addTrack(event.track);
        
        // Hide video info overlay when remote video starts
        document.querySelector('.video-info').style.display = 'none';
    };
    
    // Handle ICE candidates
    peerConnection.onicecandidate = (event) => {
        if (event.candidate) {
            console.log('Sending ICE candidate');
            sendSignal('ice-candidate', event.candidate);
        }
    };
    
    // Handle connection state changes
    peerConnection.onconnectionstatechange = () => {
        console.log('Connection state:', peerConnection.connectionState);
        updateCallStatus(peerConnection.connectionState);
        
        if (peerConnection.connectionState === 'disconnected' || 
            peerConnection.connectionState === 'failed') {
            endCall();
        }
    };
}

/**
 * Create and send offer (initiator only)
 */
async function createOffer() {
    try {
        console.log('Creating offer...');
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        
        // Send offer to remote peer
        sendSignal('offer', offer);
    } catch (error) {
        console.error('Error creating offer:', error);
    }
}

/**
 * Handle incoming offer (receiver only)
 */
async function handleOffer(offer) {
    try {
        console.log('Handling offer...');
        await peerConnection.setRemoteDescription(new RTCSessionDescription(offer));
        
        // Create answer
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(answer);
        
        // Send answer back
        sendSignal('answer', answer);
    } catch (error) {
        console.error('Error handling offer:', error);
    }
}

/**
 * Handle incoming answer (initiator only)
 */
async function handleAnswer(answer) {
    try {
        console.log('Handling answer...');
        await peerConnection.setRemoteDescription(new RTCSessionDescription(answer));
    } catch (error) {
        console.error('Error handling answer:', error);
    }
}

/**
 * Handle incoming ICE candidate
 */
async function handleIceCandidate(candidate) {
    try {
        console.log('Adding ICE candidate...');
        await peerConnection.addIceCandidate(new RTCIceCandidate(candidate));
    } catch (error) {
        console.error('Error adding ICE candidate:', error);
    }
}

/**
 * Send signaling data to server
 */
async function sendSignal(signalType, signalData) {
    try {
        const response = await fetch('api/send_signal.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                to_user_id: remoteUserId,
                signal_type: signalType,
                signal_data: signalData,
                call_type: callType
            })
        });
        
        const data = await response.json();
        if (!data.success) {
            console.error('Failed to send signal:', data.message);
        }
    } catch (error) {
        console.error('Error sending signal:', error);
    }
}

/**
 * Check for incoming signals from server
 */
async function checkForSignals() {
    try {
        const response = await fetch('api/get_signals.php');
        const data = await response.json();
        
        if (data.success && data.signals.length > 0) {
            for (const signal of data.signals) {
                // Only process signals from the remote user we're calling
                if (signal.from_user_id !== remoteUserId) continue;
                
                console.log('Received signal:', signal.signal_type);
                
                switch (signal.signal_type) {
                    case 'offer':
                        await handleOffer(signal.signal_data);
                        break;
                    case 'answer':
                        await handleAnswer(signal.signal_data);
                        break;
                    case 'ice-candidate':
                        await handleIceCandidate(signal.signal_data);
                        break;
                }
            }
        }
    } catch (error) {
        console.error('Error checking signals:', error);
    }
}

/**
 * Toggle audio on/off
 */
function toggleAudio() {
    if (localStream) {
        const audioTrack = localStream.getAudioTracks()[0];
        if (audioTrack) {
            isAudioEnabled = !isAudioEnabled;
            audioTrack.enabled = isAudioEnabled;
            
            const audioIcon = document.getElementById('audioIcon');
            audioIcon.textContent = isAudioEnabled ? '🎤' : '🔇';
            
            const btn = document.getElementById('toggleAudioBtn');
            btn.classList.toggle('active', !isAudioEnabled);
        }
    }
}

/**
 * Toggle video on/off
 */
function toggleVideo() {
    if (localStream) {
        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            isVideoEnabled = !isVideoEnabled;
            videoTrack.enabled = isVideoEnabled;
            
            const videoIcon = document.getElementById('videoIcon');
            videoIcon.textContent = isVideoEnabled ? '📹' : '📷';
            
            const btn = document.getElementById('toggleVideoBtn');
            btn.classList.toggle('active', !isVideoEnabled);
        }
    }
}

/**
 * End the call
 */
function endCall() {
    console.log('Ending call...');
    
    // Stop signaling
    if (signalingInterval) {
        clearInterval(signalingInterval);
    }
    
    // Stop all tracks
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
    }
    
    // Close peer connection
    if (peerConnection) {
        peerConnection.close();
    }
    
    // Update status to online
    fetch('api/update_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({status: 'online'})
    }).then(() => {
        // Redirect back to dashboard
        window.location.href = 'dashboard.php';
    });
}

/**
 * Update call status text
 */
function updateCallStatus(status) {
    const statusEl = document.getElementById('callStatus');
    if (statusEl) {
        let statusText = status;
        
        switch(status) {
            case 'new':
            case 'connecting':
                statusText = 'Connecting...';
                break;
            case 'connected':
                statusText = 'Connected';
                break;
            case 'disconnected':
                statusText = 'Disconnected';
                break;
            case 'failed':
                statusText = 'Connection Failed';
                break;
        }
        
        statusEl.textContent = statusText;
    }
}

/**
 * Handle page unload
 */
window.addEventListener('beforeunload', function() {
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
    }
    if (peerConnection) {
        peerConnection.close();
    }
});