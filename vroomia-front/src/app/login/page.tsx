'use client';
import React, { useState } from 'react';

export default function LoginPage() {
  const [isLogin, setIsLogin] = useState(true);

  return (
    <div className="min-h-screen flex flex-col md:flex-row">
      {/* Left side */}
      <div className="w-full md:w-5/12 bg-gradient-to-br from-indigo-600 to-blue-500 text-white flex flex-col justify-center items-start px-10 py-20">
        <h1 className="text-5xl font-extrabold italic mb-6">VroomIA</h1>
        <p className="text-2xl leading-relaxed max-w-md">
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
            {isLogin ? 'Se connecter' : 'Créer un compte'}
          </h2>

          <form className="space-y-5">
            {!isLogin && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Nom complet
                  </label>
                  <input
                    type="text"
                    placeholder="John Doe"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Vous êtes ?
                  </label>
                  <select
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                  >
                    <option value="particulier">Particulier</option>
                    <option value="entreprise">Entreprise</option>
                  </select>
                </div>
              </>
            )}

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Adresse mail
              </label>
              <input
                type="email"
                placeholder="votre@email.com"
                className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Mot de passe
              </label>
              <input
                type="password"
                placeholder="********"
                className="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-400 focus:outline-none"
              />
            </div>

            <button
              type="submit"
              className="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300"
            >
              {isLogin ? 'Se connecter' : 'Créer un compte'}
            </button>
          </form>

          <p className="text-center text-sm text-gray-600">
            {isLogin ? "Pas de compte ?" : "Vous avez déjà un compte ?"}{' '}
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
