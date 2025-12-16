<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Documents - Go Clear</title>
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

        .document-item {
            margin-bottom: 25px;
        }

        .document-label {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            display: block;
        }

        .document-note {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
            display: block;
        }

        .upload-box {
            background: #f0f9ff;
            border: 2px dashed #7FDBDE;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .upload-box:hover {
            background: #e0f5f7;
            border-color: #4EADB5;
        }

        .upload-box.has-file {
            background: #f0feff;
            border-color: #4EADB5;
            border-style: solid;
        }

        .upload-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
        }

        .upload-icon svg {
            width: 100%;
            height: 100%;
            color: #7FDBDE;
        }

        .upload-text {
            font-size: 14px;
            color: #4EADB5;
            font-weight: 500;
        }

        .file-input {
            display: none;
        }

        .file-preview {
            display: none;
            background: white;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .file-preview.active {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-icon {
            width: 35px;
            height: 35px;
            background: #7FDBDE;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        .file-name {
            flex: 1;
            font-size: 13px;
            color: #1a1a1a;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .remove-file {
            background: transparent;
            color: #ff4444;
            border: none;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .submit-btn {
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

        .submit-btn:hover {
            background: #0f2537;
        }

        .submit-btn:disabled {
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
            <h1 class="page-title">Upload Required Documents</h1>
            <p class="page-subtitle">We need these to verify your identity and services.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.driver.documents.upload') }}" enctype="multipart/form-data" id="documentForm">
            @csrf

            <!-- Driving License -->
            <div class="document-item">
                <label class="document-label">Driving Licence</label>
                <span class="document-note">Front and Back</span>
                <div class="upload-box" id="box-driving-license" onclick="document.getElementById('driving_license').click()">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="upload-text">+ Upload</p>
                </div>
                <input type="file" id="driving_license" name="driving_license" class="file-input" accept="image/*,.pdf" onchange="handleFileSelect(this, 'driving-license')" required>
                <div class="file-preview" id="preview-driving-license">
                    <div class="file-icon">📄</div>
                    <span class="file-name" id="name-driving-license"></span>
                    <button type="button" class="remove-file" onclick="removeFile('driving-license')">✕</button>
                </div>
            </div>

            <!-- Vehicle Photo -->
            <div class="document-item">
                <label class="document-label">Vehicle Photo</label>
                <div class="upload-box" id="box-vehicle-photo" onclick="document.getElementById('vehicle_photo').click()">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="upload-text">+ Upload</p>
                </div>
                <input type="file" id="vehicle_photo" name="vehicle_photo" class="file-input" accept="image/*" onchange="handleFileSelect(this, 'vehicle-photo')" required>
                <div class="file-preview" id="preview-vehicle-photo">
                    <div class="file-icon">🚗</div>
                    <span class="file-name" id="name-vehicle-photo"></span>
                    <button type="button" class="remove-file" onclick="removeFile('vehicle-photo')">✕</button>
                </div>
            </div>

            <!-- Number Plate -->
            <div class="document-item">
                <label class="document-label">Number Plate</label>
                <div class="upload-box" id="box-number-plate" onclick="document.getElementById('number_plate').click()">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="upload-text">+ Upload</p>
                </div>
                <input type="file" id="number_plate" name="number_plate" class="file-input" accept="image/*" onchange="handleFileSelect(this, 'number-plate')" required>
                <div class="file-preview" id="preview-number-plate">
                    <div class="file-icon">🔢</div>
                    <span class="file-name" id="name-number-plate"></span>
                    <button type="button" class="remove-file" onclick="removeFile('number-plate')">✕</button>
                </div>
            </div>

            <!-- Insurance Proof -->
            <div class="document-item">
                <label class="document-label">Insurance Proof</label>
                <div class="upload-box" id="box-insurance-proof" onclick="document.getElementById('insurance_proof').click()">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="upload-text">+ Upload</p>
                </div>
                <input type="file" id="insurance_proof" name="insurance_proof" class="file-input" accept="image/*,.pdf" onchange="handleFileSelect(this, 'insurance-proof')" required>
                <div class="file-preview" id="preview-insurance-proof">
                    <div class="file-icon">🛡️</div>
                    <span class="file-name" id="name-insurance-proof"></span>
                    <button type="button" class="remove-file" onclick="removeFile('insurance-proof')">✕</button>
                </div>
            </div>

            <!-- Waste Carrier License -->
            <div class="document-item">
                <label class="document-label">Waste Carrier License</label>
                <span class="document-note">(only if offering waste services)</span>
                <div class="upload-box" id="box-waste-carrier" onclick="document.getElementById('waste_carrier_license').click()">
                    <div class="upload-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="upload-text">+ Upload (Optional)</p>
                </div>
                <input type="file" id="waste_carrier_license" name="waste_carrier_license" class="file-input" accept="image/*,.pdf" onchange="handleFileSelect(this, 'waste-carrier')">
                <div class="file-preview" id="preview-waste-carrier">
                    <div class="file-icon">♻️</div>
                    <span class="file-name" id="name-waste-carrier"></span>
                    <button type="button" class="remove-file" onclick="removeFile('waste-carrier')">✕</button>
                </div>
            </div>

            <button type="submit" class="submit-btn">Submit for Verification</button>
        </form>
    </div>

    <script>
        function handleFileSelect(input, itemId) {
            const file = input.files[0];
            if (file) {
                const box = document.getElementById('box-' + itemId);
                const preview = document.getElementById('preview-' + itemId);
                const nameSpan = document.getElementById('name-' + itemId);
                
                box.classList.add('has-file');
                preview.classList.add('active');
                nameSpan.textContent = file.name;
            }
        }

        function removeFile(itemId) {
            const input = document.getElementById(itemId.replace(/-/g, '_'));
            const box = document.getElementById('box-' + itemId);
            const preview = document.getElementById('preview-' + itemId);
            
            input.value = '';
            box.classList.remove('has-file');
            preview.classList.remove('active');
        }
    </script>
</body>
</html>