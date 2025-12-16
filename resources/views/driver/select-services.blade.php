<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Services - Go Clear</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #666;
        }

        .service-item {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .service-item:hover {
            background: #f0f9ff;
            border-color: #7FDBDE;
        }

        .service-item.selected {
            background: #f0feff;
            border-color: #4EADB5;
            border-width: 3px;
        }

        .service-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid #ccc;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .service-item.selected .service-checkbox {
            background: #4EADB5;
            border-color: #4EADB5;
        }

        .service-checkbox svg {
            width: 16px;
            height: 16px;
            color: white;
            display: none;
        }

        .service-item.selected .service-checkbox svg {
            display: block;
        }

        .service-label {
            flex: 1;
            font-size: 16px;
            font-weight: 500;
            color: #1a1a1a;
        }

        .service-input {
            display: none;
        }

        .next-btn {
            width: 100%;
            padding: 16px;
            background: #1a3a52;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease;
        }

        .next-btn:hover {
            background: #0f2537;
        }

        .next-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #ffe6e6;
            color: #cc0000;
            border: 1px solid #ffcccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-section">
            <img src="{{ asset('photo/car.svg') }}" class="logo" alt="Go Clear Logo">
            <h1 class="page-title">Select the Services You Offer</h1>
            <p class="page-subtitle">Choose all services you want to provide</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.driver.services.save') }}" id="servicesForm">
            @csrf

            <div class="service-item" onclick="toggleService('vehicle-collection')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Vehicle Collection</label>
                <input type="checkbox" name="services[]" value="1" class="service-input" id="vehicle-collection">
            </div>

            <div class="service-item" onclick="toggleService('skip-hire')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Skip Hire</label>
                <input type="checkbox" name="services[]" value="2" class="service-input" id="skip-hire">
            </div>

            <div class="service-item" onclick="toggleService('home-removals')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Home Removals</label>
                <input type="checkbox" name="services[]" value="3" class="service-input" id="home-removals">
            </div>

            <div class="service-item" onclick="toggleService('muck-away')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Muck Away</label>
                <input type="checkbox" name="services[]" value="4" class="service-input" id="muck-away">
            </div>

            <div class="service-item" onclick="toggleService('cement-aggregates')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Cement / Aggregates</label>
                <input type="checkbox" name="services[]" value="5" class="service-input" id="cement-aggregates">
            </div>

            <div class="service-item" onclick="toggleService('drainage')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Drainage</label>
                <input type="checkbox" name="services[]" value="6" class="service-input" id="drainage">
            </div>

            <div class="service-item" onclick="toggleService('handyman')">
                <div class="service-checkbox">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <label class="service-label">Handyman</label>
                <input type="checkbox" name="services[]" value="7" class="service-input" id="handyman">
            </div>

            <button type="submit" class="next-btn" id="nextBtn">Next</button>
        </form>
    </div>

    <script>
        function toggleService(serviceId) {
            const item = event.currentTarget;
            const checkbox = document.getElementById(serviceId);
            
            item.classList.toggle('selected');
            checkbox.checked = !checkbox.checked;
            
            updateButtonState();
        }

        function updateButtonState() {
            const checkboxes = document.querySelectorAll('.service-input:checked');
            const nextBtn = document.getElementById('nextBtn');
            
            if (checkboxes.length > 0) {
                nextBtn.disabled = false;
            } else {
                nextBtn.disabled = true;
            }
        }

        // Initialize button state
        document.addEventListener('DOMContentLoaded', function() {
            updateButtonState();
        });

        // Form validation
        document.getElementById('servicesForm').addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.service-input:checked');
            
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one service');
            }
        });
    </script>
</body>
</html>