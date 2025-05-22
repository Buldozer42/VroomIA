"use client";

import React, { useState, useEffect } from "react";
import { useDispatch } from "react-redux";
import Joyride, { Step, CallBackProps, STATUS } from "react-joyride";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";
import { ChatBubbleLeftRightIcon } from "@heroicons/react/24/outline";

import { addGarageEntry } from "../store/slices/garageSlice";
import { addAppointment } from "../store/slices/appointmentsSlice";
import { addVehicle } from "../store/slices/vehiclesSlice";
import { addOperation } from "../store/slices/operationsSlice";

const ChatComponent = () => {
  const dispatch = useDispatch();

  // Messages du chat (role: user | bot)
  const [messages, setMessages] = useState<{ role: string; text: string }[]>([]);
  // Texte saisi dans l’input
  const [input, setInput] = useState("");

  // États pour Joyride (le tutoriel)
  const [run, setRun] = useState(false);
  const [stepIndex, setStepIndex] = useState(0);

  // Définition des étapes du tutoriel
  const steps: Step[] = [
    {
      target: ".chat-screen",
      content: "Bienvenue sur VroomIA, votre assistant intelligent de gestion automobile.",
    },
    {
      target: ".chat-message",
      content: "Interagissez avec VroomIA afin d’identifier précisément les besoins de votre véhicule et d’optimiser la gestion de votre prise de rendez-vous.",
    },
  ];

  // Lance automatiquement le tutoriel au montage du composant
  useEffect(() => {
    setRun(true);
  }, []);

  // Gestion des événements du tutoriel
  const handleJoyrideCallback = (data: CallBackProps) => {
    const { status, index, type } = data;

    if (status === STATUS.FINISHED || status === STATUS.SKIPPED) {
      setRun(false);
      setStepIndex(0);
    } else if (type === "step:after" || type === "error:target_not_found") {
      setStepIndex(index + 1);
    }
  };

  // Simule l’ajout d’une opération au store Redux
  const simulateSingleOperationAdd = (title: string) => {
    const operations = [
      {
        title: "Vidange",
        subtitle: "Entretien périodique",
        description:
          "La vidange moteur permet de prolonger la durée de vie du véhicule et d’assurer son bon fonctionnement.",
        status: true,
        price: 33,
      },
      {
        title: "Contrôle technique",
        subtitle: "Obligation légale",
        description:
          "Le contrôle technique vérifie les points de sécurité et les normes environnementales du véhicule.",
        status: false,
        price: 121,
      },
      {
        title: "Révision",
        subtitle: "Maintenance constructeur",
        description:
          "Une révision complète selon les recommandations constructeur pour éviter les pannes futures.",
        status: false,
        price: 69,
      },
      {
        title: "Carrosserie",
        subtitle: "Réparations esthétiques",
        description:
          "Réparation ou remplacement d’éléments abîmés ou rayés sur votre carrosserie.",
        status: false,
        price: 400,
      },
      {
        title: "Diagnostic moteur",
        subtitle: "Recherche de panne",
        description:
          "Analyse électronique complète du moteur pour détecter les anomalies ou messages d’erreur.",
        status: false,
        price: 129,
      },
    ];

    const found = operations.find(
      (op) => op.title.toLowerCase() === title.toLowerCase()
    );

    if (found) {
      dispatch(addOperation(found));
      setMessages((prev) => [
        ...prev,
        { role: "bot", text: `Opération « ${found.title} » ajoutée ! ✅` },
      ]);
    } else {
      setMessages((prev) => [
        ...prev,
        { role: "bot", text: `Aucune opération trouvée pour « ${title} ». ❌` },
      ]);
    }
  };

  // Simule l’ajout d’un rendez-vous
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

  // Simule l’ajout d’un garage
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

  // Simule l’ajout d’un véhicule
  const simulateVehicleAdd = () => {
    dispatch(
      addVehicle({
        immatriculation: "AB-123-CD",
        marque: "Peugeot",
        model: "208",
        year: 2022,
        vin: "VF3XXXXXXXXXXXXXX",
        mileage: 15000,
        lastTechnicalInspectionDate: new Date("2024-05-10"),
      })
    );
  };

  // Gestion de l’envoi du message utilisateur
  const handleSend = async () => {
    if (!input.trim()) return;

    // Ajout du message utilisateur dans la liste
    const newMessages = [...messages, { role: "user", text: input }];
    setMessages(newMessages);
    setInput("");

    const lowerInput = input.toLowerCase();
    const keywords = [
      "vidange",
      "contrôle technique",
      "révision",
      "carrosserie",
      "diagnostic moteur",
    ];

    // Si le message contient une opération connue
    const matched = keywords.find((word) => lowerInput.includes(word));

    if (matched) {
      simulateSingleOperationAdd(matched);
      return;
    } 
    // Sinon selon le mot clé, simuler l’ajout correspondant
    else if (lowerInput.includes("garage")) {
      simulateGarageAdd();
      setMessages((prev) => [...prev, { role: "bot", text: "Garage ajouté ! ✅" }]);
    } else if (lowerInput.includes("rendez-vous")) {
      simulateAppointmentAdd();
      setMessages((prev) => [...prev, { role: "bot", text: "Rendez-vous ajouté ! 📅✅" }]);
    } else if (lowerInput.includes("véhicule")) {
      simulateVehicleAdd();
      setMessages((prev) => [...prev, { role: "bot", text: "Véhicule ajouté ! 🚗✅" }]);
    }
  };

  return (
    <>
      <Joyride
        steps={steps}
        run={run}
        stepIndex={stepIndex}
        continuous
        showSkipButton
        showProgress
        callback={handleJoyrideCallback}
        styles={{
          options: {
            zIndex: 10000,
            primaryColor: "#1d4ed8",
          },
        }}
      />

      <main className="h-screen w-full bg-base-200 p-2 flex justify-center items-center">
        <div className="w-full h-full bg-base-100 rounded-box shadow p-4 flex flex-col p-2">
          {/* Titre */}
          <div className="flex flex-row chat-screen">
            <h1 className="text-2xl font-bold text-left font-racing mt-auto">
              VroomIA
            </h1>
            <ChatBubbleLeftRightIcon className="w-6 mt-auto mb-auto ml-2" />
          </div>
          {/* Zone des messages */}
          <div className="flex-1 overflow-y-auto space-y-2 chat-messages">
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
          <p className="bg-gray-100 p-4 rounded-3xl text-xs text-black-200 mb-2 opacity-80">
            Veuillez-nous renseigner votre problème : bruits moteurs, voyants,
            quel types de prestations vous voulez : vidange, contrôle technique
            etc
          </p>
          <div className="relative w-full chat-message">
            <input
              type="text"
              className="input input-bordered w-full pr-10 outline-none focus:outline-none border-gray-200 chat-input"
              placeholder="Écris ton message..."
              value={input}
              onChange={(e) => setInput(e.target.value)}
              onKeyDown={(e) => e.key === "Enter" && handleSend()}
            />
            <button
              onClick={handleSend}
              className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary send-button"
              disabled={!input.trim()}
              aria-label="Envoyer le message"
            >
              <PaperAirplaneIcon className="w-5 h-5 rotate-45" />
            </button>
          </div>
        </div>
      </main>
    </>
  );
};

export default ChatComponent;
