"use client";
import React from "react";
import { useState } from "react";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";

const ChatComponent = () => {
  const [messages, setMessages] = useState<{ role: string; text: string }[]>(
    []
  );
  const [input, setInput] = useState("");

  const handleSend = () => {
    if (!input.trim()) return;

    // Ajoute le message utilisateur
    const newMessages = [...messages, { role: "user", text: input }];

    // Simule une réponse (à remplacer par un appel à une vraie API)
    newMessages.push({
      role: "bot",
      text: `Tu as dit : "${input}"`,
    });

    setMessages(newMessages);
    setInput("");
  };

  return (
    <main className="h-screen bg-base-200 p-2 flex justify-center items-center">
      <div className="w-full h-full max-w-xl bg-base-100 rounded-box shadow p-4 flex flex-col p-2">
        {/* Titre */}
        <h1 className="text-2xl font-bold text-left mb-4 font-racing">
          VroomIA
        </h1>

        {/* Zone des messages */}
        <div className="flex-1 overflow-y-auto space-y-2">
          {messages.map((msg, idx) => (
            <div
              key={idx}
              className={`flex ${
                msg.role === "user" ? "justify-end" : "justify-start"
              }`}
            >
              <div
                className={`rounded-3xl px-4 py-2 max-w-xs shadow ${
                  msg.role === "user"
                    ? "bg-white-500 text-black"
                    : "bg-grey-800 text-black"
                }`}
              >
                {msg.text}
              </div>
            </div>
          ))}
        </div>

        <div className="relative w-full">
          <input
            type="text"
            className="input input-bordered w-full pr-10"
            placeholder="Écris ton message..."
            value={input}
            onChange={(e) => setInput(e.target.value)}
            onKeyDown={(e) => e.key === "Enter" && handleSend()}
          />

          <button
            onClick={handleSend}
            className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary"
            disabled={!input.trim()}
            aria-label="Envoyer le message"
          >
            <PaperAirplaneIcon className="w-5 h-5 rotate-45" />
          </button>
        </div>
      </div>
    </main>
  );
};

export default ChatComponent;
