'use client';
import React, { useState } from "react";
import {
  Cog6ToothIcon,
  ShieldExclamationIcon,
  WrenchIcon,
  PaintBrushIcon,
  ExclamationTriangleIcon,
} from "@heroicons/react/24/outline";

type Operation = {
  icon: React.FC<React.SVGProps<SVGSVGElement>>;
  title: string;
  subtitle: string;
  description: string;
};

const vehicleData = [
  { label: "Immatriculation", value: "BF-678-AF" },
  { label: "Marque", value: "BMW" },
  { label: "Gamme et Modèle", value: "Série 8" },
  { label: "Motorisation", value: "200d" },
  { label: "N°CINT", value: "XXX" },
  { label: "Energie", value: "Diesel" },
  { label: "Date de mise en circulation", value: "15/06/2020" },
];

const garageInfo = [
  { label: "Nom", value: "Volkswagen Lyon Sud - Groupe Central Autos" },
  { label: "Adresse", value: "51 Bd Lucien Sampaix, 69190 Saint-Fons" },
  { label: "Téléphone", value: "04 72 28 96 96" },
  {
    label: "Site web",
    value:
      "https://www.volkswagen.fr/fr/partenaire/centralautos-saintfons.html",
  },
  {
    label: "Horaire",
    value: `lundi 08:00–12:00, 14:00–19:00
mardi 08:00–12:00, 14:00–19:00
mercredi 08:00–12:00, 14:00–19:00
jeudi 08:00–12:00, 14:00–19:00
vendredi 08:00–12:00, 14:00–19:00
samedi 09:00–12:00, 14:00–18:00
dimanche Fermé`,
  },
];

const operations: Operation[] = [
  {
    icon: WrenchIcon,
    title: "Vidange",
    subtitle: "Entretien périodique",
    description:
      "La vidange moteur permet de prolonger la durée de vie du véhicule et d’assurer son bon fonctionnement.",
  },
  {
    icon: ShieldExclamationIcon,
    title: "Contrôle technique",
    subtitle: "Obligation légale",
    description:
      "Le contrôle technique vérifie les points de sécurité et les normes environnementales du véhicule.",
  },
  {
    icon: Cog6ToothIcon,
    title: "Révision",
    subtitle: "Maintenance constructeur",
    description:
      "Une révision complète selon les recommandations constructeur pour éviter les pannes futures.",
  },
  {
    icon: PaintBrushIcon,
    title: "Carrosserie",
    subtitle: "Réparations esthétiques",
    description:
      "Réparation ou remplacement d’éléments abîmés ou rayés sur votre carrosserie.",
  },
  {
    icon: ExclamationTriangleIcon,
    title: "Diagnostic moteur",
    subtitle: "Recherche de panne",
    description:
      "Analyse électronique complète du moteur pour détecter les anomalies ou messages d’erreur.",
  },
];

