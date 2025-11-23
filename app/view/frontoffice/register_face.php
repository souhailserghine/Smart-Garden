<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Face - SmartGarden</title>
    <link href="./assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/boxicons@1.9.2/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .face-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
        }
        #video {
            border-radius: 10px;
            border: 3px solid #667eea;
            margin: 20px 0;
        }
        .btn-capture {
            background: #667eea;
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-capture:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="face-container">
        <h2><i class='bx bx-face-mask'></i> Register Your Face</h2>
        <p class="text-muted">Position your face in the camera</p>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        
        <form action="face_register_handler.php" method="POST">
            <input type="email" class="form-control mb-3" name="email" placeholder="Your Email" required>
            <video id="video" autoplay width="320" height="240"></video>
            <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
            <input type="hidden" name="face" id="faceData">
            <button type="button" class="btn-capture" onclick="captureFace()">
                <i class='bx bx-camera'></i> Capture & Register
            </button>
        </form>
        
        <div class="mt-3">
            <a href="sign-in.php">Back to Sign In</a>
        </div>
    </div>

    <script>
        navigator.mediaDevices.getUserMedia({video:true}).then(stream=>{
            document.getElementById('video').srcObject = stream;
        }).catch(err=>{
            alert('Camera access denied. Please allow camera access to register your face.');
        });

        function captureFace(){
            const canvas = document.getElementById('canvas');
            const video = document.getElementById('video');
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, 320, 240);
            const faceData = canvas.toDataURL('image/png');
            document.getElementById('faceData').value = faceData;
            document.querySelector('form').submit();
        }
    </script>
    <script src="./assets/js/jquery/jquery-3.3.1.min.js"></script>
    <script src="./assets/js/bootstrap/bootstrap.min.js"></script>
</body>
</html>
