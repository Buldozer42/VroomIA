import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export type Operation = {
  title: string;
  subtitle: string;
  description: string;
  status: boolean;
  price: number;
};

// État initial vide
const initialState: Operation[] = [];

/* Exemple d'état initial complet :
const initialState: Operation[] = [
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
*/

const operationsSlice = createSlice({
  name: "operations",
  initialState,
  reducers: {
    toggleStatus: (state, action: PayloadAction<string>) => {
      const index = state.findIndex((op) => op.title === action.payload);
      if (index !== -1) {
        state[index].status = !state[index].status;
      }
    },
    addOperation: (state, action: PayloadAction<Operation>) => {
      const exists = state.some(op => op.title === action.payload.title);
      if (!exists) {
        state.push(action.payload);
      }
    },
    setOperations: (state, action: PayloadAction<Operation[]>) => {
      return action.payload;
    },
    updateOperation: (
      state,
      action: PayloadAction<{ index: number; data: Operation }>
    ) => {
      state[action.payload.index] = action.payload.data;
    },
  },
});

export const { toggleStatus, setOperations, updateOperation, addOperation } = operationsSlice.actions;
export default operationsSlice.reducer;
