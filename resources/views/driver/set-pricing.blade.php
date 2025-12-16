<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Set Your Prices - Go Clear</title>
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

        .pricing-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #e0e0e0;
        }

        .service-name {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .pricing-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .price-input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .price-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .price-input-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .currency-symbol {
            font-size: 16px;
            font-weight: 600;
            color: #4EADB5;
        }

        .price-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .price-input:focus {
            outline: none;
            border-color: #7FDBDE;
            background: #f0feff;
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
            <h1 class="page-title">Set Your Prices</h1>
            <p class="page-subtitle">Set prices based on hourly, per mile, or per load</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.driver.pricing.save') }}" id="pricingForm">
            @csrf

            @foreach($selectedServices as $service)
            <div class="pricing-item">
                <div class="service-name">{{ $service->name }}</div>
                
                <div class="pricing-options">
                    <div class="price-input-group">
                        <label class="price-label">Fixed Price</label>
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">£</span>
                            <input type="number" 
                                   name="pricing[{{ $service->id }}][fixed]" 
                                   class="price-input" 
                                   placeholder="60"
                                   step="0.01"
                                   min="0">
                        </div>
                    </div>

                    <div class="price-input-group">
                        <label class="price-label">Price per Mile</label>
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">£</span>
                            <input type="number" 
                                   name="pricing[{{ $service->id }}][per_mile]" 
                                   class="price-input" 
                                   placeholder="2/mile"
                                   step="0.01"
                                   min="0">
                        </div>
                    </div>

                    <div class="price-input-group">
                        <label class="price-label">Price per Hour</label>
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">£</span>
                            <input type="number" 
                                   name="pricing[{{ $service->id }}][per_hour]" 
                                   class="price-input" 
                                   placeholder="25/hr"
                                   step="0.01"
                                   min="0">
                        </div>
                    </div>

                    <div class="price-input-group">
                        <label class="price-label">Price per Load/Ton/Bag</label>
                        <div class="price-input-wrapper">
                            <span class="currency-symbol">£</span>
                            <input type="number" 
                                   name="pricing[{{ $service->id }}][per_load]" 
                                   class="price-input" 
                                   placeholder="30/load"
                                   step="0.01"
                                   min="0">
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <button type="submit" class="next-btn">Next</button>
        </form>
    </div>

    <script>
        document.getElementById('pricingForm').addEventListener('submit', function(e) {
            let hasAtLeastOnePrice = false;
            const inputs = document.querySelectorAll('.price-input');
            
            inputs.forEach(input => {
                if (input.value && parseFloat(input.value) > 0) {
                    hasAtLeastOnePrice = true;
                }
            });
            
            if (!hasAtLeastOnePrice) {
                e.preventDefault();
                alert('Please set at least one price for your services');
            }
        });
    </script>
</body>
</html>