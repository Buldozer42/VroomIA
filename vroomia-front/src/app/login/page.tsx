"use client";
import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { ChatBubbleLeftRightIcon } from "@heroicons/react/24/outline";

export default function LoginPage() {
  const router = useRouter();
  const [isLogin, setIsLogin] = useState(true);
  const [title, setTitle] = useState("M");

  const [firstname, setFirstname] = useState("");
  const [lastname, setLastname] = useState("");
  const [phone, setPhone] = useState("");
  const [company, setCompany] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    const formData = {
      email,
      password,
      ...(isLogin
        ? {}
        : {
            firstname,
            lastname,
            phone,
            ...(title === "société" ? { company } : {}),
          }),
    };

    const url = isLogin
      ? "http://localhost:8000/api/login"
      : "http://localhost:8000/api/register";

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });

      const result = await response.json();

      if (response.ok) {
        // Stocker le token JWT dans le localStorage
        if (result.token) {
          localStorage.setItem("token", result.token);
        }
      
        // Rediriger vers la page d'accueil
        router.push("/home");
      } else {
        // ❌ Erreur renvoyée par l'API
        setError(result.message || "Une erreur est survenue.");
      }
    } catch (err) {
      setError(`Erreur réseau: ${err}. Veuillez réessayer.`);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex flex-col md:flex-row">
      {/* Left side */}
      <div
        className="relative w-full md:w-5/12 bg-cover bg-center bg-no-repeat text-white px-10 py-20 flex flex-col justify-center items-start"
        style={{ backgroundImage: "url('/images/screen-vroomia.webp')" }}
      >
        <div className="absolute inset-0 bg-black opacity-50 z-0"></div>
        <div className="flex flex-row">
          <h1 className="text-5xl font-extrabold italic m-auto z-100">VroomIA</h1>
          <ChatBubbleLeftRightIcon className="w-15 m-auto z-100" />
        </div>
        <p className="text-2xl leading-relaxed max-w-md z-100">
          La première IA pour simplifier <br />
          votre prise de rendez-vous <br />
          automobile — rapide, intelligente <br />
          et sans tracas.
        </p>
      </div>

      {/* Right side */}
      <div className="w-full md:w-7/12 flex items-center justify-center px-6 py-12 bg-white">
        <div className="w-full max-w-md space-y-6">
          <h2 className="text-3xl font-bold italic text-gray-800 text-center">
            {isLogin ? "Se connecter" : "Créer un compte"}
          </h2>

          {/* ✅ Message d'erreur */}
          {error && (
            <div className="text-red-600 text-sm text-center">{error}</div>
          )}

          <form className="space-y-5" onSubmit={handleSubmit}>
            {!isLogin && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                  <select
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                  >
                    <option value="M">M</option>
                    <option value="Mme">Mme</option>
                    <option value="société">Société</option>
                  </select>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                  <input
                    type="text"
                    value={firstname}
                    onChange={(e) => setFirstname(e.target.value)}
                    placeholder="Jean"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                  <input
                    type="text"
                    value={lastname}
                    onChange={(e) => setLastname(e.target.value)}
                    placeholder="Dupont"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone</label>
                  <input
                    type="tel"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    placeholder="06 12 34 56 78"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                  />
                </div>

                {title === "société" && (
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">Nom de l&apos;entreprise</label>
                    <input
                      type="text"
                      value={company}
                      onChange={(e) => setCompany(e.target.value)}
                      placeholder="Nom de la société"
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                    />
                  </div>
                )}
              </>
            )}

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Adresse mail</label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="votre@email.com"
                className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="********"
                className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
              />
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 disabled:opacity-50"
            >
              {loading
                ? isLogin
                  ? "Connexion en cours..."
                  : "Création du compte..."
                : isLogin
                ? "Se connecter"
                : "Créer un compte"}
            </button>
          </form>

          <p className="text-center text-sm text-gray-600">
            {isLogin ? "Pas de compte ?" : "Vous avez déjà un compte ?"}{" "}
            <button
              type="button"
              onClick={() => setIsLogin(!isLogin)}
              className="text-indigo-600 hover:underline"
            >
              {isLogin ? "Inscrivez-vous" : "Connectez-vous"}
            </button>
          </p>
        </div>
      </div>
    </div>
  );
}
