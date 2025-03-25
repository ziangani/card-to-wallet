<!DOCTYPE html>
<html>
<head>
    <title>3D Secure Authentication</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        #result { margin-top: 20px; padding: 10px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<!-- Hidden form for 3DS redirect -->
<form id="threeDSForm" method="POST">
    <input type="hidden" name="creq" value="">
</form>

<div id="result"></div>

<script>
    // Get URL parameters for debugging
    const urlParams = new URLSearchParams(window.location.search);
    const resultDiv = document.getElementById('result');

    // Function to handle the 3DS response from your PHP endpoint
    function handle3DSResponse(responseData) {
        const form = document.getElementById('threeDSForm');
        form.action = responseData.acsUrl;
        form.querySelector('[name="creq"]').value = responseData.pareq;
        form.submit();
    }

    // Function to process the authentication
    function processAuthentication() {
        // Sample response data (replace this with your actual PHP response)
        const sampleResponse = {
            requires_action: true,
            acsUrl: "{{request()->acsUrl}}",
            pareq: "{{request()->pareq}}",
            authenticationTransactionId: "{{request()->authenticationTransactionId}}"
        };

        handle3DSResponse(sampleResponse);
    }

    // Start the process immediately when the page loads
    processAuthentication();

    // If there's a response after returning from 3DS
    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        if (status === 'success') {
            resultDiv.className = 'success';
            resultDiv.textContent = 'Authentication successful!';
        } else {
            resultDiv.className = 'error';
            resultDiv.textContent = 'Authentication failed: ' + urlParams.get('message');
        }
    }
</script>
</body>
</html>
