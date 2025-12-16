<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Status - Go Clear</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .status-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            text-align: center;
        }

        .status-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            position: relative;
        }

        .status-icon.pending,
        .status-icon.in-progress {
            border: 8px solid #7FDBDE;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        .status-icon.verified {
            width: 120px;
            height: 120px;
            background: #7FDBDE;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-icon.verified svg {
            width: 70px;
            height: 70px;
            color: white;
        }

        .status-icon.rejected {
            width: 120px;
            height: 120px;
            background: #ff4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-icon.rejected svg {
            width: 70px;
            height: 70px;
            color: white;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .status-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .status-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .action-btn {
            display: inline-block;
            padding: 16px 40px;
            background: #1a1a1a;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .action-btn:hover {
            background: #333;
        }

        .action-btn.secondary {
            background: #7FDBDE;
            margin-left: 10px;
        }

        .action-btn.secondary:hover {
            background: #4EADB5;
        }

        .rejection-reason {
            background: #ffe6e6;
            border: 2px solid #ffcccc;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .rejection-reason h4 {
            color: #cc0000;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .rejection-reason p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .info-box {
            background: #f0feff;
            border: 2px solid #7FDBDE;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .info-box p {
            color: #4EADB5;
            font-size: 14px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container">
            @if($status === 'pending' || $status === 'in_progress')
                <!-- In Progress Status -->
                <div class="status-icon in-progress"></div>
                <h1 class="status-title">Verification In Progress</h1>
                <p class="status-message">
                    Our team will review your documents. You'll be notified once approved.
                </p>
                <div class="info-box">
                    <p>Your profile has been submitted for verification.<br>Check back later for updates.</p>
                </div>
                <a href="/" class="action-btn">Go to Home</a>

            @elseif($status === 'verified')
                <!-- Verified Status -->
                <div class="status-icon verified">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="status-title">Congratulations!</h1>
                <p class="status-message">Your account is verified.</p>
                <a href="{{ route('driver.dashboard') }}" class="action-btn">Go to Dashboard</a>

            @elseif($status === 'rejected')
                <!-- Rejected Status -->
                <div class="status-icon rejected">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h1 class="status-title">Verification Failed</h1>
                <p class="status-message">Unfortunately, your documents could not be verified.</p>
                
                @if(isset($rejectionReason))
                <div class="rejection-reason">
                    <h4>Reason for Rejection:</h4>
                    <p>{{ $rejectionReason }}</p>
                </div>
                @endif

                <a href="{{ route('driver.documents.reupload') }}" class="action-btn">Re-upload Documents</a>
                <a href="{{ route('support') }}" class="action-btn secondary">Contact Support</a>
            @endif
        </div>
    </div>
</body>
</html>