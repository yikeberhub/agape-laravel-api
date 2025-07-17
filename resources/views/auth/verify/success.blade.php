<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verified</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md text-center max-w-md w-full">
        <h1 class="text-2xl font-bold text-green-600 mb-4">✅ Email Verified Successfully</h1>
        <p class="text-gray-700 mb-6">Thank you for confirming your email. You can now log in to your account.</p>

            <button onclick="window.close()"
                    class="inline-block bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600 transition">
                Close Tab
            </button>
        </div>
    </div>
    <script>
        setTimeout(() => window.close(), 10000); // Closes after 10 seconds
    </script>
</body>
</html>
