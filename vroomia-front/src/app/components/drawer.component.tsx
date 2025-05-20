"use client";
import React, { useState } from "react";
import {
  EllipsisHorizontalCircleIcon,
  UserIcon,
  ArrowRightOnRectangleIcon,
  RectangleStackIcon,
  QueueListIcon,
  ArrowUturnLeftIcon,
} from "@heroicons/react/24/outline";
import StepperComponent from "./stepper.component";

const Drawer = () => {
  const [selectedTab, setSelectedTab] = useState("profile");

  const previousRdvList = [
    {
      date: "Lundi 7 Octobre 2023",
      vehicule: "BMW",
      concessionnaire: "VW Lyon Sud",
      operations: ["Vidange", "Polish"],
    },
    {
      date: "Lundi 7 Octobre 2023",
      vehicule: "BMW",
      concessionnaire: "VW Lyon Sud",
      operations: ["Vidange", "Polish"],
    },
  ];

  const nextRdvList = [
    {
      date: "Lundi 23 juin 2025",
      vehicule: "BMW",
      concessionnaire: "VW Lyon Sud",
      operations: ["Vidange", "Polish"],
    },
    {
      date: "Lundi 23 juin 2025",
      vehicule: "BMW",
      concessionnaire: "VW Lyon Sud",
      operations: ["Vidange", "Polish"],
    },
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

  const renderContent = () => {
    switch (selectedTab) {
      case "profile":
        return (
          <>
            <h2 className="text-2xl font-bold mb-4">Profil</h2>
            <br />
            <h3 className="text-xl font-bold mb-4">Votre garage préféré</h3>
            <div className="bg-base-100 p-6 rounded shadow">
              <ul className="space-y-4">
                {garageInfo.map((item, index) => (
                  <li key={index}>
                    <p className="font-semibold">{item.label} :</p>
                    {item.label === "Site web" ? (
                      <a
                        href={item.value}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-blue-500 underline"
                      >
             
                      </a>
                    ) : item.label === "Horaire" ? (
                      <pre className="whitespace-pre-wrap text-sm text-gray-700 mt-1">
                        {item.value}
                      </pre>
                    ) : (
                      <p className="text-gray-700">{item.value}</p>
                    )}
                  </li>
                ))}
              </ul>
            </div>
          </>
        );
      case "stack":
        return (
          <>
            <h2 className="text-2xl font-bold mb-4">Les rendez-vous</h2>
            <h3 className="text-xl font-bold mb-4">Vos prochain rendez-vous</h3>
            <ul className="space-y-4">
              {nextRdvList.map((rdv, index) => (
                <li key={index} className="p-4 rounded bg-base-100 shadow">
                  <p className="font-bold">{rdv.date}</p>
                  <p>Véhicule : {rdv.vehicule}</p>
                  <p>Concessionnaire : {rdv.concessionnaire}</p>
                  <p>Opérations :</p>
                  <ul className="list-disc list-inside ml-4">
                    {rdv.operations.map((op, i) => (
                      <li key={i}>{op}</li>
                    ))}
                  </ul>
                </li>
              ))}
            </ul>
            <br></br>
            <h3 className="text-xl font-bold mb-4">Vos rendez-vous passés</h3>
            <ul className="space-y-4">
              {previousRdvList.map((rdv, index) => (
                <li key={index} className="p-4 rounded bg-base-100 shadow">
                  <p className="font-bold">{rdv.date}</p>
                  <p>Véhicule : {rdv.vehicule}</p>
                  <p>Concessionnaire : {rdv.concessionnaire}</p>
                  <p>Opérations :</p>
                  <ul className="list-disc list-inside ml-4">
                    {rdv.operations.map((op, i) => (
                      <li key={i}>{op}</li>
                    ))}
                  </ul>
                </li>
              ))}
            </ul>
          </>
        );
      case "logout":
        return (
          <>
            <h2 className="text-2xl font-bold mb-4">Déconnexion</h2>
            <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
          </>
        );
      case "stepper":
        return <StepperComponent />;
      default:
        return null;
    }
  };

  const handleTabChange = (tab: string) => {
    setSelectedTab(tab);

    // Fermer le drawer après le clic
    const drawerCheckbox = document.getElementById("my-drawer") as HTMLInputElement;
    if (drawerCheckbox) drawerCheckbox.checked = false;
  };

  return (
    <div className="drawer drawer-end w-full">
      <input id="my-drawer" type="checkbox" className="drawer-toggle" />
      <div className="drawer-content">
        <label htmlFor="my-drawer" className="text-gray-700 flex">
          <EllipsisHorizontalCircleIcon className="w-8 h-8 mt-4 m-auto" />
        </label>
      </div>

      <div className="drawer-side">
        <label htmlFor="my-drawer" className="drawer-overlay"></label>
        <div className="flex h-full xs:w-full xl:w-1/2 bg-base-200 text-base-content">
          {/* Sidebar */}
          <nav className="w-15 md:w-20 bg-base-300 p-4 flex flex-col gap-4 items-center relative">
            {/* Mobile only button */}
            <button
              onClick={() => handleTabChange("stepper")}
              className={`p-2 rounded hover:bg-base-100 lg:hidden ${
                selectedTab === "stepper" ? "bg-base-100 text-blue-500" : ""
              }`}
            >
              <QueueListIcon className="w-6 h-6" />
            </button>

            <button
              onClick={() => setSelectedTab("profile")}
              className={`p-2 rounded hover:bg-base-100 ${
                selectedTab === "profile" ? "bg-base-100 text-blue-500" : ""
              }`}
            >
              <UserIcon className="w-6 h-6" />
            </button>

            <button
              onClick={() => setSelectedTab("stack")}
              className={`p-2 rounded hover:bg-base-100 ${
                selectedTab === "stack" ? "bg-base-100 text-blue-500" : ""
              }`}
            >
              <RectangleStackIcon className="w-6 h-6" />
            </button>

            {/* Return with close drawer */}
            <button
              onClick={() => handleTabChange("return")}
              className={`p-2 rounded hover:bg-base-100 ${
                selectedTab === "return" ? "bg-base-100 text-blue-500" : ""
              }`}
            >
              <ArrowUturnLeftIcon className="w-6 h-6" />
            </button>

            {/* Logout button fixed at bottom */}
            <button
              onClick={() => setSelectedTab("logout")}
              className={`absolute bottom-5 p-2 rounded hover:bg-base-100 ${
                selectedTab === "logout" ? "bg-base-100 text-blue-500" : ""
              }`}
            >
              <ArrowRightOnRectangleIcon className="w-6 h-6 text-red-500" />
            </button>
          </nav>

          {/* Drawer content */}
          <div className="flex-1 p-6 overflow-y-auto">{renderContent()}</div>
        </div>
      </div>
    </div>
  );
};

export default Drawer;