const StepperComponent = () => {
  const CheckIcon = () => (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      className="w-4 h-4 mr-2 inline-block text-success"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        strokeLinecap="round"
        strokeLinejoin="round"
        strokeWidth="2"
        d="M5 13l4 4L19 7"
      />
    </svg>
  );

  const appointments = [
    {
      operations: "Vidange",
      startDate: "12/06/2025",
      endDate: "17/06/2025",
      comments:
        "Veuillez-vous munir de votre carte grise et les clés du véhicule",
      price: "99$",
    },
  ];

  // État modal
  const [selectedOp, setSelectedOp] = useState<Operation | null>(null);

  return (
    <>
      <section className="min-h-screen bg-base-200 p-2 flex justify-center items-start overflow-y-auto">
        <div className="w-full max-w-xl bg-base-100 rounded-box shadow p-4 flex flex-col space-y-4">
          {/* VEHICULE */}
          <div className="collapse collapse-plus border border-base-300">
            <input type="radio" name="accordion-stepper" defaultChecked />
            <div className="collapse-title font-semibold">
              IDENTIFICATION DU VÉHICULE
            </div>
            <div className="collapse-content text-sm space-y-1">
              {vehicleData.map((item, index) => (
                <div key={index} className="flex gap-2">
                  <p className="font-medium w-40">{item.label}</p>
                  <span>:</span>
                  <p>{item.value}</p>
                </div>
              ))}
            </div>
          </div>

          {/* GARAGE */}
          <div className="collapse collapse-plus border border-base-300">
            <input type="radio" name="accordion-stepper" />
            <div className="collapse-title font-semibold">
              VOTRE CONCESSIONNAIRE/RÉPARATEUR AGRÉÉ
            </div>
            <div className="collapse-content text-sm space-y-1">
              {garageInfo.map((item, index) => (
                <div key={index} className="flex gap-2 items-start mb-1">
                  <p className="w-40 font-medium">{item.label}</p>
                  <span>:</span>
                  <div>
                    {item.label === "Site web" ? (
                      <a
                        href={item.value}
                        className="text-blue-600 underline break-all"
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                        {item.value}
                      </a>
                    ) : item.label === "Horaire" ? (
                      item.value
                        .split("\n")
                        .map((line, i) => <div key={i}>{line.trim()}</div>)
                    ) : (
                      <p>{item.value}</p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* PANIER (opérations cliquables) */}
          <div className="collapse collapse-plus border border-base-300">
            <input type="radio" name="accordion-stepper" />
            <div className="collapse-title font-semibold">VOTRE PANIER</div>
            <div className="collapse-content text-sm">
              <div className="max-h-96 overflow-y-auto">
                <ul className="list bg-base-100 rounded-box shadow-md">
                  {operations.map((op, index) => {
                    const Icon = op.icon;
                    return (
                      <li
                        key={index}
                        className="list-row p-4 flex flex-col sm:flex-row gap-2 cursor-pointer hover:bg-base-200 rounded"
                        onClick={() => setSelectedOp(op)}
                      >
                        <Icon className="w-6 h-8 text-primary" />
                        <div>
                          <div className="font-medium">{op.title}</div>
                          <div className="text-xs uppercase font-semibold opacity-60">
                            {op.subtitle}
                          </div>
                          <p className="text-xs mt-1">{op.description}</p>
                        </div>
                      </li>
                    );
                  })}
                </ul>
              </div>
            </div>
          </div>

          {/* RENDEZ-VOUS */}
          <div className="collapse collapse-plus border border-base-300">
            <input type="radio" name="accordion-stepper" />
            <div className="collapse-title font-semibold">
              VOTRE RENDEZ-VOUS
            </div>
            <div className="collapse-content text-sm">
              <div className="card w-full bg-base-100 shadow">
                <div className="card-body">
                  <span className="badge badge-xs badge-warning">
                    Most Popular
                  </span>
                  <div className="flex justify-between">
                    <h2 className="text-3xl font-bold">Premium</h2>
                    <span className="text-xl">{appointments[0].price}</span>
                  </div>
                  {appointments.map((appointment, index) => (
                    <ul key={index}>
                      <li className="flex items-center">
                        <CheckIcon />
                        <span>Opération(s) : {appointment.operations}</span>
                      </li>
                      <li className="flex items-center">
                        <CheckIcon />
                        <span>Date de début : {appointment.startDate}</span>
                      </li>
                      <li className="flex items-center">
                        <CheckIcon />
                        <span>Date de fin : {appointment.endDate}</span>
                      </li>
                      <li className="flex items-center">
                        <CheckIcon />
                        <span>Commentaires : {appointment.comments}</span>
                      </li>
                    </ul>
                  ))}
                  <div className="mt-6">
                    <button className="btn btn-success btn-block">
                      Confirmer le rendez-vous
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* MODAL DAISYUI */}
      {selectedOp && (
        <input
          type="checkbox"
          id="operation-modal"
          className="modal-toggle"
          checked
          readOnly
        />
      )}
      <div className={`modal ${selectedOp ? "modal-open" : ""}`}>
        <div className="modal-box relative">
          <label
            htmlFor="operation-modal"
            className="btn btn-sm btn-circle absolute right-2 top-2"
            onClick={() => setSelectedOp(null)}
          >
            ✕
          </label>
          {selectedOp && (
            <>
              <h3 className="text-lg font-bold flex items-center gap-2 mb-4">
                <selectedOp.icon className="w-6 h-6 text-primary" />
                {selectedOp.title}
              </h3>
              <p className="uppercase font-semibold text-xs opacity-60 mb-2">
                {selectedOp.subtitle}
              </p>
              <p>{selectedOp.description}</p>
            </>
          )}
        </div>
      </div>
    </>
  );
};

export default StepperComponent;
