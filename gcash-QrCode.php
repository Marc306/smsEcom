<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCash Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="css/page/QRcode.css"> -->
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <img src="image/gcash-logo.png" alt="GCash Logo" class="img-fluid mb-3" style="max-height: 60px;">
                            <h2 class="card-title text-primary">Pay via GCash</h2>
                            <p class="text-muted">Scan the QR code below or download it to upload in GCash</p>
                        </div>

                        <div class="text-center mb-4">
                            <div class="p-3 bg-white rounded shadow-sm d-inline-block mb-3">
                                <img id="gcashQR" src="image/admin-gcash-QrCode.PNG" alt="GCash QR Code" class="img-fluid" style="max-width: 200px;">
                            </div>
                            
                            <div>
                                <a id="downloadQR" href="image/admin-gcash-QrCode.PNG" download="GCash-QR-Code.png" class="btn btn-primary mb-3">
                                    <i class="bi bi-download me-2"></i>Download QR Code
                                </a>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <p class="text-muted mb-2">OR Send payment to:</p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control text-center" value="09XXXXXXXXX" readonly>
                                <button class="btn btn-outline-primary" type="button" id="copyNumber">
                                    <i class="bi bi-clipboard me-2"></i>Copy
                                </button>
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="user-toPay.php" class="btn btn-link text-decoration-none">
                                <i class="bi bi-arrow-left me-2"></i>Back to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script/qrCode-downloader.js"></script>
</body>
</html>
