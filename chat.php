<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan - Panenly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .chat-container {
            max-width: 1000px;
            margin: 2rem auto;
            display: flex;
            height: 70vh;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .chat-sidebar {
            width: 300px;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        .chat-list {
            flex: 1;
            overflow-y: auto;
        }
        .chat-user {
            display: flex;
            gap: 10px;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
        }
        .chat-user:hover, .chat-user.active {
            background: rgba(16, 185, 129, 0.05);
        }
        .chat-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }
        .chat-messages {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background: var(--bg-color);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .msg {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
        }
        .msg.received {
            background: var(--white);
            align-self: flex-start;
            border-bottom-left-radius: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .msg.sent {
            background: var(--primary-color);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 0;
        }
        .chat-input {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            gap: 10px;
        }
        .chat-input input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            outline: none;
            font-family: inherit;
        }
        .chat-input input:focus {
            border-color: var(--primary-color);
        }
        .btn-send {
            background: var(--primary-color);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        .btn-send:hover {
            background: var(--primary-dark);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <i class="fa-solid fa-leaf"></i> Panenly
        </a>
    </nav>
    <div style="padding: 0 5%;">
        <div class="chat-container">
            <div class="chat-sidebar">
                <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); font-weight: 600;">
                    Pesan Masuk
                </div>
                <div class="chat-list">
                    <div class="chat-user active">
                        <img src="https://ui-avatars.com/api/?name=Pak+Tono&background=10b981&color=fff">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">Pak Tono</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">Terima kasih ya pak sudah belanja</div>
                        </div>
                    </div>
                    <div class="chat-user">
                        <img src="https://ui-avatars.com/api/?name=Bu+Siti&background=e5e7eb">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">Bu Siti</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">Besok wortelnya siap kirim bu</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chat-main">
                <div class="chat-header">
                    <img src="https://ui-avatars.com/api/?name=Pak+Tono&background=10b981&color=fff" style="width:35px; border-radius:50%;">
                    Pak Tono
                </div>
                <div class="chat-messages" id="messageBox">
                    <div class="msg received">
                        Halo bapak, apakah stok apel fuji petik langsung masih ada 2 kg?
                    </div>
                    <div class="msg sent">
                        Masih pak, apelnya baru dipanen pagi ini. Sangat segar! Silakan langsung checkout saja.
                    </div>
                    <div class="msg received">
                        Oke pak, saya sudah transfer via QRIS ya. Mohon segera diproses.
                    </div>
                    <div class="msg sent">
                        Terima kasih ya pak sudah belanja di toko kami. Pesanan akan dikirim siang ini.
                    </div>
                </div>
                <div class="chat-input">
                    <input type="text" id="chatInput" placeholder="Ketik pesan disini...">
                    <button class="btn-send" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function sendMessage() {
            const input = document.getElementById('chatInput');
            if(input.value.trim() === '') return;
            
            const msgBox = document.getElementById('messageBox');
            const newMsg = document.createElement('div');
            newMsg.className = 'msg sent';
            newMsg.innerText = input.value;
            
            msgBox.appendChild(newMsg);
            input.value = '';
            msgBox.scrollTop = msgBox.scrollHeight;
        }
        
        document.getElementById('chatInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    </script>
</body>
</html>
