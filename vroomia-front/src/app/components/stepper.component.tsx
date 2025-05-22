"use client";
import React, { useState, useEffect } from "react";
import { useSelector, useDispatch } from "react-redux";
import {
  IdentificationIcon,
  WrenchScrewdriverIcon,
  ShoppingBagIcon,
  CalendarDaysIcon,
} from "@heroicons/react/24/outline";
import Joyride, { Step, CallBackProps, STATUS } from "react-joyride";
import { RootState } from "../store/store";
import { Operation, toggleStatus } from "../store/slices/operationsSlice";
import NotificationDot from "./notificationsDot.component";

const StepperComponent = () => {
  const dispatch = useDispatch();

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

  // Redux selectors
  const vehicle = useSelector((state: RootState) => state.vehicules);
  const garage = useSelector((state: RootState) => state.garage);
  const appointment = useSelector((state: RootState) => state.appointment);
  const operations = useSelector((state: RootState) => state.operations);

  // Modal state
  const [selectedOp, setSelectedOp] = useState<Operation | null>(null);
  const [openSection, setOpenSection] = useState<string | null>("vehicule");

  // Joyride state
  const [run, setRun] = useState(false);
  const [stepIndex, setStepIndex] = useState(0);

  // Tutoriel étapes
  const steps: Step[] = [
    {
      target: ".stepper-vehicule-garage",
      content:
        "Fournissez-nous les informations sur votre véhicule préféré et un concessionnaire proche ou préféré.",
    },
    {
      target: ".stepper-panier",
      content:
        "Puis, selon vos exigences, nous vous conseillerons des prestations à effectuer auprès de ce concessionnaire.",
    },
    {
      target: ".stepper-rendezvous",
      content: "Lorsque vous serez d'accord sur ces informations...",
    },
    {
      target: ".confirm-button",
      content:
        "Vous pourrez confirmer votre rendez-vous et être notifié en temps et en heure.",
    },
  ];

  useEffect(() => {
    setRun(true);
  }, []);

  const handleJoyrideCallback = (data: CallBackProps) => {
    const { status, index, type } = data;

    if (index + 1 === 3) {
      setOpenSection("rendezvous");
    }

    if (status === STATUS.FINISHED || status === STATUS.SKIPPED) {
      setRun(false);
      setStepIndex(0);
    } else if (type === "step:after" || type === "error:target_not_found") {
      setStepIndex(index + 1);
    }
  };

  const handleToggleAccordeon = (section: string) => {
    setOpenSection((prev) => (prev === section ? null : section));
  };

  const handleToggle = () => {
    if (selectedOp) {
      dispatch(toggleStatus(selectedOp.title));
      setSelectedOp(null);
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

      <section className="min-h-screen bg-base-200 sm:p-2 flex justify-center items-start overflow-y-auto">
        <div className="w-full bg-base-100 rounded-box shadow p-0 flex flex-col">

          {/* VEHICULE + GARAGE - Step 1 */}
          <div className="stepper-vehicule-garage">
            {/* VEHICULE */}
            <div className="collapse collapse-plus border border-base-300">
              <input
                type="radio"
                name="accordion-stepper"
                checked={openSection === "vehicule"}
                onChange={() => handleToggleAccordeon("vehicule")}
              />
              <div className="collapse-title font-semibold flex flex-row">
                <IdentificationIcon className="w-6" />
                <p className="pl-2 mt-auto mb-auto flex items-center relative">
                  IDENTIFICATION DU VÉHICULE
                  {vehicle.length > 0 && openSection !== "vehicule" && (
                    <div className="absolute -left-5 bottom-0">
                      <NotificationDot />
                    </div>
                  )}
                </p>
              </div>
              <div className="collapse-content text-sm space-y-1">
                {vehicle.map((item, index) => (
                  <div key={index} className="space-y-1">
                    {[
                      { label: "Immatriculation", value: item.immatriculation },
                      { label: "Marque", value: item.marque },
                      { label: "Modèle", value: item.model },
                      { label: "Année", value: item.year },
                      { label: "VIN", value: item.vin },
                      { label: "Kilométrage", value: `${item.mileage} km` },
                      {
                        label: "Dernière inspection",
                        value: item.lastTechnicalInspectionDate.toLocaleDateString(
                          "fr-FR",
                          {
                            day: "2-digit",
                            month: "long",
                            year: "numeric",
                          }
                        ),
                      },
                    ].map(({ label, value }, i) => (
                      <div key={i} className="flex gap-2">
                        <p className="font-medium w-40">{label}</p>
                        <span>:</span>
                        <p>{value}</p>
                      </div>
                    ))}
                  </div>
                ))}
              </div>

            </div>

            {/* GARAGE */}
            <div className="collapse collapse-plus border border-base-300">
              <input
                type="radio"
                name="accordion-stepper"
                checked={openSection === "garage"}
                onChange={() => handleToggleAccordeon("garage")}
              />
              <div className="collapse-title font-semibold flex flex-row">
                <WrenchScrewdriverIcon className="w-6" />
                <p className="pl-2 mt-auto mb-auto flex items-center relative">
                  VOTRE CONCESSIONNAIRE/RÉPARATEUR AGRÉÉ
                  {garage.length > 0 && openSection !== "garage" && (
                    <div className="absolute -left-5 bottom-0">
                      <NotificationDot />
                    </div>
                  )}
                </p>
              </div>
              <div className="collapse-content text-sm space-y-1">
                {garage.map((item, index) => (
                  <div key={index} className="flex gap-2 items-start mb-1">
                    <p className="w-40 font-medium">{item.label}</p>
                    <span>:</span>
                    <div className="w-full max-w-xs break-words">
                      {item.label === "Site web" ? (
                        <a
                          href={item.value}
                          className="text-blue-600 underline"
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
          </div>

          {/* PANIER - Step 2 */}
          <div className="stepper-panier">
            <div className="collapse collapse-plus border border-base-300">
              <input
                type="radio"
                name="accordion-stepper"
                checked={openSection === "panier"}
                onChange={() => handleToggleAccordeon("panier")}
              />
              <div className="collapse-title font-semibold flex flex-row">
                <ShoppingBagIcon className="w-6 relative" />
                <p className="pl-2 mt-auto mb-auto flex items-center relative">
                  VOTRE PANIER
                  {operations.some((op) => op.status) && openSection !== "panier" && (
                    <div className="absolute -left-5 bottom-0">
                      <NotificationDot />
                    </div>
                  )}
                </p>
              </div>
              <div className="collapse-content text-sm">
                <div className="max-h-96 overflow-y-auto">
                  <ul className="list bg-base-100 rounded-box shadow-md">
                    {operations.map((op, index) => (
                      <li
                        key={index}
                        className={`list-row p-4 flex flex-col sm:flex-row gap-2 rounded cursor-pointer ${
                          op.status ? "hover:bg-base-200" : "opacity-40"
                        }`}
                        onClick={() => setSelectedOp(op)}
                      >
                        <div>
                          <div className="font-medium">{op.title}</div>
                          <div className="text-xs uppercase font-semibold opacity-60">
                            {op.subtitle}
                          </div>
                          <p className="text-xs mt-1">{op.description}</p>
                        </div>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </div>
          </div>

          {/* RENDEZ-VOUS - Step 3 and 4 */}
          <div className="stepper-rendezvous">
            <div className="collapse collapse-plus border border-base-300">
              <input
                type="radio"
                name="accordion-stepper"
                checked={openSection === "rendezvous"}
                onChange={() => handleToggleAccordeon("rendezvous")}
              />
              <div className="collapse-title font-semibold flex flex-row">
                <CalendarDaysIcon className="w-6" />
                <p className="pl-2 mt-auto mb-auto flex items-center relative">
                  VOTRE RENDEZ-VOUS
                  {appointment.length > 0 && openSection !== "rendezvous" && (
                    <div className="absolute -left-5 bottom-0">
                      <NotificationDot />
                    </div>
                  )}
                </p>
              </div>
              <div className="collapse-content text-sm">
                <div className="card w-full bg-base-100 shadow">
                  <div className="card-body">
                    {appointment.map((a, index) => (
                      <ul key={index} className="space-y-2 mt-4">
                        <li className="flex items-center">
                          <CheckIcon />
                          <span>Opération(s) : {a.operations.join(", ")}</span>
                        </li>
                        <li className="flex items-center">
                          <CheckIcon />
                          <span>
                            Date de début :{" "}
                            {a.startDate.toLocaleDateString("fr-FR", {
                              day: "2-digit",
                              month: "long",
                              year: "numeric",
                            })}
                          </span>
                        </li>
                        <li className="flex items-center">
                          <CheckIcon />
                          <span>
                            Date de fin :{" "}
                            {a.endDate.toLocaleDateString("fr-FR", {
                              day: "2-digit",
                              month: "long",
                              year: "numeric",
                            })}
                          </span>
                        </li>
                        <li className="flex items-center">
                          <CheckIcon />
                          <span>Commentaires : {a.comments}</span>
                        </li>
                        <li className="flex items-center">
                          <CheckIcon />
                          <span className="font-bold">Prix : {a.price} €</span>
                        </li>
                      </ul>
                    ))}
                    <div className="mt-6">
                      <button className="btn btn-success btn-block confirm-button">
                        Confirmer le rendez-vous
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* MODAL */}
        {selectedOp && (
          <div className="modal modal-open">
            <div className="modal-box relative">
              <button
                className="btn btn-sm btn-circle absolute right-2 top-2"
                onClick={() => setSelectedOp(null)}
              >
                ✕
              </button>
              <h3 className="text-lg font-bold">{selectedOp.title}</h3>
              <p className="uppercase font-semibold text-xs opacity-60 mb-2">
                {selectedOp.subtitle}
              </p>
              <p>{selectedOp.description}</p>
              <div className="mt-4 flex justify-end gap-2">
                <p className="text-center ml-auto mt-auto mb-auto">
                  Prix : {selectedOp.price} €
                </p>
                <button
                  className={`btn ${
                    selectedOp.status ? "btn-error" : "btn-success"
                  }`}
                  onClick={handleToggle}
                >
                  {selectedOp.status ? "Supprimer" : "Confirmer"}
                </button>
              </div>
            </div>
          </div>
        )}
      </section>
    </>
  );
};

export default StepperComponent;
