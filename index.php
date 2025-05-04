<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <title>EasyStock - Inscription</title>
  </head>
  <body class="bg-gray-50 text-gray-800 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md px-6">
      <h1 class="text-3xl font-medium mb-10 text-gray-700">Créer un compte</h1>
      <form action="index.php" class="space-y-6">
        <div>
          <label for="prenom" class="text-sm text-gray-600">Prénom</label>
          <input type="text" name="prenom" id="prenom" placeholder="John"
                 class="w-full bg-transparent border-b border-gray-300 focus:outline-none focus:border-violet-400 text-base py-1 placeholder-gray-400" />
        </div>
        <div>
          <label for="nom" class="text-sm text-gray-600">Nom</label>
          <input type="text" name="nom" id="nom" placeholder="Doe"
                 class="w-full bg-transparent border-b border-gray-300 focus:outline-none focus:border-violet-400 text-base py-1 placeholder-gray-400" />
        </div>
        <div>
          <label for="email" class="text-sm text-gray-600">Email</label>
          <input type="email" name="email" id="email" placeholder="exemple@mail.com"
                 class="w-full bg-transparent border-b border-gray-300 focus:outline-none focus:border-violet-400 text-base py-1 placeholder-gray-400" />
        </div>
        <div>
          <label for="tel" class="text-sm text-gray-600">Téléphone</label>
          <input type="text" name="tel" id="tel" placeholder="77 070 77 00"
                 class="w-full bg-transparent border-b border-gray-300 focus:outline-none focus:border-violet-400 text-base py-1 placeholder-gray-400" />
        </div>
        <div>
          <label for="password" class="text-sm text-gray-600">Mot de passe</label>
          <input type="password" name="password" id="password" placeholder="••••••••"
                 class="w-full bg-transparent border-b border-gray-300 focus:outline-none focus:border-violet-400 text-base py-1 placeholder-gray-400" />
        </div>
        <button type="submit"
                class="w-full mt-8 py-2 text-center bg-violet-500 text-white text-sm rounded-md hover:bg-violet-600 transition">
          Créer un compte
        </button>
      </form>
    </div>
  </body>
</html>
