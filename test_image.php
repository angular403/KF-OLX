<!DOCTYPE html>
<html>
<head>
    <title>Test Gambar</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-item { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
        img { max-width: 200px; height: auto; }
    </style>
</head>
<body>
    <h1>Test Tampilan Gambar</h1>
    
    <div class="test-item">
        <h3>Test 1: Path uploads/ads/noimage.png</h3>
        <img src="uploads/ads/noimage.png" alt="Test Image 1" onerror="this.style.background='red'; this.alt='ERROR';">
        <p>Status: <span id="status1">Loading...</span></p>
    </div>
    
    <div class="test-item">
        <h3>Test 2: Path relatif dari index.php</h3>
        <img src="uploads/ads/noimage.png" alt="Test Image 2" onerror="this.style.background='red'; this.alt='ERROR';">
        <p>Status: <span id="status2">Loading...</span></p>
    </div>
    
    <div class="test-item">
        <h3>Test 3: Path absolut</h3>
        <img src="/kf-olx/uploads/ads/noimage.png" alt="Test Image 3" onerror="this.style.background='red'; this.alt='ERROR';">
        <p>Status: <span id="status3">Loading...</span></p>
    </div>
    
    <script>
        // Test image loading
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img');
            const statuses = [
                document.getElementById('status1'),
                document.getElementById('status2'), 
                document.getElementById('status3')
            ];
            
            images.forEach((img, index) => {
                if (img.complete && img.naturalHeight !== 0) {
                    statuses[index].textContent = '✅ Loaded successfully';
                    statuses[index].style.color = 'green';
                } else {
                    img.onload = function() {
                        statuses[index].textContent = '✅ Loaded successfully';
                        statuses[index].style.color = 'green';
                    };
                    img.onerror = function() {
                        statuses[index].textContent = '❌ Failed to load';
                        statuses[index].style.color = 'red';
                    };
                }
            });
        });
    </script>
</body>
</html>
