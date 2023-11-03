<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/dist/output.css">
    <title>Login</title>
</head>

<body class="bg-[#fff5d2] flex items-center justify-center flex-col h-screen">
    <div class="text-center">
        <div class=" flex items-center justify-center">
            <!-- Establecer el tamaño de la imagen a 300x300 px -->
            <img src="../assets/logo.jpg" alt="Logo" class="w-[300px] h-[300px]">
        </div>
        <h2 class="text-[20px] font-bold mb-4">Bienvenido, ingresa con tu cuenta</h2>
        <!-- Contenedor con fondo blanco para el formulario -->
        <div class="bg-white p-6 rounded-md shadow-md w-[300px] mx-auto">
            <form action="/handle_db/loginconnect.php" method="post" class="space-y-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Correo:</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500">

                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-blue-500">

                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">Ingresar</button>
            </form>
        </div>
    </div>
</body>

</html>