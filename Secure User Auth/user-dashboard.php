<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.html");
    exit;
}

// Get user data from session
$user_id = $_SESSION['user_id'] ?? '';
$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Secure Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-bg: #0a0b0d;
            --secondary-bg: #1a1d23;
            --card-bg: #242830;
            --accent-blue: #00d4ff;
            --accent-purple: #8b5cf6;
            --accent-green: #10b981;
            --accent-orange: #f59e0b;
            --accent-red: #ef4444;
            --text-primary: #ffffff;
            --text-secondary: #a8b3cf;
            --text-muted: #64748b;
            --border-color: #2d3748;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --shadow-primary: 0 25px 50px -12px rgba(0, 212, 255, 0.25);
            --shadow-glow: 0 0 50px rgba(0, 212, 255, 0.3);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-bg);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Background */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #0a0b0d 0%, #1a1d23 50%, #0f1419 100%);
        }

        .background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(0, 212, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: var(--accent-blue);
            border-radius: 50%;
            opacity: 0.1;
            animation: float 15s infinite linear;
        }

        @keyframes backgroundShift {
            0%, 100% {
                transform: translateX(0) translateY(0) rotate(0deg);
            }
            25% {
                transform: translateX(5px) translateY(-5px) rotate(1deg);
            }
            50% {
                transform: translateX(-3px) translateY(3px) rotate(-1deg);
            }
            75% {
                transform: translateX(-5px) translateY(-3px) rotate(0.5deg);
            }
        }

        @keyframes float {
            from {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.1;
            }
            90% {
                opacity: 0.1;
            }
            to {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        @keyframes shimmer {
            0%, 100% {
                opacity: 0;
                transform: translateX(-100%);
            }
            50% {
                opacity: 1;
                transform: translateX(100%);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(10, 11, 13, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            z-index: 1000;
            padding: 0.75rem 0;
        }

        .nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .logo i {
            font-size: 1.5rem;
            color: var(--accent-blue);
            filter: drop-shadow(0 0 10px var(--accent-blue));
        }

        .logo:hover {
            transform: translateY(-2px);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .user-info-badge i {
            color: var(--accent-green);
        }

        .btn-logout {
            background: linear-gradient(135deg, var(--accent-red), #dc2626);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-logout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-logout:hover::before {
            left: 100%;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
        }

        /* Main Container */
        .main-container {
            min-height: 100vh;
            padding: 6rem 2rem 2rem;
        }

        .dashboard-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .welcome-title {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: var(--text-secondary);
            font-size: 1.2rem;
            line-height: 1.6;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow-primary);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent-blue), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-glow);
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .card-title i {
            font-size: 1.5rem;
            color: var(--accent-blue);
        }

        /* User Profile Card */
        .user-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateX(5px);
            border-color: var(--accent-blue);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon i {
            font-size: 1.2rem;
            color: white;
        }

        .info-details {
            flex: 1;
        }

        .info-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: var(--accent-blue);
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.1);
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--accent-blue);
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Activity Section */
        .activity-section {
            margin-top: 2rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 12px;
            margin-bottom: 0.75rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            transform: translateX(5px);
            border-color: var(--accent-green);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-green), #059669);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-icon i {
            color: white;
            font-size: 1rem;
        }

        .activity-text {
            flex: 1;
            color: var(--text-primary);
            font-weight: 500;
        }

        .activity-time {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .action-btn {
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            color: white;
            padding: 1.5rem;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(139, 92, 246, 0.3);
        }

        .action-btn i {
            font-size: 1.2rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .welcome-title {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .nav {
                padding: 0 1.5rem;
                gap: 1rem;
            }

            .main-container {
                padding: 5rem 1.5rem 2rem;
            }

            .card {
                padding: 1.5rem;
                border-radius: 20px;
            }

            .welcome-title {
                font-size: 2.2rem;
            }

            .welcome-subtitle {
                font-size: 1.1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .user-info-badge {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .nav {
                padding: 0 1rem;
            }

            .logo {
                font-size: 1.1rem;
                gap: 0.5rem;
            }

            .logo i {
                font-size: 1.3rem;
            }

            .btn-logout {
                padding: 0.6rem 1.2rem;
                font-size: 0.9rem;
            }

            .main-container {
                padding: 5rem 1rem 2rem;
            }

            .card {
                padding: 1.2rem;
            }

            .welcome-title {
                font-size: 2rem;
            }

            .welcome-subtitle {
                font-size: 1rem;
            }

            .card-title {
                font-size: 1.3rem;
            }

            .info-item {
                padding: 0.8rem;
            }

            .info-icon {
                width: 45px;
                height: 45px;
            }

            .stat-card {
                padding: 1.2rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 0.5rem 0;
            }

            .nav {
                padding: 0 0.75rem;
            }

            .logo {
                font-size: 1rem;
                gap: 0.4rem;
            }

            .logo i {
                font-size: 1.2rem;
            }

            .main-container {
                padding: 4.5rem 0.75rem 1.5rem;
            }

            .card {
                padding: 1rem;
                border-radius: 16px;
            }

            .welcome-title {
                font-size: 1.8rem;
            }

            .welcome-subtitle {
                font-size: 0.9rem;
            }

            .dashboard-grid {
                gap: 1rem;
            }

            .card-title {
                font-size: 1.2rem;
            }

            .info-item {
                padding: 0.7rem;
                gap: 0.8rem;
            }

            .info-icon {
                width: 40px;
                height: 40px;
            }

            .activity-icon {
                width: 35px;
                height: 35px;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-value {
                font-size: 1.3rem;
            }

            .action-btn {
                padding: 1.2rem;
                gap: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="background"></div>
    <div class="floating-particles" id="particles"></div>

    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <div class="header-actions">
                <div class="user-info-badge">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars(ucwords($user_name)); ?></span>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-container">
        <div class="dashboard-wrapper">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars(ucwords($user_name)); ?>!</h1>
                <p class="welcome-subtitle">We're glad to see you again. Here's your secure dashboard overview.</p>
            </div>

            <!-- Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- User Profile Card -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-user"></i>
                        User Profile
                    </div>
                    <div class="user-info">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">User ID</div>
                                <div class="info-value">#<?php echo htmlspecialchars($user_id); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Full Name</div>
                                <div class="info-value"><?php echo htmlspecialchars(ucwords($user_name)); ?></div>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-details">
                                <div class="info-label">Email Address</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_email); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Overview Card -->
                <div class="card">
                    <div class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Dashboard Overview
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value" id="current-date"><?php echo date('M j'); ?></div>
                            <div class="stat-label">Current Date</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value" id="current-time"><?php echo date('H:i'); ?></div>
                            <div class="stat-label">Current Time</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="stat-value">Active</div>
                            <div class="stat-label">Account Status</div>
                        </div>
                    </div>
                    
                    <div class="activity-section">
                        <div class="card-title">
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="activity-text">Successfully logged in</div>
                            <div class="activity-time">Just now</div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="activity-text">Profile accessed</div>
                            <div class="activity-time"><?php echo date('g:i A'); ?></div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="activity-text">Security check passed</div>
                            <div class="activity-time"><?php echo date('M j, Y'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <button class="action-btn" onclick="refreshDashboard()">
                    <i class="fas fa-sync-alt"></i>
                    Refresh Dashboard
                </button>
                
                <button class="action-btn" onclick="viewProfile()">
                    <i class="fas fa-user-edit"></i>
                    Edit Profile
                </button>
                
                <button class="action-btn" onclick="viewSettings()">
                    <i class="fas fa-cog"></i>
                    Account Settings
                </button>
            </div>
        </div>
    </main>

    <script>
        // Create floating particles
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = window.innerWidth < 768 ? 25 : 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.width = particle.style.height = Math.random() * 4 + 1 + 'px';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                container.appendChild(particle);
            }
        }

        // Update time every second
        function updateTime() {
            const now = new Date();
            const timeElement = document.getElementById('current-time');
            const dateElement = document.getElementById('current-date');
            
            if (timeElement) {
                const hours = now.getHours().toString().padStart(2, '0');
                const minutes = now.getMinutes().toString().padStart(2, '0');
                timeElement.textContent = `${hours}:${minutes}`;
            }
            
            if (dateElement) {
                const options = { month: 'short', day: 'numeric' };
                dateElement.textContent = now.toLocaleDateString('en-US', options);
            }
        }

        // Add click effect to cards
        function addRippleEffect() {
            const cards = document.querySelectorAll('.card, .stat-card, .info-item, .activity-item');
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(0, 212, 255, 0.3)';
                    ripple.style.pointerEvents = 'none';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                    ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        }

        // Quick action functions
        function refreshDashboard() {
            // Add loading animation
            const btn = event.target;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            // Simulate refresh
            setTimeout(() => {
                updateTime();
                btn.innerHTML = originalHTML;
                showNotification('Dashboard refreshed successfully!', 'success');
            }, 1500);
        }

        function viewProfile() {
            showNotification('Profile editing feature coming soon!', 'info');
        }

        function viewSettings() {
            showNotification('Settings panel coming soon!', 'info');
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${type === 'success' ? 'rgba(16, 185, 129, 0.9)' : 
                           type === 'error' ? 'rgba(239, 68, 68, 0.9)' : 
                           'rgba(0, 212, 255, 0.9)'};
                color: white;
                padding: 1rem 1.5rem;
                border-radius: 12px;
                font-weight: 500;
                backdrop-filter: blur(10px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                transform: translateX(100%);
                transition: all 0.3s ease;
                max-width: 300px;
                word-wrap: break-word;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Animate out and remove
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Handle window resize for particles
        function handleResize() {
            const container = document.getElementById('particles');
            const currentParticleCount = container.children.length;
            const newParticleCount = window.innerWidth < 768 ? 25 : 50;

            if (currentParticleCount !== newParticleCount) {
                container.innerHTML = '';
                createParticles();
            }
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            addRippleEffect();
            
            // Update time immediately and then every second
            updateTime();
            setInterval(updateTime, 1000);
            
            // Handle resize events
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(handleResize, 250);
            });
            
            // Add fade-in animation to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
            
            // Welcome message
            setTimeout(() => {
                showNotification('Welcome to your secure dashboard!', 'success');
            }, 1000);
        });

        // Add enhanced touch interactions for mobile
        if ('ontouchstart' in window) {
            document.querySelectorAll('.card, .stat-card, .action-btn, .info-item, .activity-item').forEach(element => {
                element.addEventListener('touchstart', function() {
                    this.style.transform = this.style.transform.replace(/translateY\([^)]*\)/, '') + ' translateY(-2px)';
                });

                element.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = this.style.transform.replace(/translateY\([^)]*\)/, '');
                    }, 150);
                });
            });
        }

        // Keyboard navigation support
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                // Close any open modals or notifications
                const notifications = document.querySelectorAll('[style*="position: fixed"]');
                notifications.forEach(notification => {
                    if (notification.style.right === '20px') {
                        notification.style.transform = 'translateX(100%)';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }
                });
            }
        });

        // Add loading states to action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (!this.classList.contains('loading')) {
                    this.classList.add('loading');
                    const originalContent = this.innerHTML;
                    
                    // Add subtle loading animation
                    setTimeout(() => {
                        this.classList.remove('loading');
                        this.innerHTML = originalContent;
                    }, 1500);
                }
            });
        });

        // Performance optimization for animations
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (prefersReducedMotion.matches) {
            // Disable animations for users who prefer reduced motion
            document.documentElement.style.setProperty('--animation-duration', '0s');
        }

        // Add custom cursor effects for interactive elements
        document.querySelectorAll('.card, .action-btn, .stat-card, .info-item').forEach(element => {
            element.addEventListener('mouseenter', function() {
                document.body.style.cursor = 'pointer';
            });
            
            element.addEventListener('mouseleave', function() {
                document.body.style.cursor = 'default';
            });
        });

        // Auto-refresh dashboard every 5 minutes
        setInterval(() => {
            updateTime();
            // You can add more auto-refresh logic here
        }, 300000);

        // Add visibility change handler to pause animations when tab is not active
        document.addEventListener('visibilitychange', function() {
            const particles = document.getElementById('particles');
            if (document.hidden) {
                particles.style.animationPlayState = 'paused';
            } else {
                particles.style.animationPlayState = 'running';
                updateTime();
            }
        });

        // Browser back/forward button detection and auto-logout
        (function() {
            // Push initial state to history
            const currentUrl = window.location.href;
            history.pushState({page: 'dashboard', timestamp: Date.now()}, 'Dashboard', currentUrl);
            
            // Listen for popstate events (back/forward button clicks)
            window.addEventListener('popstate', function(event) {
                console.log('Browser navigation detected - Auto logout triggered');
                
                // Show logout notification
                showNotification('Session terminated due to navigation. Redirecting...', 'error');
                
                // Prevent any further navigation
                history.pushState({page: 'dashboard', timestamp: Date.now()}, 'Dashboard', currentUrl);
                
                // Call logout after a brief delay
                setTimeout(function() {
                    // Redirect to logout.php to destroy session
                    window.location.href = 'logout.php';
                }, 1500);
            });
            
            // Also handle beforeunload to catch manual URL changes
            window.addEventListener('beforeunload', function(event) {
                // Check if navigation is to logout.php (allow normal logout)
                const newUrl = event.target.activeElement?.href || '';
                if (newUrl.includes('logout.php')) {
                    return; // Allow normal logout
                }
                
                // For any other navigation, trigger logout
                if (performance.getEntriesByType('navigation')[0]?.type !== 'reload') {
                    navigator.sendBeacon('logout.php');
                }
            });
            
            // Additional security: Check for direct URL manipulation
            let lastUrl = window.location.href;
            setInterval(function() {
                const currentUrl = window.location.href;
                if (currentUrl !== lastUrl && !currentUrl.includes('user-dashboard.php')) {
                    // URL changed to something other than dashboard - logout
                    window.location.href = 'logout.php';
                }
                lastUrl = currentUrl;
            }, 1000);
            
            // Prevent browser caching of this page
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    // Page was loaded from cache (back button) - logout
                    window.location.href = 'logout.php';
                }
            });
            
            // Additional security: Disable right-click context menu (optional)
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                showNotification('Right-click disabled for security', 'info');
                return false;
            });
            
            // Prevent F5 refresh in some cases (optional - you might want to allow refresh)
            document.addEventListener('keydown', function(e) {
                // Allow Ctrl+R and F5 for refresh (remove these if you want to disable refresh)
                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                    return; // Allow refresh
                }
                
                // Block other potentially dangerous key combinations
                if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) { // Ctrl+U (view source)
                    e.preventDefault();
                    showNotification('Action blocked for security', 'error');
                    return false;
                }
                
                if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) { // Developer tools
                    e.preventDefault();
                    showNotification('Developer tools blocked for security', 'error');
                    return false;
                }
            });
            
            console.log('Security measures activated: Browser navigation monitoring enabled');
        })();
    </script>
    <script src="./assets/js/disable.js"></script>
</body>
</html>