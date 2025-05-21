// store/slices/garageSlice.ts
import { createSlice, PayloadAction } from "@reduxjs/toolkit";

interface GarageInfo {
  label: string;
  value: string;
}

const initialState: GarageInfo[] = [];

const garageSlice = createSlice({
  name: "garage",
  initialState,
  reducers: {
    setGarage: (state, action: PayloadAction<GarageInfo[]>) => {
      return action.payload;
    },
    addGarageEntry: (state, action: PayloadAction<GarageInfo>) => {
      state.push(action.payload);
    },
    clearGarage: () => {
      return [];
    },
  },
});

export const { setGarage, addGarageEntry, clearGarage } = garageSlice.actions;

export default garageSlice.reducer;

