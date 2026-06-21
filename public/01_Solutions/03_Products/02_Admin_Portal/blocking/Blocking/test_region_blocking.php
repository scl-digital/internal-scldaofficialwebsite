<!DOCTYPE html>
<html>
<head>
    <title>Test Region Blocking - SCL</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .test-section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 5px; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Region Blocking API Test</h1>
    
    <div class="test-section">
        <h2>1. Database Connection Test</h2>
        <button class="btn" onclick="testDatabase()">Test Database</button>
        <div id="db-result"></div>
    </div>
    
    <div class="test-section">
        <h2>2. API Endpoints Test</h2>
        <button class="btn" onclick="testListAPI()">Test List API</button>
        <button class="btn" onclick="testBlockAPI()">Test Block API</button>
        <button class="btn" onclick="testStatusAPI()">Test Status API</button>
        <div id="api-result"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Test Results</h2>
        <div id="test-results"></div>
    </div>

    <script>
        async function testDatabase() {
            const resultDiv = document.getElementById('db-result');
            try {
                const response = await fetch('api/region_block.php?action=list');
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = '<div class="success">✓ Database connection successful</div>';
                } else {
                    resultDiv.innerHTML = '<div class="error">✗ Database connection failed: ' + data.message + '</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">✗ Database connection error: ' + error.message + '</div>';
            }
        }

        async function testListAPI() {
            const resultDiv = document.getElementById('api-result');
            try {
                const response = await fetch('api/region_block.php?action=list');
                const data = await response.json();
                
                resultDiv.innerHTML = '<h3>List API Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
                if (data.success) {
                    resultDiv.innerHTML += '<div class="success">✓ List API working</div>';
                } else {
                    resultDiv.innerHTML += '<div class="error">✗ List API failed</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">✗ List API error: ' + error.message + '</div>';
            }
        }

        async function testBlockAPI() {
            const resultDiv = document.getElementById('api-result');
            try {
                const formData = new FormData();
                formData.append('action', 'block');
                formData.append('country', 'TEST');
                formData.append('reason', 'Test blocking');
                formData.append('admin_key', 'scl_admin_secret_key_2024');
                
                const response = await fetch('api/region_block.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                resultDiv.innerHTML = '<h3>Block API Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
                if (data.success) {
                    resultDiv.innerHTML += '<div class="success">✓ Block API working</div>';
                } else {
                    resultDiv.innerHTML += '<div class="error">✗ Block API failed</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">✗ Block API error: ' + error.message + '</div>';
            }
        }

        async function testStatusAPI() {
            const resultDiv = document.getElementById('api-result');
            try {
                const response = await fetch('api/region_block.php?action=status&country=US');
                const data = await response.json();
                
                resultDiv.innerHTML = '<h3>Status API Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
                if (data.success) {
                    resultDiv.innerHTML += '<div class="success">✓ Status API working</div>';
                } else {
                    resultDiv.innerHTML += '<div class="error">✗ Status API failed</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error">✗ Status API error: ' + error.message + '</div>';
            }
        }

        // Run all tests
        async function runAllTests() {
            const results = [];
            
            try {
                // Test database
                const dbResponse = await fetch('api/region_block.php?action=list');
                const dbData = await dbResponse.json();
                results.push('Database: ' + (dbData.success ? 'PASS' : 'FAIL'));
                
                // Test status
                const statusResponse = await fetch('api/region_block.php?action=status&country=US');
                const statusData = await statusResponse.json();
                results.push('Status API: ' + (statusData.success ? 'PASS' : 'FAIL'));
                
                // Test block
                const formData = new FormData();
                formData.append('action', 'block');
                formData.append('country', 'TEST');
                formData.append('reason', 'Test');
                formData.append('admin_key', 'scl_admin_secret_key_2024');
                
                const blockResponse = await fetch('api/region_block.php', { method: 'POST', body: formData });
                const blockData = await blockResponse.json();
                results.push('Block API: ' + (blockData.success ? 'PASS' : 'FAIL'));
                
                document.getElementById('test-results').innerHTML = '<h3>Test Summary:</h3><pre>' + results.join('\n') + '</pre>';
                
            } catch (error) {
                document.getElementById('test-results').innerHTML = '<div class="error">Test failed: ' + error.message + '</div>';
            }
        }

        // Auto-run tests when page loads
        window.onload = runAllTests;
    </script>
</body>
</html>
