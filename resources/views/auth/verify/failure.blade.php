<!-- resources/views/auth/verify/failure.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-red-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md text-center max-w-md w-full">
        <h1 class="text-2xl font-bold text-red-600 mb-4">❌ Email Verification Failed</h1>
        <p class="text-gray-700 mb-6">{{ $message }}</p>

        <div class="flex justify-center gap-4">
            <button onclick="window.close()"
                    class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600 transition">
                Close Tab
            </button>
        </div>

    </div>

    <script>
        setTimeout(() => window.close(), 10000); // Closes after 10 seconds
    </script>
</body>
</html>
