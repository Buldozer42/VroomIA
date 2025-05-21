import { createSlice, PayloadAction } from "@reduxjs/toolkit";

export type GarageField = {
  label: string;
  value: string;
};

const initialState: GarageField[] = [
  {
    label: "Nom",
    value: "Volkswagen Lyon Sud - Groupe Central Autos",
  },
  {
    label: "Adresse",
    value: "51 Bd Lucien Sampaix, 69190 Saint-Fons",
  },
  {
    label: "Téléphone",
    value: "04 72 28 96 96",
  },
];

const garageSlice = createSlice({
  name: "garage",
  initialState,
  reducers: {
    setGarageInfo: (state, action: PayloadAction<GarageField[]>) => {
      return action.payload;
    },
    updateGarageField: (
      state,
      action: PayloadAction<{ index: number; data: GarageField }>
    ) => {
      state[action.payload.index] = action.payload.data;
    },
    addGarageField: (state, action: PayloadAction<GarageField>) => {
      state.push(action.payload);
    },
    removeGarageField: (state, action: PayloadAction<number>) => {
      state.splice(action.payload, 1);
    },
  },
});

export const {
  setGarageInfo,
  updateGarageField,
  addGarageField,
  removeGarageField,
} = garageSlice.actions;

export default garageSlice.reducer;
