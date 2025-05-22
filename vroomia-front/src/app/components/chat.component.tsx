"use client";
import React, { useState, useEffect, useRef } from "react";
import { useDispatch, useSelector } from "react-redux";
import Joyride, { Step, CallBackProps, STATUS } from "react-joyride";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";
import { ChatBubbleLeftRightIcon } from "@heroicons/react/24/outline";
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
import { addVehicle } from "../store/slices/vehiclesSlice";
import { addOperation } from "../store/slices/operationsSlice";
import { closeDrawer, DrawerType, openDrawer } from "../store/slices/uiSlice"; 
import { RootState } from "../store/store";

const ChatComponent = () => {
  const dispatch = useDispatch();

  const [messages, setMessages] = useState<{ role: string; text: string }[]>(
    []
  );
  const [input, setInput] = useState("");
  const hasFetchedRef = useRef(false);
  const [run, setRun] = useState(false);
  const [stepIndex, setStepIndex] = useState(0);
  const messagesEndRef = useRef<HTMLDivElement | null>(null);
  const { drawerOpen, selectedTab } = useSelector((state: RootState) => state.ui);
  const [isConfirmStep, setIsConfirmStep] = useState(false);
  
  const steps: Step[] = [
    {
      target: ".chat-screen",
      content:
        "Bienvenue sur VroomIA, votre assistant intelligent de prise de rendez-vous pour votre véhicule automobile.",
    },
    {
      target: ".chat-message",
      content:
        "Interagissez avec VroomIA afin d’identifier précisément les besoins de votre véhicule et d’optimiser la gestion de votre prise de rendez-vous.",
    },
  ];

  useEffect(() => {
    setRun(true);
  }, []);

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  const handleJoyrideCallback = (data: CallBackProps) => {
    const { status, index, type } = data;

    if (status === STATUS.FINISHED || status === STATUS.SKIPPED) {
      setRun(false);
      setStepIndex(0);
    } else if (type === "step:after" || type === "error:target_not_found") {
      setStepIndex(index + 1);
    }
  };

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
      if (hasFetchedRef.current) return;

      hasFetchedRef.current = true;
      try {
        const response = await fetch("http://localhost:8000/api/gemini/conversation/new", {
          method: "POST",
          headers: {
            "Accept": "application/json",
            "Content-Type": "application/json",
          },
          credentials: 'include',
          body: JSON.stringify({ personId: 2 }),
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

  const simulateGarageAdd = () => {
    dispatch(
      addGarageEntry({ label: "Nom du garage", value: "Garage du Centre" })
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

 const handleSend = async (text?: string) => {
  const message = text ?? input

  const newMessages = [...messages, { role: "user", text: message }];
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
        messageContent: message,
        conversationId: conversationId,
      }),
    });

    const data = await response.json();

    if (data.error) {
      console.error("Erreur :", data.error);
      setMessages((prev) => [
        ...prev,
        { role: "bot", text: "❌ Une erreur est survenue lors de la réponse du serveur." },
      ]);
    } else {
      console.log("Réponse du backend:", data.geminiResponse);
      setMessages((prev) => [
        ...prev,
        { role: "bot", text: data.geminiResponse },
      ]);
      if (data.geminiResponse.includes("Étape de confirmation")) {
        setIsConfirmStep(true);
      } else {
        setIsConfirmStep(false);
      }
    }
  } catch (error) {
    console.error("Erreur lors de l'envoi au backend:", error);
    setMessages((prev) => [
      ...prev,
      { role: "bot", text: "❌ Impossible de contacter le serveur. Vérifiez la connexion ou l'URL." },
    ]);
  }

  // 🔁 Logique locale (simulations)
  const lowerInput = message.toLowerCase();
  const keywords = [
    "vidange",
    "contrôle technique",
    "révision",
    "carrosserie",
    "diagnostic moteur",
  ];

  const matched = keywords.find((word) => lowerInput.includes(word));

  if (matched) {
    simulateSingleOperationAdd(matched);
    return;
  } else if (lowerInput.includes("garage")) {
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
  } else if (lowerInput.includes("véhicule")) {
    simulateVehicleAdd();
    setMessages((prev) => [
      ...prev,
      { role: "bot", text: "Véhicule ajouté ! 🚗✅" },
    ]);
  }
};


  // ===➡️ Clics sur les cartes : dispatch vers Redux
  const handleCardClick = (tab: DrawerType) => {
    console.log(tab)
    if (drawerOpen && selectedTab === tab) {
      dispatch(closeDrawer());
      setTimeout(() => {
        dispatch(openDrawer(tab));
      }, 100);
    } else {
      dispatch(openDrawer(tab));
    }
  };

  const LineButtonConfirm = () => {
    
    const onConfirm = async () => {
      setIsConfirmStep(false);
      try {
        const response = await fetch("http://localhost:8000/api/gemini/message/confirm", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
          },
          body: JSON.stringify({
            conversationId: conversationId,
          }),
        });

        const data = await response.json();

        if (data.error) {
          console.error("Erreur :", data.error);
          setMessages((prev) => [
            ...prev,
            { role: "bot", text: "❌ Une erreur est survenue lors de la réponse du serveur." },
          ]);
        }
        console.log(data);
      } catch (error) {
        console.log("Erreur lors de l'envoi au backend:", error);
        setMessages((prev) => [
          ...prev,
          { role: "bot", text: "❌ Impossible de contacter le serveur. Vérifiez la connexion ou l'URL." },
        ]);
      }
    }

    return (
      <div style={{display: "flex", justifyContent:"flex-end", flexDirection: "row", gap: 10}}>
        <button className="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6
          rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center 
          space-x-2 transform hover:scale-105 focus:outline-none focus:ring-4 
          focus:ring-green-300 focus:ring-opacity-75"
          onClick={async () => {await onConfirm(); handleSend("Oui"); }}
        >
          <span>✅</span>
          <span>Oui</span>
        </button>
        <button className="bg-red-500 hover:bg-red-600  text-white font-bold py-3 px-6 
          rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center 
          space-x-2 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-red-300 
          focus:ring-opacity-75"
          onClick={() => { handleSend("Non"); }}
        >
          <span>❌</span>
          <span>Non</span>
        </button>
      </div>
    )
  }


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
          <div className="flex flex-row chat-screen">
            <h1 className="text-2xl font-bold text-left font-racing mt-auto">
              VroomIA
            </h1>
            <ChatBubbleLeftRightIcon className="w-6 mt-auto mb-auto ml-2" />
          </div>

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
                      ? "bg-gray-200 text-black"
                      : "bg-white-500 text-black"
                  }`}
                >
                  {msg.text}
                </div>
              </div>
            ))}
            <div ref={messagesEndRef} />
          </div>

          {/* 🟦 Cartes de navigation */}
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-4 mb-2">
            <div
              /* todo : add action to book reservation */
              className="card card-compact bg-base-100 shadow hover:shadow-lg cursor-pointer transition"
            >
              <div className="card-body">
                <h2 className="card-title text-sm">Prendre un rendez-vous</h2>
                <p className="text-xs">Réservez une date avec votre garage.</p>
              </div>
            </div>
            <div
              onClick={() => handleCardClick("profile")}
              className="card card-compact bg-base-100 shadow hover:shadow-lg cursor-pointer transition"
            >
              <div className="card-body">
                <h2 className="card-title text-sm">Consulter mes infos</h2>
                <p className="text-xs">
                  Voir mon profil utilisateur et mes véhicules.
                </p>
              </div>
            </div>
            <div
              onClick={() => handleCardClick("stack")}
              className="card card-compact bg-base-100 shadow hover:shadow-lg cursor-pointer transition"
            >
              <div className="card-body">
                <h2 className="card-title text-sm">Voir mes rendez-vous</h2>
                <p className="text-xs">
                  Liste des rendez-vous passés et futurs.
                </p>
              </div>
            </div>
          </div>

          <p className="bg-gray-100 p-4 rounded-3xl text-xs text-black-200 mb-2 opacity-80">
            Veuillez-nous renseigner votre problème : bruits moteurs, voyants,
            quel types de prestations vous voulez : vidange, contrôle technique,
            etc.
          </p>

            {isConfirmStep ? 
              <LineButtonConfirm/>
            :  
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
                  onClick={() => handleSend()}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary send-button"
                  disabled={!input.trim()}
                  aria-label="Envoyer le message"
                >
                  <PaperAirplaneIcon className="w-5 h-5 rotate-45" />
                </button>
              </div>
            }
        </div>
      </main>
    </>
  );
};


export default ChatComponent;