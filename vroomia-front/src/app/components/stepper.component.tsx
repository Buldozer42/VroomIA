"use client";
import React, { useState } from "react";
import operations from "../data/operationsData";
import vehicleData from "../data/vehiclesData";
import garageInfo from "../data/garageData";
import appointments from "../data/appointmentsData";

type Operation = {
  icon: React.FC<React.SVGProps<SVGSVGElement>>;
  title: string;
  subtitle: string;
  description: string;
  status: boolean;
  price: number;
};


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



  // État modal
  const [selectedOp, setSelectedOp] = useState<Operation | null>(null);
  const [ops, setOps] = useState(operations);
  const handleConfirm = () => {
    if (selectedOp) {
      // Passe le statut de l'opération à true (prise en charge)
      setOps((prev) =>
        prev.map((op) =>
          op.title === selectedOp.title ? { ...op, status: true } : op
        )
      );
      setSelectedOp(null);
    }
  };

  const handleRemove = () => {
    if (selectedOp) {
      // Passe le statut de l'opération à false (non prise en charge)
      setOps((prev) =>
        prev.map((op) =>
          op.title === selectedOp.title ? { ...op, status: false } : op
        )
      );
      setSelectedOp(null);
    }
  };

  return (
    <>
      <section className="min-h-screen bg-base-200 sm:p-2 flex justify-center items-start overflow-y-auto">
        <div className="w-full bg-base-100 rounded-box shadow p-0 flex flex-col">
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
                  <div className="w-full max-w-xs break-words">
                    {item.label === "Site web" ? (
                      <a
                        href={item.value}
                        className="text-blue-600 underline break-words whitespace-pre-wrap overflow-hidden overflow-ellipsis"
                        style={{ wordBreak: "break-word", overflowWrap: "break-word", wordWrap: "break-word" }}
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                     
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
                  {ops.map((op, index) => {
                    const Icon = op.icon;
                    return (
                      <li
                        key={index}
                        className={`list-row p-4 flex flex-col sm:flex-row gap-2 rounded
                      ${
                        op.status
                          ? "cursor-pointer hover:bg-base-200"
                          : "opacity-40 cursor-pointer"
                      }`}
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

              <div className="mt-4 flex justify-end gap-2">
              <p className="text-center ml-auto mt-auto mb-auto ">Price : {selectedOp.price} €</p>
                {selectedOp.status ? (
                  <button className="btn btn-error" onClick={handleRemove}>
                    Supprimer
                  </button>
                ) : (
                  <button className="btn btn-success" onClick={handleConfirm}>
                    Confirmer
                  </button>
                )}
              </div>
            </>
          )}
        </div>
      </div>
    </>
  );
};

export default StepperComponent;
