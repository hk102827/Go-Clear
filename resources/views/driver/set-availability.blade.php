<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set Availability - Go Clear</title>
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

        .section-label {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 25px 0 15px 0;
        }

        .time-selector {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e0e0e0;
        }

        .time-row {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
        }

        .time-row:last-child {
            margin-bottom: 0;
        }

        .time-label {
            flex: 0 0 80px;
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .time-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .time-input:focus {
            outline: none;
            border-color: #7FDBDE;
            background: #f0feff;
        }

        .radius-input-group {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border: 2px solid #e0e0e0;
        }

        .radius-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
            margin-bottom: 12px;
            display: block;
        }

        .radius-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .radius-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .radius-input:focus {
            outline: none;
            border-color: #7FDBDE;
            background: #f0feff;
        }

        .unit-text {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .save-btn {
            width: 100%;
            padding: 16px;
            background: #1a3a52;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
            transition: background 0.3s ease;
        }

        .save-btn:hover {
            background: #0f2537;
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
            <h1 class="page-title">Set Your Availability</h1>
            <p class="page-subtitle">Let customers know when you're available</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.driver.availability.save') }}" id="availabilityForm">
            @csrf

            <div class="section-label">Working Hours</div>
            <div class="time-selector">
                <div class="time-row">
                    <label class="time-label">Days</label>
                    <input type="text" 
                           name="working_days" 
                           class="time-input" 
                           placeholder="Mon - Fri"
                           value="{{ old('working_days', 'Mon - Fri') }}"
                           required>
                </div>
                <div class="time-row">
                    <label class="time-label">Time</label>
                    <input type="time" 
                           name="start_time" 
                           class="time-input" 
                           value="{{ old('start_time', '08:00') }}"
                           required>
                    <span style="color: #666;">-</span>
                    <input type="time" 
                           name="end_time" 
                           class="time-input" 
                           value="{{ old('end_time', '18:00') }}"
                           required>
                </div>
            </div>

            <div class="section-label">Service Radius</div>
            <div class="radius-input-group">
                <label class="radius-label">Enter radius in miles</label>
                <div class="radius-input-wrapper">
                    <input type="number" 
                           name="service_radius" 
                           class="radius-input" 
                           placeholder="Enter radius in miles"
                           min="1"
                           step="1"
                           value="{{ old('service_radius') }}"
                           required>
                    <span class="unit-text">miles</span>
                </div>
            </div>

            <button type="submit" class="save-btn">Save & Continue</button>
        </form>
    </div>

    <script>
        document.getElementById('availabilityForm').addEventListener('submit', function(e) {
            const radius = document.querySelector('input[name="service_radius"]').value;
            
            if (!radius || parseInt(radius) < 1) {
                e.preventDefault();
                alert('Please enter a valid service radius (minimum 1 mile)');
            }
        });
    </script>
</body>
</html>