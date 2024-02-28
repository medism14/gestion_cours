<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Première connexion</title>
            <!-- Fonts -->
            <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="icon" href="{{ asset('images/roundedLogo.png') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
         <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
         <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <style>
        
        body {
            font-family: 'figtree', sans-serif;
            background: rgb(230, 213, 202);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

    </style>

</head>
<body>
    <div class="p-2 flex justify-end">
        <a id="logout" class="mr-2 md:mr-6 border-2 p-2 rounded-lg transition-all duration-300 ease-in-out bg-white text-black hover:bg-gray-400 relative " title="Déconnexion" href="{{ route('logout') }}">
            <i class="fas fa-door-open"></i>
        </a> 
    </div>

    <section class="flex flex-col space-y-0 mt-16">
        <h1 class="text-3xl font-bold text-center bg-gray-200 text-black p-4 mx-auto rounded-t-lg w-[80%] md:w-[60%]">Changez votre mot de passe par defaut</h1>
        <section class="bg-gray-600 rounded-lg border-2 border-gray-600 w-[80%] md:w-[60%] mx-auto mt-20 rounded-t-none p-10 my-auto text-white">
            <form action="{{ route('first_connection') }}" method="POST" class="m-0 p-0" onsubmit="return confirmMDP()">
            @csrf
            <input type="text" readonly class="hidden" value="{{ auth()->user()->id }}" name="id">
                <div class="md:flex text-white">
                    <div class="md:flex-1 flex flex-col space-y-2 items-center mb-5 md:mb-0">
                        <label for="password">Mot de passe: </label>
                        <input name="password" id="password" type="password" class="p-1 outline-none shadow-md rounded text-gray-900 focus:ring-2 focus:ring-gray-300 w-[80%]">
                    </div>

                    <div class="md:flex-1 flex flex-col space-y-2 items-center">
                        <label for="confirmation_password">Confirmation: </label>
                        <input name="confirmation_password" id="confirmation_password" type="password" class="p-1 outline-none shadow-md rounded text-gray-900 focus:ring-2 focus:ring-gray-300 w-[80%]">
                    </div>
                </div>

                <div class="flex mt-16">
                    <div class="flex-1 flex justify-center">
                        <button type="submit" class="px-3 py-2 bg-blue-600 text-white transition-all duration-300 hover:bg-blue-700 rounded-lg">Confirmer</button>
                    </div>
                </div>
            </form>
        </section>
    </section>
    
    <script>

        //Soummission des formulaires
            var formIsSubmitting = false;

            function submitFunction () {

                if (formIsSubmitting) {
                    return false
                } else {
                    formIsSubmitting = true;
                    return true;
                }
            }

            function submitFunctionFalse () {
                return false
            }
        //

        const confirmMDP = () => {
            const password = document.getElementById('password');
            const confirmation_password = document.getElementById('confirmation_password');
            if (password.value.length < 5) {
                alert('le mot de passe est trop court');
                return false;
            }
            if (password.value != confirmation_password.value) {
                alert('Les mot de passes ne correspondent pas');
                return false;
            }

            if (password.value == confirmation_password.value && password.value.length >= 5) {
                return submitFunction();
            }
        }

        
    </script>
</body>
</html>