<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiReportGenerator
{
    /**
     * Génère un compte rendu structuré via l'API Groq (Llama 3).
     */
    public function generate(string $notes, string $contextType, string $clientName): string
    {
        // Si tu n'as pas encore mis la clé dans le .env, mets ceci à true pour tester
        $demoMode = false;

        // --- MODE DEMO (Fallback) ---
        if ($demoMode) {
            sleep(1);
            return "--- MODE DÉMO (Configurez GROQ_API_KEY) ---\n\n" .
                   "**Observation :** $clientName a participé à l'activité.\n" .
                   "**Analyse :** Les notes \"$notes\" suggèrent une séance intéressante.\n" .
                   "**Conclusion :** À poursuivre.";
        }

        // --- PROMPT SYSTÈME (Le cerveau de l'expert) ---
        $systemPrompt = <<<EOT
Tu es un expert en éducation spécialisée et travail social avec 15 ans d'expérience.
Ta mission est de rédiger des comptes rendus d'intervention professionnels, précis et bienveillants.

Règles de rédaction :
1. **Ton** : Clinique, objectif, professionnel (ni robotique, ni trop familier). Utilise le "Nous" ou la voix passive.
2. **Vocabulaire** : Utilise des termes métier adaptés (ex: étayage, contenance, dynamique relationnelle, interactions, autonomie).
3. **Structure** :
   - **Contexte** : Accueil de l'usager, climat général.
   - **Observations & Déroulé** : Description factuelle des actions et réactions, basée sur les notes.
   - **Analyse** : Interprétation éducative courte (progrès, difficultés, leviers).
   - **Perspectives** : Une phrase sur la suite.
4. **Fidélité** : Ne pas inventer de faits. Si les notes sont maigres, fais un rapport concis mais bien formulé.

L'utilisateur te fournira : Le nom du bénéficiaire, le type d'intervention et ses notes brutes.
EOT;

        $userPrompt = "Bénéficiaire : $clientName\n" .
                      "Type d'intervention : $contextType\n" .
                      "Notes brutes : \"$notes\"";

        try {
            // Appel API GROQ
            $response = Http::withToken(config('services.groq.key', env('GROQ_API_KEY')))
                ->timeout(30) // Groq est rapide, 30s suffit largement
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                    'temperature' => 0.6, // Assez bas pour éviter les hallucinations
                    'max_tokens' => 1024,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            // Gestion d'erreur explicite
            $errorMsg = $response->json('error.message');
            return "Erreur Groq API ($errorMsg). Veuillez réessayer ou compléter manuellement.";

        } catch (\Exception $e) {
            return "Erreur de connexion : " . $e->getMessage();
        }
    }
}