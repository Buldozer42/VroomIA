"use client";
import React, { use } from "react";
import { useState, useEffect } from "react";
import { useDispatch } from "react-redux";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";
import { addGarageEntry } from "../store/slices/garageSlice";
import { addAppointment } from "../store/slices/appointmentsSlice";

const simulateAIResponse = (input: string) => {
  const lower = input.toLowerCase();
  if (lower.includes("frein") || lower.includes("bruit")) {
    return {
      type: "multi",
      content:
        "Plusieurs causes possibles, sélectionne ce qui semble pertinent :",
      options: [
        "Disques de frein usés",
        "Plaquettes à changer",
        "Liquide de frein bas",
        "Autre chose",
      ],
    };
  }

  if (
    lower.includes("je dois faire la vidange") ||
    lower.includes("révision")
  ) {
    return {
      type: "binaire",
      content: "Souhaitez-vous planifier une vidange maintenant ?",
    };
  }

  return {
    type: "texte",
    content: `Tu as dit : "${input}"`,
  };
};

const ChatComponent = () => {

  const simulateAppointmentAdd = () => {
    dispatch(
      addAppointment({
        operations: ["Révision", "Freinage"],
        startDate: new Date("2025-06-20"),
        endDate: new Date("2025-06-22"),
        comments: "Merci d'arriver 10 minutes en avance.",
        price: 250,
      })
    );
  };

  const [conversationId, setConversationId] = useState("");
  useEffect(() => {
    const fetchConversation = async () => {
      try {
        const response = await fetch("http://localhost:8000/api/gemini/conversation/new", {
          method: "POST",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
          },
          credentials: 'include',
          body: JSON.stringify({ personId: 26 }),
        });

        const data = await response.json();
        if (data.error) {
          console.error("Erreur du backend:", data.error);
        } else {
          console.log("Réponse du backend:", data.conversationId);
        }
        setConversationId(data.conversationId);
      } catch (error) {
        console.error("Erreur lors de la récupération des messages:", error);
      }
    };

    fetchConversation();
  }, []);

  const dispatch = useDispatch();
  const simulateGarageAdd = () => {
    dispatch(
      addGarageEntry({
        label: "Nom du garage",
        value: "Garage du Centre",
      })
    );
    dispatch(
      addGarageEntry({
        label: "Téléphone",
        value: "01 23 45 67 89",
      })
    );
    dispatch(
      addGarageEntry({
        label: "Adresse",
        value: "12 rue des Mécanos, 75000 Paris",
      })
    );
  };

  const [messages, setMessages] = useState<{ role: string; text: string }[]>(
    []
  );
  const [input, setInput] = useState("");

  const handleSend = async () => {
    if (!input.trim()) return;
  
    const newMessages = [...messages, { role: "user", text: input }];
    setMessages(newMessages);
    setInput("");
    try {
       const response = await fetch("http://localhost:8000/api/gemini/message/send", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        body: JSON.stringify({ 
            messageContent: input,
            conversationId: conversationId,
          }),
      });
      const data = await response.json();
      if (data.error) {
        console.error("Erreur :", data.error);
      } else {
        console.log("Réponse du backend:", data.geminiResponse);
      }
    } catch (error) {
      console.error("Erreur lors de l'envoi au backend:", error);
    }
  
    // 🔁 Logique locale (simulations)
    const lowerInput = input.toLowerCase();
  
    if (lowerInput.includes("garage")) {
      simulateGarageAdd();
      setMessages((prev) => [
        ...prev,
        { role: "bot", text: "Garage ajouté ! ✅" },
      ]);
    } else if (lowerInput.includes("rendez-vous")) {
      simulateAppointmentAdd();
      setMessages((prev) => [
        ...prev,
        { role: "bot", text: "Rendez-vous ajouté ! 📅✅" },
      ]);
    } else {
      const ai = simulateAIResponse(input);
  
      setMessages((prev) => [
        ...prev,
        {
          role: "bot",
          text:
            ai.type === "multi" && ai.options
              ? ai.content +
                "\n" +
                ai.options.map((opt, i) => `${i + 1}. ${opt}`).join("\n")
              : ai.content,
        },
      ]);
    }
  };
  
  
  return (
    <main className="h-screen w-full bg-base-200 p-2 flex justify-center items-center">
      <div className="w-full h-full bg-base-100 rounded-box shadow p-4 flex flex-col p-2">
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
                    : "bg-gray-200 text-black"
                }`}
              >
                {msg.text}
              </div>
            </div>
          ))}
        </div>
        <p className="bg-gray-200 p-4 rounded-3xl text-xs text-black-200 mb-2">Veuillez-nous renseigner votre problème : bruits moteurs, voyants, quel types de prestations vous voulez : vidange, contrôle technique etc</p>
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
