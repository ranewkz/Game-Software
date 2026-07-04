// document.getElementById('profileForm').addEventListener('submit', function(e) {
//     e.preventDefault(); 
    
//     const pass1 = document.getElementById('opPass').value;
//     const pass2 = document.getElementById('opPassConfirm').value;

//     if (pass1 !== pass2) {
//         showNotification('> ERROR: SECURITY KEYS DO NOT MATCH', '#ff003c');
//         return;
//     }

//     const btn = this.querySelector('.btn-primary');
//     const originalText = btn.innerText;
//     btn.innerText = 'UPLOADING...';
//     btn.style.opacity = '0.7';

//     setTimeout(() => {
//         btn.innerText = originalText;
//         btn.style.opacity = '1';
//         showNotification('> SYSTEM_OVERRIDE_SUCCESSFUL', '#00f2ff');
//         document.getElementById('opPass').value = '';
//         document.getElementById('opPassConfirm').value = '';
//     }, 800);
// });

const avatarTrigger = document.getElementById('avatarTrigger');
const avatarUpload = document.getElementById('avatarUpload');

avatarTrigger.addEventListener('click', () => { 
    avatarUpload.click(); 
});

avatarUpload.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        showNotification('> NEW_AVATAR_ACQUIRED. AWAITING SAVE.', '#ff00ff');
        avatarTrigger.style.background = 'rgba(255, 0, 255, 0.2)';
        avatarTrigger.style.borderColor = '#ff00ff';
    }
});

document.getElementById('btnPurge').addEventListener('click', function() {
    const confirmation = confirm("// WARNING // \nAre you absolutely certain you wish to purge this profile? This will sever your Comm-Link permanently.");
    
    if(confirmation) {
        showNotification('> INITIATING DATA PURGE...', '#ff003c');
        setTimeout(() => {
            document.body.innerHTML = '<h1 style="color:#ff003c; text-align:center; margin-top:20vh; font-family:Courier New;">CONNECTION TERMINATED.</h1>';
        }, 1500);
    }
});

function showNotification(message, color) {
    const notif = document.getElementById('sys-notification');
    notif.innerText = message;
    notif.style.color = color;
    notif.style.borderLeftColor = color;
    notif.classList.add('show');
    setTimeout(() => { 
        notif.classList.remove('show'); 
    }, 3000);
}