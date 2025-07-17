<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">OTP Verification</h1>

        <p class="text-gray-700 text-lg mb-4">
            Dear <span class="text-blue-600 font-semibold">{{ $name }}</span>,
        </p>

        <p class="text-gray-700 mb-6">
            Thank you for your request. Please use the following One-Time Password (OTP) to verify your account:
        </p>

        <div class="bg-blue-600 text-white text-4xl font-bold text-center rounded-md py-4 tracking-widest mb-8">
            {{ $otp }}
        </div>

        <p class="text-gray-700 mb-4">
            This OTP is valid for a limited time. Please do not share it with anyone.
        </p>

        <p class="text-gray-700 mb-6">
            If you did not request this OTP, please ignore this email.
        </p>

        <div class="text-center text-gray-500 text-sm">
            <p>Best regards,</p>
            <p>Agape mobility Ethiopia</p>
        </div>
    </div>
</body>
</html>
