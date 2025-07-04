<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
        }
        .otp {
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-size: 20px;
            text-align: center;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
        .name{
            color: #007bff;
            font-size: larger;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OTP Verification</h1>
        <p>Dear <span class="name">{{$name}}</span>,</p>
        <p>Thank you for your request. Please use the following One-Time Password (OTP) to verify your account:</p>
        <div class="otp">{{ $otp }}</div>
        <p>This OTP is valid for a limited time. Please do not share it with anyone.</p>
        <p>If you did not request this OTP, please ignore this email.</p>
        <div class="footer">
            <p>Best regards,</p>
            <p>Agape mobility Ethiopia</p>
        </div>
    </div>
</body>
</html>