<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Payment</title>
    <link rel="stylesheet" href="css/page/QRcode.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="section">
        <div class="gcash-container">
            <h2>Pay via GCash</h2>
            <p>Scan the QR code below or download it to upload in GCash.</p>

            <div class="qr-box">
                <img id="gcashQR" src="image/admin-gcash-QrCode.PNG" alt="GCash QR Code">
            </div>

            <a id="downloadQR" href="gcash-qr-code.png" download="GCash-QR-Code.png">
                <button class="download-btn">Download QR Code</button>
            </a>

            <p>OR Send payment to:</p>
            <p class="gcash-number"><strong>GCash Number: 09XXXXXXXXX</strong></p>
            <button id="copyNumber">Copy GCash Number</button>

            <a href="user-purchase.php" class="back-link">⬅ Back to Checkout</a>
        </div>
    </div>

    <script src="script/qrCode-downloader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
